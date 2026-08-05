<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\NewBookingAlert;
use App\Notifications\NewReviewAlert;
use App\Models\Reservation;
use App\Models\Review;

trait NotifyAdmins
{
    protected function notifyAdminsNewBooking(Reservation $reservation): void
    {
        foreach (User::admins()->get() as $admin) {
            $this->sendNotification($admin, new NewBookingAlert($reservation));
        }
    }

    protected function notifyAdminsNewReview(Review $review): void
    {
        foreach (User::admins()->get() as $admin) {
            $this->sendNotification($admin, new NewReviewAlert($review));
        }
    }

    /**
     * Send a single notification without letting SMTP failures (e.g. rate limits)
     * crash the underlying business flow.
     *
     * @param \Illuminate\Notifications\Notification $notification
     */
    protected function sendNotification(User $user, $notification): void
    {
        try {
            $user->notify($notification);
        } catch (\Throwable $e) {
            report($e);
        }

        // Many mail providers (e.g. Mailtrap free tier) allow ~1 email/second.
        // Space consecutive sends so a burst isn't rejected with a 550 error.
        usleep(1100000);
    }
}
