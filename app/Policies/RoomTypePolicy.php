<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;

class RoomTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('room-type.view-any');
    }

    public function view(User $user, RoomType $roomType): bool
    {
        return $user->hasPermissionTo('room-type.view') || $roomType->hotel->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('room-type.create');
    }

    public function update(User $user, RoomType $roomType): bool
    {
        return $user->hasPermissionTo('room-type.update') || $roomType->hotel->isOwnedBy($user);
    }

    public function delete(User $user, RoomType $roomType): bool
    {
        return $user->hasPermissionTo('room-type.delete') || $roomType->hotel->isOwnedBy($user);
    }

    public function toggleStatus(User $user, RoomType $roomType): bool
    {
        return $user->hasPermissionTo('room-type.toggle-status') || $roomType->hotel->isOwnedBy($user);
    }
}
