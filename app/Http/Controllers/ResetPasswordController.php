<?php

namespace App\Http\Controllers;

use App\Models\ResetCodePassword;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends ApiController
{

    public function __invoke(ResetPasswordRequest $request)
    {
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->isExpire()) {
            return $this->jsonResponse(null, trans('passwords.code_is_expire'), 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        $user->update(['password' => Hash::make($request->password)]);

        $passwordReset->delete();

        return $this->jsonResponse(null, trans('site.password_has_been_successfully_reset'), 200);
    }
}
