<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
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
            'product_id' => ['required', 'numeric'],
            'supplier_id' => ['required', 'numeric'],
            'suggested_quantity' => ['required', 'numeric'],
            'preferred_price' => ['required'],
            'notes' => ['nullable'],
            'status' => ['required', 'boolean', 'min:0'],
            'created_by' => ['required', 'numeric'],
            'lastchanged_by' => ['required', 'numeric'],
            'verified_by' => ['nullable', 'numeric'],
            'verified_at' => ['nullable', 'date']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'product_id.required' => '',
            //'product_id.numeric' => '',
            //'supplier_id.required' => '',
            //'supplier_id.numeric' => '',
            //'suggested_quantity.required' => '',
            //'suggested_quantity.numeric' => '',
            //'preferred_price.required' => '',
            //'status.required' => '',
            //'status.boolean' => '',
            //'status.min:0' => '',
            //'created_by.required' => '',
            //'created_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => '',
            //'verified_by.numeric' => '',
            //'verified_at.date' => ''
        ];
    }
}
