<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class VehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:40|regex:/^[a-zA-Z ]+$/',
            'year' => 'required|digits:4|integer|min:1900|max:'.Carbon::tomorrow()->year,
            'tax_date' => 'required|date',
            'valid_date' => 'required|date',
            'license_number' => 'required|string|between:3,11|regex:/^[A-Z]{1,2}\s{1}\d{0,4}\s{0,1}[A-Z]{0,3}$/',
            'distance_count' => 'required|integer|min:0',
            'vcategory_id' => 'required|exists:App\Models\VehicleCategory,vcategory_id'
        ];
    }
}
