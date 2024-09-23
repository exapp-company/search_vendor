<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfficeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shop_id' => 'required|exists:shops,id',
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|min:3',
            'address' => 'nullable|string|min:3',
            'email' => 'nullable|string|email',
            'lat' => 'required_with:lon|numeric|',
            'lon' => 'required_with:lat|numeric',
            'phone' => ["nullable", "string", 'regex:/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/i'],
            'use_parent_feed' => 'sometimes|boolean',
            'use_parent_mapping' => 'sometimes|boolean',
            'feed_url' => 'nullable|url',
            'feed_type' => 'required_with:feed_url,nullable|string|in:xml,json',
            'feed_mapping' => 'nullable|array:name,price,picture,url,in_stock,amount,sku,wholesale_price',
            'feed_mapping.*' => 'array:type,name|size:2',
            'feed_mapping.*.type' => 'nullable|required|in:attribute,field',
            'feed_mapping.*.name' => 'required|string',
            'locations' => 'sometimes|array',
            'locations.*' => 'numeric|exists:locations,id'
        ];
    }
}
