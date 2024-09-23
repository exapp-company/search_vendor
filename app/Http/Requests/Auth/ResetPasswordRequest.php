<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'exists:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Пожалуйста, введите ваш адрес электронной почты.',
            'email.email' => 'Пожалуйста, укажите действительный адрес электронной почты.',
            'email.exists' => 'Пользователь с указанным адресом электронной почты не существует.',
            'password.required' => 'Пожалуйста, введите пароль.',
            'password.confirmed' => 'Пароль и его подтверждение не совпадают. Пожалуйста, убедитесь, что они совпадают.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
