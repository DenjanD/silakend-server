<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleUsageRequest extends FormRequest
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
        /*
            STATUSES:
            1. WAITING: User made an order to VERIFIER.
            2. APPROVED: Order verified by VERIFIER, sent to VALIDATOR.
            3. READY: Order validated by VALIDATOR, sent to DRIVER.
            4. PROGRESS: Order accepted by DRIVER, confirmed by USER.
            5. DONE: Order successfully done, confirmed by DRIVER.
            6. CANCELED: Order canceled by USER.
            7. REJECTED: Order cancelen by VERIFIER, or VALIDATOR.
        */
        return [
            'vehicle_id' => 'required|exists:App\Models\Vehicle,vehicle_id',
            'driver_id' => 'required|exists:App\Models\User,user_id',
            'user_id' => 'required|exists:App\Models\User,user_id',
            'ucategory_id' => 'required|exists:App\Models\UsageCategory,ucategory_id',
            'usage_description' => 'required|string',
            'personel_count' => 'required|integer|digits_between:1,11',
            'destination' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'depart_date' => 'nullable|date',
            'depart_time' => 'nullable|date_format:H:i',
            'arrive_date' => 'nullable|date',
            'arrive_time' => 'nullable|date_format:H:i',
            'distance_count_out' => 'nullable|integer|min:0',
            'distance_count_in' => 'nullable|integer|min:0',
            'status' => 'nullable|in:WAITING,APPROVED,READY,PROGRESS,DONE,CANCELED,REJECTED',
            'status_description' => 'nullable|string'
        ];
    }
}
