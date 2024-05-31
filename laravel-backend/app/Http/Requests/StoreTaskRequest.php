<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'module_id' => ['required', 'boolean', 'numeric'],
            'product_id' => ['nullable', 'numeric'],
            'supplier_id' => ['nullable', 'numeric'],
            'name' => ['required'],
            'assigned_to' => ['nullable', 'numeric'],
            'status' => ['required', 'boolean', 'min:0'],
            'priority' => ['required', 'boolean', 'min:0'],
            'inserted_by' => ['required', 'numeric'],
            'lastchanged_by' => ['required', 'numeric']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'module_id.required' => '',
            //'module_id.boolean' => '',
            //'module_id.numeric' => '',
            //'product_id.numeric' => '',
            //'supplier_id.numeric' => '',
            //'name.required' => '',
            //'assigned_to.numeric' => '',
            //'status.required' => '',
            //'status.boolean' => '',
            //'status.min:0' => '',
            //'priority.required' => '',
            //'priority.boolean' => '',
            //'priority.min:0' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
