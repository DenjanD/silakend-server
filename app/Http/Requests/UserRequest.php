<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'nip' => 'required|numeric|digits_between:1,20',
            'password' => 'nullable|confirmed|string|min:8',
            'name' => 'required|string|max:100|regex:/^[a-zA-Z ]+$/',
            'address' => 'required|string',
            'phone' => [
                'required',
                'numeric',
                'digits_between:1,14',
                'regex:/^(\+62|62)?[\s-]?0?8[1-9]{1}\d{1}[\s-]?\d{4}[\s-]?\d{2,5}$/'
            ],
            'email' => [
                        'required','string','email','max:60',
                        Rule::unique('users','email')->ignore($this->route()->user,'user_id')
                    ],
            'unit_id' => 'required|exists:App\Models\JobUnit,unit_id',
            'role_id' => 'nullable|exists:App\Models\Role,role_id',
        ];
    }
}
