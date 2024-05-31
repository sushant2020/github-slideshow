<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GRN extends Model
{

    public $table = 'GRN';
    public $primaryKey = 'GRN_Id';

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
	
	
		    /**
     * Gets the GRN details
     *
     * @param \App\Models\Product $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getProductGRNData($productid, $page, $sortcolumn, $sort)
    {
        $gdata = [];
		$sortcolumn;
		$limit = 30;
		$sortcolumn = trim($sortcolumn);
		
		if(empty($sortcolumn)) {
			$sortcolumn = 'GRN.Receipt_Date';
			$sorder = "DESC";
		} else {
 		if($sort == 1) {
			$sorder = "ASC";
		} else {
			$sorder = "DESC";
		}
		}
		if($sortcolumn == 'Product_Desc') {
			$sortcolumn = DB::raw('CAST(GRN.Product_Desc AS NVARCHAR(500))');
		}
 
       $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";
		
		$rowCnt = GRN::where("Product_AC_4", $productParentCode)
		->count();
	
		
$grn = GRN::select("Product_Id_Main", "Product_Code","Supplier_Id","Supplier_Code", "Grn_No", DB::raw('CAST(Grn_Price AS DECIMAL(10,2)) AS Grn_Price'), "Grn_Qty", "Receipt_Date","Product_Desc", "Pack_Size")->where("Product_AC_4", $productParentCode)
	->orderBy($sortcolumn, $sorder)
	->limit($limit)->offset(($page - 1) * $limit)
	->get();
		$gdata["grn"] = $grn;
		$gdata["rowCnt"] = $rowCnt;

        return $gdata;
    }
	
	/**
     * Gets the GRN of Supplier
     *
     * @param id $supplierId The supplier id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSupplierGRNData($supplierId, $page, $sortcolumn, $sort)
    {
		$gdata = [];
		$sortcolumn;
		$limit = 30;
		$sortcolumn = trim($sortcolumn);
		
		if(empty($sortcolumn)) {
			$sortcolumn = 'GRN.Receipt_Date';
			$sorder = "DESC";
		} else {
 		if($sort == 1) {
			$sorder = "ASC";
		} else {
			$sorder = "DESC";
		}
		}
		if($sortcolumn == 'Product_Desc') {
			$sortcolumn = DB::raw('CAST(GRN.Product_Desc AS NVARCHAR(500))');
		}
		
		$supplierObj = Supplier::select("code", "name")->where(["id" => $supplierId])->first();
		$name = !empty($supplierObj)  && isset($supplierObj->name) ? $supplierObj->name : "";
		$code = !empty($supplierObj)  && isset($supplierObj->code) ? $supplierObj->code : "";
		
		$rowCnt =  DB::table('GRN')
						->where('Supplier_Code', $code)->where('Supplier_Name', $name)
			->count();
		
		$supplier_with_grn = DB::table('GRN')
						->where('Supplier_Code', $code)->where('Supplier_Name', $name)
			->select('Supplier_Code', 'Grn_No', 'Grn_Qty','Receipt_Date','Product_Id_Main','Product_Code',"Product_Desc", "Pack_Size",DB::raw('CAST(Grn_Price AS DECIMAL(10,2)) AS Grn_Price'))->orderBy($sortcolumn, $sorder)
	->limit($limit)->offset(($page - 1) * $limit)
	->get();
		$gdata["grn"] = $supplier_with_grn;
		$gdata["rowCnt"] = $rowCnt;
		
       return $gdata;
    }

}
