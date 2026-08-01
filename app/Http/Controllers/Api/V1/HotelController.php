<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\HotelSearchRequest;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Hotel::active()
            ->withCount(['rooms as available_rooms_count' => fn ($q) => $q->where('is_active', true)->where('status', 'available')])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()]);

        if ($search = $request->input('search')) {
            $safeTerm = '%' . addslashes($search) . '%';
            $query->where(function ($q) use ($safeTerm): void {
                $q->where('name', 'LIKE', $safeTerm)
                  ->orWhere('city', 'LIKE', $safeTerm)
                  ->orWhere('address', 'LIKE', $safeTerm);
            });
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
                default  => $q->orderBy('name'),
            });

        $perPage = min((int) $request->input('per_page', 15), 50);
        $hotels = $query->paginate($perPage);

        return $this->paginatedResponse(HotelResource::collection($hotels));
    }

    public function show(string $slug): JsonResponse
    {
        $hotel = Hotel::where('slug', $slug)
            ->active()
            ->with([
                'user:id,name,email',
                'roomTypes' => fn ($q) => $q->active()->withCount('rooms'),
                'rooms'     => fn ($q) => $q->active()->with(['roomType', 'images', 'amenities']),
                'reviews'   => fn ($q) => $q->approved()->with('user:id,name')->latest()->take(10),
                'galleries' => fn ($q) => $q->with('images')->ordered(),
            ])
            ->withCount(['rooms as available_rooms_count' => fn ($q) => $q->where('is_active', true)->where('status', 'available')])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()])
            ->first();

        if (!$hotel) {
            return $this->notFoundResponse('Hotel not found.');
        }

        return $this->successResponse(
            new HotelResource($hotel),
            'Hotel retrieved'
        );
    }

    public function cities(): JsonResponse
    {
        $cities = Hotel::active()->distinct()->pluck('city')->sort()->values();

        return $this->successResponse($cities, 'Cities retrieved');
    }

    protected function rawAvgRatingQuery(): string
    {
        return '(SELECT AVG(r.rating) FROM reviews r WHERE r.hotel_id = hotels.id AND r.is_approved = 1 AND r.deleted_at IS NULL)';
    }
}
