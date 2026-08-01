<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('reservation.view-any');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('reservation.view') || $reservation->isOwnedBy($user);
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('reservation.update') || $reservation->isOwnedBy($user);
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('reservation.delete') || $reservation->isOwnedBy($user);
    }

    public function confirm(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('reservation.confirm') || $reservation->isOwnedBy($user);
    }

    public function checkIn(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('reservation.check-in') || $reservation->isOwnedBy($user);
    }

    public function checkOut(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('reservation.check-out') || $reservation->isOwnedBy($user);
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('reservation.cancel') || $reservation->isOwnedBy($user);
    }
}
