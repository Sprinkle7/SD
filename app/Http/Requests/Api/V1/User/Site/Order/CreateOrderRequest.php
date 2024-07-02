<?php

namespace App\Http\Requests\Api\V1\User\Site\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'addresses' => 'required|array|min:1',
            'addresses.*.address_id' => 'nullable|numeric|min:1',
            'addresses.*.post_duration_id' => 'required|numeric|min:1',
            'addresses.*.items' => 'required|array|min:1',
            'addresses.*.items.*.product_id' => 'required|numeric|min:1',
            'addresses.*.items.*.combination_id' => 'required|numeric|min:1',
            'addresses.*.items.*.duration_id' => 'required|numeric|min:1',
            'addresses.*.items.*.services' => 'nullable|string',
            'addresses.*.items.*.quantity' => 'required|numeric|min:1',

        ];
    }
}
