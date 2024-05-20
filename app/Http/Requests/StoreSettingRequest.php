<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'param' => ['required'],
            'description' => ['nullable'],
            'default_value' => ['nullable'],
            'value' => ['nullable'],
            'is_active' => ['nullable', 'boolean', 'min:0'],
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
            //'param.required' => '',
            //'is_active.boolean' => '',
            //'is_active.min:0' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
