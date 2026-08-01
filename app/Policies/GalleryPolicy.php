<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;

class GalleryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('gallery.view-any');
    }

    public function view(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo('gallery.view') || $gallery->hotel->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('gallery.create');
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo('gallery.update') || $gallery->hotel->isOwnedBy($user);
    }

    public function delete(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo('gallery.delete') || $gallery->hotel->isOwnedBy($user);
    }

    public function manageImages(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo('gallery.manage-images') || $gallery->hotel->isOwnedBy($user);
    }
}
