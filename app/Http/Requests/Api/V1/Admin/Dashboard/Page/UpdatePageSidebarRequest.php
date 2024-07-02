<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Page;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageSidebarRequest extends FormRequest
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
            'sidebar' => 'required|array|min:1',
            'sidebar.*.page_id' => 'required|numeric',
            'sidebar.*.arrange' => 'required|numeric',
        ];
    }
}
