<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Review::approved()
            ->with(['user:id,name', 'hotel:id,name,slug']);

        if ($hotelId = $request->input('hotel_id')) {
            $query->where('hotel_id', $hotelId);
        }

        if ($rating = $request->input('rating')) {
            $query->where('rating', $rating);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $reviews = $query->latest()->paginate($perPage);

        return $this->paginatedResponse(ReviewResource::collection($reviews));
    }

    public function show(Review $review): JsonResponse
    {
        if (!$review->is_approved) {
            return $this->notFoundResponse('Review not found.');
        }

        $review->load(['user:id,name', 'hotel:id,name,slug']);

        return $this->successResponse(
            new ReviewResource($review),
            'Review retrieved'
        );
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = Review::create([
            'user_id'        => Auth::id(),
            'hotel_id'       => $request->hotel_id,
            'reservation_id' => $request->reservation_id,
            'rating'         => $request->rating,
            'comment'        => $request->comment,
            'is_approved'    => false,
        ]);

        return $this->createdResponse(
            new ReviewResource($review->load(['user:id,name', 'hotel:id,name,slug'])),
            'Review submitted and pending approval'
        );
    }

    public function destroy(Review $review): JsonResponse
    {
        if ($review->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You can only delete your own reviews.');
        }

        $review->delete();

        return $this->successResponse([], 'Review deleted');
    }
}
