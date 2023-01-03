<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleMaintenanceRequest extends FormRequest
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
            'vehicle_id' => 'required|exists:App\Models\Vehicle,vehicle_id',
            'date' => 'required|date',
            'category' => 'required|string|max:40',
            'description' => 'required|string',
            'total_cost' => 'required|integer|min:0'
        ];
    }
}
