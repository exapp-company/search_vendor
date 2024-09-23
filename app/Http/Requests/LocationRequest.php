<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
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
        $location = $this->route('location');
        $childrens = [];
        if ($location) {
            $childrens[] = $location->id;
            $currenChilds = Location::where('parent_id', $location->id)->with('deep_chieldren')->get();
            $currenChilds = $currenChilds->toArray();
            for ($i = 0; $i < count($currenChilds); $i++) {
                $childrens[] = $currenChilds[$i]['id'];
                if (count($currenChilds[$i]['deep_chieldren'])) {
                    $currenChilds = array_merge($currenChilds, $currenChilds[$i]['deep_chieldren']);
                }
            }
        }
        return [
            'city_id' => 'required|numeric|exists:cities,id',
            'title' => 'required|string|min:3|max:255',
            'type' => 'required|string|in:district,metro,transport_stop,mart',
            'parent_id' => ['nullable', 'numeric', Rule::notIn($childrens), 'exists:locations,id']
        ];
    }
}
