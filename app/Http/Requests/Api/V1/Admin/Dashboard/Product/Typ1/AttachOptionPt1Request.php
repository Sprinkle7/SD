<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ1;

use Illuminate\Foundation\Http\FormRequest;

class AttachOptionPt1Request extends FormRequest
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
            'options' => 'required|array|min:1',
            'options.*.option_id' => 'required|numeric|min:1',
            'options.*.option_value_id' => 'required|numeric|min:1',
        ];
    }
}
