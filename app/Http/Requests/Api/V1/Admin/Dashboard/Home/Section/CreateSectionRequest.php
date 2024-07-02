<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class CreateSectionRequest extends FormRequest
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
            'title' => 'required|string',
            'language' => ['required', 'string', new CheckLanguage],
            'type' => 'required|string',
            'products_id' => 'required_if:type,manual|array|min:1',
            'products_id.*.product_id' => 'required|numeric|min:1',
            'products_id.*.arrange' => 'required|numeric|min:1',
        ];
    }
}