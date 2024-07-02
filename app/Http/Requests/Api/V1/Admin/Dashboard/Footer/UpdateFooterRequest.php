<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Footer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterRequest extends FormRequest
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
            'footer_sections' => 'required|array|min:3',
            'footer_sections.*.footer_section_id' => 'required|numeric',
            'footer_sections.*.title' => 'nullable|string',
            'footer_sections.*.type' => 'nullable|string',
            'footer_sections.*.arrange' => 'required|numeric',
            'footer_sections.*.items' => 'nullable|array',
            'footer_sections.*.items.*.id' => 'required|numeric',
            'footer_sections.*.items.*.arrange' => 'required|numeric',

        ];
    }
}
