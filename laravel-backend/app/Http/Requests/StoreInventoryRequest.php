<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
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
            'Company_Id' => ['required', 'numeric'],
            'Product_Id' => ['nullable', 'numeric'],
            'Depot_Id' => ['nullable', 'numeric'],
            'LG_Date' => ['nullable', 'date'],
            'LS_Date' => ['nullable', 'date'],
            'Physical_Stock' => ['nullable', 'numeric'],
            'Allocation_Stock' => ['nullable', 'numeric'],
            'Allocation_After' => ['nullable', 'numeric'],
            'On_Order' => ['nullable', 'numeric'],
            'Backorder' => ['nullable', 'numeric'],
            'LG_Number' => ['nullable', 'numeric'],
            'LPP_Cost' => ['nullable'],
            'Avg_Cost' => ['nullable'],
            'TCost' => ['nullable'],
            'Min_Stock' => ['nullable', 'numeric'],
            'Std_Cost' => ['nullable'],
            'Max_Stock' => ['nullable'],
            'Average_usage' => ['nullable', 'numeric'],
            'Pick_Bin' => ['nullable'],
            'Average_usage_UOM' => ['nullable'],
            'Average_usage_Period' => ['nullable'],
            'inserted_by' => ['required', 'numeric'],
            'lastchanged_by' => ['required', 'numeric']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'Company_Id.required' => '',
            //'Company_Id.numeric' => '',
            //'Product_Id.numeric' => '',
            //'Depot_Id.numeric' => '',
            //'LG_Date.date' => '',
            //'LS_Date.date' => '',
            //'Physical_Stock.numeric' => '',
            //'Allocation_Stock.numeric' => '',
            //'Allocation_After.numeric' => '',
            //'On_Order.numeric' => '',
            //'Backorder.numeric' => '',
            //'LG_Number.numeric' => '',
            //'Min_Stock.numeric' => '',
            //'Average_usage.numeric' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
