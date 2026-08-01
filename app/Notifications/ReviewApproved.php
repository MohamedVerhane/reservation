<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApproved extends Notification implements ShouldQueue
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
            ->subject(__('auth.notif_review_approved_subject'))
            ->greeting(__('auth.notif_hello', ['name' => $notifiable->name]))
            ->line(__('auth.notif_review_approved_line1'))
            ->line(__('auth.notif_hotel_with_value', ['value' => $review->hotel->name]))
            ->line(__('auth.notif_rating_with_value', ['value' => $review->star_display]))
            ->action(__('auth.notif_view_hotel'), route('frontend.hotel.show', $review->hotel->slug));
    }

    public function toArray(object $notifiable): array
    {
        $review = $this->review;

        return [
            'type'       => 'review_approved',
            'review_id'  => $review->id,
            'hotel_name' => $review->hotel->name,
            'hotel_slug' => $review->hotel->slug,
            'rating'     => $review->rating,
            'message'    => __('auth.notif_review_approved_line1'),
        ];
    }
}
