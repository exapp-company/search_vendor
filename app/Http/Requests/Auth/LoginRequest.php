<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember_me' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Пожалуйста, укажите ваш адрес электронной почты.',
            'email.email' => 'Пожалуйста, введите действительный адрес электронной почты.',
            'password.required' => 'Пожалуйста, введите пароль.',
        ];

    }

    public function authorize(): bool
    {
        return true;
    }

}
