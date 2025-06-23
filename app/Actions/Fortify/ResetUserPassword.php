<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
  use PasswordValidationRules;

  public function reset($user, array $input): void
  {
    Validator::make($input, [
      'password' => $this->passwordRules(),
    ])->after(function ($validator) use ($user, $input) {
      if (Hash::check($input['password'], $user->password)) {
        $validator->errors()->add(
          'password',
          'Password baru tidak boleh sama dengan password sebelumnya.'
        );
      }
    })->validate();

    $user->forceFill([
      'password' => Hash::make($input['password']),
    ])->save();
  }
}
