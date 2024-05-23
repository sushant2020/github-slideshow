<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsFeedRequest extends FormRequest
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
            'name' => ['required'],
            'description' => ['nullable'],
            'is_active' => ['required', 'boolean', 'min:0'],
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
            //'name.required' => '',
            //'is_active.required' => '',
            //'is_active.boolean' => '',
            //'is_active.min:0' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
