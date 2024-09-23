<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('feed_statuses', 'name')->ignore($this->feedStatus)],
            'code' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, укажите название статуса.',
            'name.string' => 'Значение статуса должно быть строкой.',
            'code.required' => 'Пожалуйста, укажите код статуса.'
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
