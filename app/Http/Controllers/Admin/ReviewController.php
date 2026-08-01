<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Review;
use App\Notifications\ReviewApproved;
use App\Notifications\ReviewReplyReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $query = Review::with(['user', 'hotel']);

        if (request('search')) {
            $term = '%' . addslashes(request('search')) . '%';
            $query->where(function ($q) use ($term): void {
                $q->where('comment', 'LIKE', $term)
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'LIKE', $term))
                  ->orWhereHas('hotel', fn ($hq) => $hq->where('name', 'LIKE', $term));
            });
        }

        if (request('hotel_id')) {
            $query->where('hotel_id', request('hotel_id'));
        }

        if (request('rating')) {
            $query->where('rating', request('rating'));
        }

        if (request('reply_status') === 'replied') {
            $query->whereNotNull('reply');
        } elseif (request('reply_status') === 'pending') {
            $query->whereNull('reply');
        }

        if (request('approval_status') === 'approved') {
            $query->approved();
        } elseif (request('approval_status') === 'pending') {
            $query->pending();
        }

        if (request('trashed') === '1') {
            $query->onlyTrashed();
        }

        $reviews = $query->ordered()->paginate(15)->withQueryString();
        $hotels = Hotel::orderBy('name')->pluck('name', 'id');

        return view('admin.reviews.index', compact('reviews', 'hotels'));
    }

    public function show(Review $review): View
    {
        $this->authorize('view', $review);

        $review->load(['user', 'hotel', 'reservation' => fn ($q) => $q->with('room')]);

        return view('admin.reviews.show', compact('review'));
    }

    public function reply(Review $review): RedirectResponse
    {
        $this->authorize('reply', $review);

        $validated = request()->validate([
            'reply' => ['required', 'string', 'max:2000'],
        ]);

        $review->addReply($validated['reply']);
        $review->load('user', 'hotel');
        $review->user->notify(new ReviewReplyReceived($review));

        return redirect()->route('admin.reviews.show', $review)
            ->with('success', __('admin.review.reply_sent'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $this->authorize('reply', $review);

        $review->approve();
        $review->load('user', 'hotel');
        $review->user->notify(new ReviewApproved($review));

        return redirect()->back()
            ->with('success', __('auth.review_approved'));
    }

    public function reject(Review $review): RedirectResponse
    {
        $this->authorize('reply', $review);

        $review->reject();

        return redirect()->back()
            ->with('success', __('auth.review_rejected'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', __('admin.review.deleted'));
    }

    public function restore(int $id): RedirectResponse
    {
        $review = Review::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $review);

        $review->restore();

        return redirect()->route('admin.reviews.index', ['trashed' => '1'])
            ->with('success', __('admin.review.restored'));
    }
}
