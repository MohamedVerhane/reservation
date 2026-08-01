<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\NewBookingAlert;
use App\Notifications\NewReviewAlert;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Support\Facades\Notification;

trait NotifyAdmins
{
    protected function notifyAdminsNewBooking(Reservation $reservation): void
    {
        Notification::send(User::admins()->get(), new NewBookingAlert($reservation));
    }

    protected function notifyAdminsNewReview(Review $review): void
    {
        Notification::send(User::admins()->get(), new NewReviewAlert($review));
    }
}
