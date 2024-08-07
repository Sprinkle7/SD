<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Page;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
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
            'slug' =>
            [
                'filled','string',
            Rule::unique('page_translations')->ignore(request()->route('id'),'page_id'),],
            'content' => 'required|string',
            'sidebar_id' => 'nullable|numeric'
        ];
    }
}
