<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends ApiController
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                $user->notify(new ResetPasswordNotification($token));
            }
        );


        return $status === Password::RESET_LINK_SENT
            ? $this->success(__('Password reset link has been sent to your email.'))
            : $this->error(__('An error occurred while sending the email.'), HttpStatus::internalServerError);
    }
}
