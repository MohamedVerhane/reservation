<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Hotel;
use App\Models\Review;
use App\Traits\NotifyAdmins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use NotifyAdmins;

    public function store(StoreReviewRequest $request, string $hotelSlug): RedirectResponse
    {
        $hotel = Hotel::where('slug', $hotelSlug)->firstOrFail();

        $review = Review::create([
            'user_id'        => Auth::id(),
            'hotel_id'       => $hotel->id,
            'reservation_id' => $request->validated('reservation_id'),
            'rating'         => $request->validated('rating'),
            'comment'        => $request->validated('comment'),
            'is_approved'    => false,
        ]);

        $this->notifyAdminsNewReview($review);

        return redirect()->route('frontend.hotel.show', $hotel->slug)
            ->with('success', __('auth.review_submitted'));
    }

    public function destroy(string $hotelSlug, Review $review): RedirectResponse
    {
        $hotel = Hotel::where('slug', $hotelSlug)->firstOrFail();

        abort_unless(
            $review->isOwnedBy(Auth::user()) && $review->hotel_id === $hotel->id,
            403
        );

        $review->delete();

        return redirect()->route('frontend.hotel.show', $hotel->slug)
            ->with('success', __('auth.review_deleted'))
            ->orJson();
    }
}
