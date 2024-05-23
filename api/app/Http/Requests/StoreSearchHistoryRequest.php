<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSearchHistoryRequest extends FormRequest
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
            'module' => ['required', 'boolean', 'numeric'],
            'keyword' => ['required'],
            'inserted_by' => ['required', 'numeric']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'module.required' => '',
            //'module.boolean' => '',
            //'module.numeric' => '',
            //'keyword.required' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => ''
        ];
    }
}
