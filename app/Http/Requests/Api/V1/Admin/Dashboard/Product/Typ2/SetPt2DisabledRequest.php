<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2;

use Illuminate\Foundation\Http\FormRequest;

class SetPt2DisabledRequest extends FormRequest
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
            'disabled' => 'nullable|array',
            'disabled.*.category_id' => 'required|numeric|min:1',
            'disabled.*.pt1_combination_id' => 'required|numeric|min:1',
            'disabled.*.disabled_category_id' => 'required|numeric|min:1',
            'disabled.*.disabled_pt1_combination_id' => 'required|numeric|min:1',
        ];
    }
}
