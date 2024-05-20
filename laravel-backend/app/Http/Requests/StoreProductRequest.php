<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'product_code' => ['required'],
            'sm_description' => ['nullable'],
            'sm_description2' => ['nullable'],
            'sm_description3' => ['nullable'],
            'sm_analysis_code1' => ['nullable'],
            'sm_analysis_code2' => ['nullable'],
            'sm_analysis_code3' => ['nullable'],
            'ac4' => ['required'],
            'sm_analysis_code5' => ['nullable'],
            'sm_analysis_code6' => ['nullable'],
            'product_group' => ['nullable'],
            'product_group2' => ['nullable'],
            'sm_bin_loc' => ['nullable'],
            'ske_analysis26' => ['nullable'],
            'vmpp_char_count' => ['nullable', 'numeric'],
            'dt_description' => ['nullable'],
            'dt_pack' => ['required', 'numeric'],
            'dt_type' => ['required'],
            'dt_price' => ['nullable'],
            'pref_supplier' => ['nullable'],
            'vat_code' => ['nullable', 'numeric'],
            'status' => ['required'],
            'sedes_pip_code' => ['nullable', 'numeric'],
            'temperature' => ['nullable'],
            'list_price' => ['required'],
            'desc_pack' => ['nullable'],
            'spec_disc' => ['nullable', 'boolean', 'min:0'],
            'own_brand' => ['nullable'],
            'cust_brand' => ['nullable'],
            'additional1' => ['nullable'],
            'additional2' => ['nullable'],
            'additional3' => ['nullable'],
            'last_purchase_cost' => ['required'],
            'standard_cost' => ['required'],
            'case_description' => ['nullable'],
            'minimum_shelf_life' => ['nullable', 'boolean', 'min:0'],
            'company' => ['required', 'boolean', 'min:0'],
            'dq_status' => ['required'],
            'product_type' => ['required', 'boolean', 'min:0'],
            'final_price' => ['nullable'],
            'inserted_by' => ['required', 'numeric'],
            'lastchanged_by' => ['required', 'numeric']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'product_code.required' => '',
            //'ac4.required' => '',
            //'vmpp_char_count.numeric' => '',
            //'dt_pack.required' => '',
            //'dt_pack.numeric' => '',
            //'dt_type.required' => '',
            //'vat_code.numeric' => '',
            //'status.required' => '',
            //'sedes_pip_code.numeric' => '',
            //'list_price.required' => '',
            //'spec_disc.boolean' => '',
            //'spec_disc.min:0' => '',
            //'last_purchase_cost.required' => '',
            //'standard_cost.required' => '',
            //'minimum_shelf_life.boolean' => '',
            //'minimum_shelf_life.min:0' => '',
            //'company.required' => '',
            //'company.boolean' => '',
            //'company.min:0' => '',
            //'dq_status.required' => '',
            //'product_type.required' => '',
            //'product_type.boolean' => '',
            //'product_type.min:0' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
