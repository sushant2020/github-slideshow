<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGRNRequest extends FormRequest
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
            'Company_Id' => ['nullable', 'numeric'],
            'Supplier_Id' => ['nullable', 'numeric'],
            'Product_Id' => ['nullable', 'numeric'],
            'Depot_Id' => ['nullable', 'numeric'],
            'Foreign_Currency_Id' => ['nullable', 'numeric'],
            'PurchaseOrder_Id' => ['nullable', 'numeric'],
            'PurchaseOrder_Operator_Id' => ['nullable', 'numeric'],
            'Grn_No' => ['nullable', 'numeric'],
            'Grn_Qty' => ['nullable', 'numeric'],
            'Grn_Value' => ['nullable'],
            'Grn_Price' => ['nullable'],
            'GRN_Exchange_Rate' => ['nullable'],
            'Purchase_Order_No' => ['nullable', 'numeric'],
            'Purchase_Order_Line_No' => ['nullable', 'numeric'],
            'Purchase_Order_Line_Desc' => ['nullable'],
            'PurchaseOrder_Value' => ['nullable'],
            'PurchaseOrder_Qty' => ['nullable', 'numeric'],
            'PurchaseOrder_FC_Value' => ['nullable'],
            'PurchaseOrder_Exchange_Rate' => ['nullable'],
            'PurchaseOrder_Type' => ['nullable'],
            'Master_Order_No' => ['nullable'],
            'Sales_Order_No' => ['nullable'],
            'Sales_Order_Line_No' => ['nullable'],
            'Sell_By_Date' => ['nullable', 'date'],
            'Due_Date' => ['nullable', 'date'],
            'Order_Date' => ['nullable', 'date'],
            'Receipt_Date' => ['nullable', 'date'],
            'Qty_Desc' => ['nullable'],
            'Price_Desc' => ['nullable'],
            'Weight' => ['nullable'],
            'Period' => ['nullable'],
            'Days_Late' => ['nullable', 'numeric'],
            'Late_Qty' => ['nullable', 'numeric'],
            'Return_Qty' => ['nullable', 'numeric'],
            'Claim_Qty' => ['nullable', 'numeric'],
            'Trans_Anal_6' => ['nullable'],
            'Account_Year' => ['nullable'],
            'Account_Month' => ['nullable'],
            'Cost_in_Currency' => ['nullable'],
            'inserted_by' => ['required', 'numeric'],
            'lastchanged_by' => ['required', 'numeric']
        ];
    }


    public function messages()
    {
        // Remove comments to return a custom message
        return [
            //'Company_Id.numeric' => '',
            //'Supplier_Id.numeric' => '',
            //'Product_Id.numeric' => '',
            //'Depot_Id.numeric' => '',
            //'Foreign_Currency_Id.numeric' => '',
            //'PurchaseOrder_Id.numeric' => '',
            //'PurchaseOrder_Operator_Id.numeric' => '',
            //'Grn_No.numeric' => '',
            //'Grn_Qty.numeric' => '',
            //'Purchase_Order_No.numeric' => '',
            //'Purchase_Order_Line_No.numeric' => '',
            //'PurchaseOrder_Qty.numeric' => '',
            //'Sell_By_Date.date' => '',
            //'Due_Date.date' => '',
            //'Order_Date.date' => '',
            //'Receipt_Date.date' => '',
            //'Days_Late.numeric' => '',
            //'Late_Qty.numeric' => '',
            //'Return_Qty.numeric' => '',
            //'Claim_Qty.numeric' => '',
            //'inserted_by.required' => '',
            //'inserted_by.numeric' => '',
            //'lastchanged_by.required' => '',
            //'lastchanged_by.numeric' => ''
        ];
    }
}
