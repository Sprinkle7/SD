<?php

namespace App\Http\Requests\Api\V1\User\Site\Address;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
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
            'first_name' => 'required|string|max:190',
            'last_name' => 'required|string|max:190',
            'phone' => 'required|max:14',
            'email' => 'required|email|max:190',
            'gender' => 'string|required',
            'company' => 'string|max:190',
            'address' => 'required|string|max:190',
            'additional_address' => 'nullable|string|max:190',
            'postcode' => 'required',
            'city' => 'required|string|max:190',
            'country_id' => 'required|numeric|max:1000',
        ];
    }
}
