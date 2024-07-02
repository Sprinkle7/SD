<?php

namespace App\Http\Requests\Api\V1\User\Dashboard\Profile\Password;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'password' => 'required|confirmed|min:8|max:20',
            'password_confirmation' => 'required|min:8|max:20',
        ];
    }
}
