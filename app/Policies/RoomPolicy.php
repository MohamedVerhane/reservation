<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('room.view-any');
    }

    public function view(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('room.view') || $room->hotel->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('room.create');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('room.update') || $room->hotel->isOwnedBy($user);
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('room.delete') || $room->hotel->isOwnedBy($user);
    }

    public function toggleStatus(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('room.toggle-status') || $room->hotel->isOwnedBy($user);
    }
}
