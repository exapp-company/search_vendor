<?php

namespace App\Http\Requests;

use App\Enums\FileTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email'],
            'address' => ['nullable', 'string', 'max:255'],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'supplier_id' => ['nullable', Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'supplier');
            })],
            'feed_url' => 'nullable|url',
            'feed_type' => 'required_with:feed_url,nullable|string|in:xml,json',
            'feed_mapping' => 'nullable|array:name,price,picture,url,in_stock,amount,sku,wholesale_price',
            'feed_mapping.*' => 'array:type,name|size:2',
            'feed_mapping.*.type' => 'nullable|required|in:attribute,field',
            'feed_mapping.*.name' => 'required|string',
            'has_subdomain' => 'nullable|boolean',
            'feed_item' => 'nullable|string',
            'recommended' => 'nullable|boolean'
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, укажите название магазина.',
            'logo.max' => 'Максимальный размер изображения не должен превышать 2 МБ.',
            'logo.in' => 'Недопустимый формат изображения. Допустимые форматы: ' . implode(', ', FileTypes::values()),
            'email.email' => 'Пожалуйста, укажите действительный адрес электронной почты.',
            'city_id.required' => 'Пожалуйста, выберите город.',
            'supplier_id.exists' => 'Поставщик не найден. Пожалуйста, выберите существующего поставщика.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
