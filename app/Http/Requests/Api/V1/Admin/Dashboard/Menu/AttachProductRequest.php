<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Menu;

use Illuminate\Foundation\Http\FormRequest;

class AttachProductRequest extends FormRequest
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
            'products_id' => 'required|array|min:1',
            'products_id.*' => 'required|numeric|min:1',
        ];
    }
}
