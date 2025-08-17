<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => 'required|integer',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|integer', // Har item mein menu_item_id honi chahiye
            'items.*.quantity' => 'required|integer|min:1', // Har item ki quantity kam se kam 1 honi chahiye
        ];
    }
}
