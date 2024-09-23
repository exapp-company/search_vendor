<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends ApiController
{
    public function __invoke(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
                $user->tokens()->delete();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? $this->success(__('Пароль успешно изменен'))
            : $this->error(__('Произошла ошибка при изменении пароля'), HttpStatus::internalServerError);
    }
}
