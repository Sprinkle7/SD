<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Portfolio;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioRequest extends FormRequest
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
            'title' => 'required|string|max:50',
            'images_id' => 'required|array|min:1',
            'images_id.*' => 'required|numeric|min:1',
        ];
    }
}
