<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleMaintenanceDetailRequest extends FormRequest
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
            'maintenance_id' => 'required|exists:App\Models\VehicleMaintenance,maintenance_id',
            'item_name' => 'required|string|max:50',
            'item_qty' => 'required|integer|min:0',
            'item_unit' => 'required|string|max:10',
            'item_price' => 'required|integer|min:0',
            'price_total' => 'nullable|integer|min:0'
        ];
    }
}
