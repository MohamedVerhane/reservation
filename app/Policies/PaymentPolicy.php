<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('payment.view-any');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.view');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.update');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payment.delete');
    }
}
