<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Gallery;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    public function home(): View
    {
        $featuredHotels = Cache::remember('frontend.featured-hotels', 600, function () {
            return Hotel::active()
                ->withCount(['rooms as available_rooms_count' => function ($q) {
                    $q->where('is_active', true)->where('status', 'available');
                }])
                ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()])
                ->latest()
                ->take(6)
                ->get();
        });

        $featuredRooms = Room::active()
            ->available()
            ->with(['hotel', 'roomType', 'images'])
            ->withCount('amenities')
            ->inRandomOrder()
            ->take(6)
            ->get();

        $stats = [
            'hotels' => Hotel::active()->count(),
            'rooms' => Room::active()->count(),
            'guests' => (int) (Hotel::active()->sum('star_rating') * 847),
            'awards' => 24,
        ];

        return view('frontend.home', compact('featuredHotels', 'featuredRooms', 'stats'));
    }

    public function hotels(Request $request): View
    {
        $query = Hotel::active()
            ->withCount(['rooms as available_rooms_count' => function ($q) {
                $q->where('is_active', true)->where('status', 'available');
            }])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()]);

        if ($search = $request->input('search')) {
            $query->search($search);
        }
        if ($city = $request->input('city')) {
            $query->byCity($city);
        }
        if ($star = $request->input('star')) {
            $query->byStarRating((int) $star);
        }

        $sort = $request->input('sort', 'name');
        $query->when($sort === 'rating', fn ($q) => $q->orderByDesc($this->rawAvgRatingQuery()),
            fn ($q) => match ($sort) {
                'newest' => $q->latest(),
                default => $q->orderBy('name'),
            });

        $hotels = $query->paginate(12)->withQueryString();
        $cities = Hotel::active()->distinct()->pluck('city')->sort()->values();

        $featuredHotels = Cache::remember('frontend.featured-hotels', 600, function () {
            return Hotel::active()
                ->withCount(['rooms as available_rooms_count' => function ($q) {
                    $q->where('is_active', true)->where('status', 'available');
                }])
                ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()])
                ->latest()
                ->take(6)
                ->get();
        });

        return view('frontend.hotels', compact('hotels', 'cities', 'featuredHotels'));
    }

    public function hotelShow(string $slug): View
    {
        $hotel = Hotel::where('slug', $slug)
            ->active()
            ->withCount(['rooms as available_rooms_count' => function ($q) {
                $q->where('is_active', true)->where('status', 'available');
            }])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()])
            ->with([
                'roomTypes' => fn ($q) => $q->active()->withCount('rooms'),
                'rooms' => fn ($q) => $q->active()->with(['roomType', 'images', 'amenities']),
                'reviews' => fn ($q) => $q->approved()->with('user')->latest()->take(10),
                'galleries' => fn ($q) => $q->with('images')->ordered(),
            ])
            ->withMin(['roomTypes as min_price' => fn ($q) => $q->active()], 'base_price')
            ->firstOrFail();

        $availableAmenities = Amenity::active()->whereHas('rooms', fn ($q) => $q->where('hotel_id', $hotel->id))->get();

        return view('frontend.hotel-show', compact('hotel', 'availableAmenities'));
    }

    public function roomShow(int $hotelId, int $roomId): View
    {
        $room = Room::where('hotel_id', $hotelId)
            ->where('id', $roomId)
            ->active()
            ->with(['hotel', 'roomType', 'amenities', 'images'])
            ->firstOrFail();

        $relatedRooms = Room::active()
            ->available()
            ->where('hotel_id', $hotelId)
            ->where('id', '!=', $roomId)
            ->with(['roomType', 'images'])
            ->take(3)
            ->get();

        return view('frontend.room-show', compact('room', 'relatedRooms'));
    }

    public function about(): View
    {
        $team = [
            ['name' => __('about.team_member_1_name'), 'role' => __('about.team_member_1_role'), 'icon' => 'bi-person-badge'],
            ['name' => __('about.team_member_2_name'), 'role' => __('about.team_member_2_role'), 'icon' => 'bi-gear-wide-connected'],
            ['name' => __('about.team_member_3_name'), 'role' => __('about.team_member_3_role'), 'icon' => 'bi-palette'],
            ['name' => __('about.team_member_4_name'), 'role' => __('about.team_member_4_role'), 'icon' => 'bi-heart-handshake'],
        ];

        return view('frontend.about', compact('team'));
    }

    public function contact(): View
    {
        return view('frontend.contact');
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        \Mail::raw($validated['message'], function ($mail) use ($validated) {
            $mail->to(config('mail.from.address'))
                ->subject(__('contact.email_subject_prefix') . $validated['subject'])
                ->replyTo($validated['email'], $validated['name']);
        });

        return redirect()->route('frontend.contact')->with('success', __('contact.success'));
    }

    public function gallery(Request $request): View
    {
        $hotels = Hotel::active()->has('galleries')->with('galleries.images')->get();

        $hotelId = $request->input('hotel');
        $query = Gallery::with(['images', 'hotel']);

        if ($hotelId) {
            $query->byHotel($hotelId);
        }

        $galleries = $query->ordered()->paginate(12)->withQueryString();

        $images = $galleries->flatMap(function ($gallery) {
            return $gallery->images->map(function ($image) use ($gallery) {
                return [
                    'image_path' => $image->path,
                    'caption' => $image->caption ?? $gallery->title,
                    'category' => $gallery->title,
                ];
            });
        });

        $categories = $galleries->pluck('title')->unique()->values()->toArray();

        return view('frontend.gallery', compact('galleries', 'hotels', 'images', 'categories'));
    }

    private function rawAvgRatingQuery(): Expression
    {
        return \DB::raw('(SELECT AVG(r.rating) FROM reviews r WHERE r.hotel_id = hotels.id)');
    }
}
