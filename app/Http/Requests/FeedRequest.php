<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'url' => ['required', 'string', Rule::unique('feeds', 'url')->ignore($this->feed)],
            'status_id' => ['nullable', Rule::exists('feed_statuses', 'id')],
            'shop_id' => ['required', 'exists:shops,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'Пожалуйста, введите URL',
            'url.unique' => 'URL уже существует',
            'shop_id.required' => 'Пожалуйста, выберите магазин',
            'shop_id.exists' => 'Магазин не найден',
            'status_id.exists' => 'Статус не найден',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
