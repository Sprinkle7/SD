<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Option;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOptionRequest extends FormRequest
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
            'title' =>  [
                'required',
                'string',
                Rule::unique('option_translations')->ignore(request()->route('optionId'),'option_id'),
            ],
            'option_values' => 'required|array|min:1',
            'option_values.*.title' => 'required|string',
            'option_values.*.option_value_id' => 'filled|numeric|min:1',
            'option_values.*.id' => 'filled|numeric|min:1',
        ];
    }
}
