<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewReviewAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Review $review,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $review = $this->review;

        return (new MailMessage)
            ->subject(__('auth.notif_admin_new_review_subject'))
            ->line(__('auth.notif_admin_new_review_line1'))
            ->line(__('auth.notif_guest') . ': ' . ($review->user->name ?? __('auth.notif_na')))
            ->line(__('auth.notif_hotel_with_value', ['value' => $review->hotel->name]))
            ->line(__('auth.notif_rating_with_value', ['value' => __('auth.notif_rating_format', ['rating' => $review->rating])]))
            ->line(__('auth.notif_comment') . ': ' . Str::limit($review->comment, 120))
            ->action(__('auth.notif_admin_view_review'), route('admin.reviews.show', $review));
    }

    public function toArray(object $notifiable): array
    {
        $review = $this->review;

        return [
            'type'       => 'new_review',
            'review_id'  => $review->id,
            'guest_name' => $review->user->name ?? __('auth.notif_na'),
            'hotel_name' => $review->hotel->name,
            'hotel_id'   => $review->hotel_id,
            'rating'     => $review->rating,
            'comment'    => Str::limit($review->comment, 200),
            'message'    => __('auth.notif_admin_new_review_line1'),
        ];
    }
}
