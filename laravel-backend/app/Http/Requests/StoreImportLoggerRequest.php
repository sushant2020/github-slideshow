<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportLoggerRequest extends FormRequest
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
            'original_filename' => ['required'],
            'filename' => ['required'],
            'filesize' => ['required'],
            'imported_by' => ['required', 'numeric'],
            'comment' => ['nullable'],
            'output_message' => ['nullable'],
            'uploaded_at' => ['nullable', 'date'],
            'imported_at' => ['required', 'date']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'original_filename.required' => '',
            //'filename.required' => '',
            //'filesize.required' => '',
            //'imported_by.required' => '',
            //'imported_by.numeric' => '',
            //'uploaded_at.date' => '',
            //'imported_at.required' => '',
            //'imported_at.date' => ''
        ];
    }
}
