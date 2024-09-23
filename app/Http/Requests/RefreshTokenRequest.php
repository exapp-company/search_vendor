<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefreshTokenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'refresh_token' => ['required'],
            'user_id' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'refresh_token.required' => 'Токен обновления обязателен',
            'user_id.required' => 'Идентификатор пользователя обязателен',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
