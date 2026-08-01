<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;

class HotelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('hotel.view-any');
    }

    public function view(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotel.view') || $hotel->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('hotel.create');
    }

    public function update(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotel.update') || $hotel->isOwnedBy($user);
    }

    public function delete(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotel.delete') || $hotel->isOwnedBy($user);
    }

    public function restore(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotel.restore');
    }

    public function forceDelete(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotel.force-delete');
    }

    public function toggleStatus(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotel.toggle-status') || $hotel->isOwnedBy($user);
    }
}
