<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    /**
     * Main search page (full page load).
     * All queries use eager loading — zero N+1.
     */
    public function index(Request $request): View
    {
        $hotels = $this->buildQuery($request)->paginate(12)->withQueryString();

        return $this->renderView($hotels, $request);
    }

    /**
     * AJAX endpoint — returns HTML fragment + JSON metadata.
     * Used by live search and filter changes.
     */
    public function ajax(Request $request): JsonResponse
    {
        $hotels = $this->buildQuery($request)->paginate(12)->withQueryString();

        $html = view('frontend.partials.search-results', compact('hotels'))->render();
        $paginationHtml = view('frontend.partials.search-pagination', compact('hotels'))->render();

        return response()->json([
            'html' => $html,
            'pagination' => $paginationHtml,
            'total' => $hotels->total(),
            'currentPage' => $hotels->currentPage(),
            'lastPage' => $hotels->lastPage(),
            'hasPages' => $hotels->hasPages(),
        ]);
    }

    /**
     * Live search — lightweight JSON for autocomplete / instant results.
     * Returns only what the UI needs.
     */
    public function live(Request $request): JsonResponse
    {
        $term = $request->input('q', '');

        $hotels = Hotel::active()
            ->select('id', 'name', 'slug', 'city', 'country', 'star_rating', 'cover_image')
            ->withCount(['rooms as available_rooms_count' => function ($q) {
                $q->where('is_active', true)->where('status', 'available');
            }])
            ->search($term)
            ->take(8)
            ->get()
            ->map(fn (Hotel $hotel) => [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'slug' => $hotel->slug,
                'city' => $hotel->city,
                'country' => $hotel->country,
                'star_rating' => $hotel->star_rating,
                'cover_image_url' => $hotel->cover_image_url,
                'available_rooms' => $hotel->available_rooms_count,
                'url' => route('frontend.hotel.show', $hotel->slug),
            ]);

        return response()->json(['results' => $hotels]);
    }

    /**
     * Filter options endpoint — returns cities, price range, room types, amenities.
     * Used to dynamically populate filter dropdowns.
     * Cached for 10 minutes as this data rarely changes.
     */
    public function options(): JsonResponse
    {
        $options = $this->getCachedOptions();

        return response()->json([
            'cities' => $options['cities'],
            'min_price' => (float) ($options['priceRange']->min_price ?? 0),
            'max_price' => (float) ($options['priceRange']->max_price ?? 1000),
            'room_types' => $options['roomTypes'],
            'amenities' => $options['amenities'],
            'star_ratings' => $options['starRatings'],
        ]);
    }

    // ─── Query Builder (zero N+1) ─────────────────────────

    private function buildQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Hotel::query()
            ->active()

            // ── Eager load everything needed by hotel-card ──
            ->withCount([
                'rooms as available_rooms_count' => fn ($q) => $q->where('is_active', true)->where('status', 'available'),
            ])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()])

            // ── Subqueries for computed columns (avoids N+1) ──
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->approved()], 'rating')
            ->selectRaw('(SELECT MIN(rt.base_price) FROM room_types rt WHERE rt.hotel_id = hotels.id AND rt.is_active = 1) as min_price');

        // ── Text Search (name, city, country, address) ──
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // ── City Filter ──
        if ($city = $request->input('city')) {
            $query->byCity($city);
        }

        // ── Star Rating Filter ──
        if ($star = $request->input('star_rating')) {
            $query->byStarRating((int) $star);
        }

        // ── Price Filter (via room types) ──
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');

            $query->whereHas('roomTypes', function ($q) use ($minPrice, $maxPrice) {
                $q->active();
                if ($minPrice !== null && $minPrice !== '') {
                    $q->where('base_price', '>=', (float) $minPrice);
                }
                if ($maxPrice !== null && $maxPrice !== '') {
                    $q->where('base_price', '<=', (float) $maxPrice);
                }
            });
        }

        // ── Guests Filter (hotel has room type supporting this many guests) ──
        if ($guests = $request->input('guests')) {
            $query->whereHas('roomTypes', function ($q) use ($guests) {
                $q->active()->where('max_guests', '>=', (int) $guests);
            });
        }

        // ── Room Type Filter ──
        if ($roomType = $request->input('room_type')) {
            $query->whereHas('rooms', function ($q) use ($roomType) {
                $q->active()->where('room_type_id', (int) $roomType);
            });
        }

        // ── Amenities Filter (hotel has rooms with ALL selected amenities) ──
        if ($amenityIds = $request->input('amenities')) {
            $amenityIds = is_array($amenityIds) ? $amenityIds : [$amenityIds];
            $amenityIds = array_filter($amenityIds);

            if (! empty($amenityIds)) {
                foreach ($amenityIds as $amenityId) {
                    $query->whereHas('rooms.amenities', function ($q) use ($amenityId) {
                        $q->where('amenities.id', (int) $amenityId);
                    });
                }
            }
        }

        // ── Availability Filter (dates) ──
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = $request->date('check_in');
            $checkOut = $request->date('check_out');

            if ($checkIn->lt($checkOut)) {
                $query->whereHas('rooms', function ($q) use ($checkIn, $checkOut) {
                    $q->availableForDates($checkIn, $checkOut);
                });
            }
        }

        // ── Sorting ──
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        match ($sort) {
            'rating' => $query->orderByDesc('average_rating'),
            'price_low' => $query->orderByRaw('(SELECT MIN(rt.base_price) FROM room_types rt WHERE rt.hotel_id = hotels.id AND rt.is_active = 1) ASC'),
            'price_high' => $query->orderByRaw('(SELECT MIN(rt.base_price) FROM room_types rt WHERE rt.hotel_id = hotels.id AND rt.is_active = 1) DESC'),
            'newest' => $query->latest(),
            'reviews' => $query->orderByDesc('reviews_count'),
            default => $query->orderBy('name', $direction === 'desc' ? 'desc' : 'asc'),
        };

        return $query;
    }

    private function renderView($hotels, Request $request): View
    {
        $options = $this->getCachedOptions();

        return view('frontend.hotels-search', array_merge(compact('hotels'), $options));
    }

    private function getCachedOptions(): array
    {
        return Cache::remember('search.options', 600, function () {
            $cities = Hotel::active()
                ->select('city')
                ->distinct()
                ->orderBy('city')
                ->pluck('city');

            $priceRange = RoomType::active()
                ->selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
                ->first();

            $roomTypes = RoomType::active()
                ->select('id', 'name', 'max_guests')
                ->orderBy('name')
                ->get();

            $amenities = Amenity::active()
                ->select('id', 'name', 'icon')
                ->orderBy('name')
                ->get();

            $starRatings = Hotel::active()
                ->select('star_rating')
                ->distinct()
                ->orderBy('star_rating')
                ->pluck('star_rating');

            return compact('cities', 'priceRange', 'roomTypes', 'amenities', 'starRatings');
        });
    }
}
