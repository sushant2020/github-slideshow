<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsageDatumRequest extends FormRequest
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
            'tier_id' => ['required', 'numeric'],
            'source_id' => ['required', 'numeric'],
            'parent_product_code' => ['required'],
            'supplier_id' => ['required', 'numeric'],
            'internal_supplier_id' => ['nullable', 'numeric'],
            'logger_id' => ['nullable', 'numeric'],
            'volume' => ['required'],
            'volume_from_date' => ['nullable', 'date'],
            'volume_untill_date' => ['nullable', 'date'],
            'original_volume_from_date' => ['required', 'date'],
            'original_volume_untill_date' => ['nullable', 'date'],
            'comment' => ['nullable'],
            'is_deleted' => ['required', 'boolean', 'min:0'],
            'inserted_by' => ['required', 'numeric'],
            'lastchanged_by' => ['required', 'numeric']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'tier_id.required' => '',
            //'tier_id.numeric' => '',
            //'source_id.required' => '',
            //'source_id.numeric' => '',
            //'parent_product_code.required' => '',
            //'supplier_id.required' => '',
            //'supplier_id.numeric' => '',
            //'internal_supplier_id.numeric' => '',
            //'logger_id.numeric' => '',
            //'volume.required' => '',
            //'volume_from_date.date' => '',
            //'volume_untill_date.date' => '',
            //'original_volume_from_date.required' => '',
            //'original_volume_from_date.date' => '',
            //'original_volume_untill_date.date' => '',
            //'is_deleted.required' => '',
            //'is_deleted.boolean' => '',
            //'is_deleted.min:0' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
