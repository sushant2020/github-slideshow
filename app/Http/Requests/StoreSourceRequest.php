<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSourceRequest extends FormRequest
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
            'name' => ['required'],
            'tier_id' => ['required', 'numeric'],
            'status' => ['required', 'boolean', 'min:0'],
            'inserted_by' => ['required', 'numeric'],
            'lastchanged_by' => ['required', 'numeric']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'name.required' => '',
            //'tier_id.required' => '',
            //'tier_id.numeric' => '',
            //'status.required' => '',
            //'status.boolean' => '',
            //'status.min:0' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
