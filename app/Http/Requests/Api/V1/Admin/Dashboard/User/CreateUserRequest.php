<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => ['required'],
            'email' => 'required|email|unique:users',
            'gender' => 'string|required',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required|min:8',
            'company' => 'nullable|string',
            'address' => 'nullable|string',
            'additional_address' => 'nullable|string',
            'postcode' => 'nullable',
            'city' => 'nullable|string',
            'country_id' => 'required|numeric',
            'role_id' => 'required|numeric',
        ];
    }
}
