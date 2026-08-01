<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    /** @param array<string, string> $input */
    public function reset(CanResetPassword $user, array $input): void
    {
        Validator::make($input, ['password' => ['required', 'string', 'confirmed', 'min:8']])->validate();
        $user->forceFill(['password' => Hash::make($input['password'])])->save();
    }
}
