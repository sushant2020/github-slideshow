<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DwGRN extends Model
{

    public $table = 'staging.dw_GRN';
    //public $primaryKey = 'GRN_Id';

    /* public $fillable = [
      'account_month',
      'account_year',
      'claim_qty',
      'company_id',
      'cost_in_currency',
      'days_late',
      'depot_id',
      'due_date',
      'foreign_currency_id',
      'grn_exchange_rate',
      'grn_id',
      'grn_no',
      'grn_price',
      'grn_qty',
      'grn_value',
      'inserted_by',
      'lastchanged_by',
      'late_qty',
      'master_order_no',
      'order_date',
      'period',
      'price_desc',
      'product_id',
      'purchase_order_line_desc',
      'purchase_order_line_no',
      'purchase_order_no',
      'purchaseorder_exchange_rate',
      'purchaseorder_fc_value',
      'purchaseorder_id',
      'purchaseorder_operator_id',
      'purchaseorder_qty',
      'purchaseorder_type',
      'purchaseorder_value',
      'qty_desc',
      'receipt_date',
      'return_qty',
      'sales_order_line_no',
      'sales_order_no',
      'sell_by_date',
      'supplier_id',
      'trans_anal_6',
      'weight',
      ];

      public $casts = [
      'account_month' => 'string',
      'account_year' => 'string',
      'master_order_no' => 'string',
      'period' => 'string',
      'price_desc' => 'string',
      'purchase_order_line_desc' => 'string',
      'purchaseorder_type' => 'string',
      'qty_desc' => 'string',
      'sales_order_line_no' => 'string',
      'sales_order_no' => 'string',
      'trans_anal_6' => 'string',
      ];
     */
    public $hidden = [];
    public $appends = [];

}
