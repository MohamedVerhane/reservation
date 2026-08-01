<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('review.view-any');
    }

    public function view(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('review.view');
    }

    public function approve(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('review.approve');
    }

    public function reject(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('review.reject');
    }

    public function reply(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('review.reply');
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('review.delete');
    }

    public function restore(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('review.restore');
    }
}
