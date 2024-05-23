<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\GRN;
use App\Models\DwGRN;
use App\Models\Supplier;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ClDesProduct;
use App\Models\DwProduct;

/**
 * This class file is created to write a function to import GRN data from staging to main table of product portal
 */
class ImportGRN extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:grn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands fetches latest records from dw_GRN data and stores in main table "GRN" of product portal';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', -1);
		$grn = [];
        $time_start = microtime(true);
        $insertCnt = $ltstGrnStagingCnt = $updateCnt = 0;
        echo PHP_EOL . "Process Started ..... " . PHP_EOL;

		
		$lastYear = Carbon::now()->subMonth(12)->format('Y-m-d');
		
        $grnData = DwGRN::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=', 'staging.dw_GRN.Product_Id')
                ->leftJoin('staging.dw_Supplier', 'dw_Supplier.Supplier_Id', '=', 'dw_GRN.Supplier_Id')
			->where("dw_GRN.Receipt_Date", '>', $lastYear)
                ->select("dw_GRN.GRN_Id","dw_GRN.Company_Id","dw_GRN.Supplier_Id","dw_GRN.Product_Id","dw_Product.Product_AC_4","dw_GRN.Grn_No","dw_GRN.Grn_Qty","dw_GRN.Grn_Price","dw_GRN.Receipt_Date","dw_GRN.Inserted_By","dw_GRN.Updated_By","dw_Supplier.Supplier_Code", "dw_Supplier.Supplier_Name","dw_Product.Product_Code")->get()->toArray();

		 foreach ($grnData as $grnDataItem) {
			  $scode = !empty($grnDataItem['Supplier_Code']) && isset($grnDataItem['Supplier_Code']) ? trim($grnDataItem['Supplier_Code']) : '';
$sname = !empty($grnDataItem['Supplier_Name']) && isset($grnDataItem['Supplier_Name']) ? trim($grnDataItem['Supplier_Name']) : '';
			 $supplierIdMain = Supplier::where(['code' => $scode,'name' =>$sname,'company_id' => '207791'])->pluck('id')->first();
			 
			  $GRN_Id = !empty($grnDataItem['GRN_Id']) && isset($grnDataItem['GRN_Id']) ? trim($grnDataItem['GRN_Id']) : '';
			  $Company_Id = !empty($grnDataItem['Company_Id']) && isset($grnDataItem['Company_Id']) ? trim($grnDataItem['Company_Id']) : '';
			  $Supplier_Id = !empty($grnDataItem['Supplier_Id']) && isset($grnDataItem['Supplier_Id']) ? trim($grnDataItem['Supplier_Id']) : '';
			  $Product_Id = !empty($grnDataItem['Product_Id']) && isset($grnDataItem['Product_Id']) ? trim($grnDataItem['Product_Id']) : '';
			 $ProductAC4 = !empty($grnDataItem['Product_AC_4']) && isset($grnDataItem['Product_AC_4']) ? trim($grnDataItem['Product_AC_4']) : '';
			  $Product_Code = !empty($grnDataItem['Product_Code']) && isset($grnDataItem['Product_Code']) ? trim($grnDataItem['Product_Code']) : '';

 $product = Product::where(['ac4' => $ProductAC4])->select('prod_id','clean_description','product_desc','pack_size')->where("is_parent",1)->first();
			 $prodIdMain = !empty($product['prod_id']) && isset($product['prod_id']) ? trim($product['prod_id']) : '';
			 $product_desc = !empty($product['product_desc']) && isset($product['product_desc']) ? trim($product['product_desc']) : '';
			  $description = !empty($product['clean_description']) && isset($product['clean_description']) ? trim($product['clean_description']) : $product_desc;
$packSize =  !empty($product['pack_size']) && isset($product['pack_size']) ? trim($product['pack_size']) : '';
			
		if(empty($description)) {
			 $descriptionCl = ClDesProduct::where(['sm_analysis_code4' => $ProductAC4,'product_code' => $Product_Code])->select('clean_description')
				 ->first();
			$productDw = DwProduct::where(['Product_AC_4' => $ProductAC4,'Product_Code' => $Product_Code])
				->select('Product_Desc','Pack_Size')
				->first();
			 $descriptionDw = !empty($productDw['Product_Desc']) && isset($productDw['Product_Desc']) ? trim($productDw['Product_Desc']) : '';
			  $description = !empty($descriptionCl['clean_description']) && isset($descriptionCl['clean_description'])   ? trim($descriptionCl['clean_description']) : $descriptionDw;
			
	$packSize  = !empty($productDw['Pack_Size']) && isset($productDw['Pack_Size']) ? trim($productDw['Pack_Size']) : '';
			
			}
		
			  
			  $Grn_No = !empty($grnDataItem['Grn_No']) && isset($grnDataItem['Grn_No']) ? trim($grnDataItem['Grn_No']) : '';

$Grn_Qty = !empty($grnDataItem['Grn_Qty']) && isset($grnDataItem['Grn_Qty']) ? trim($grnDataItem['Grn_Qty']) : '';
		
			  $Grn_Price = !empty($grnDataItem['Grn_Price']) && isset($grnDataItem['Grn_Price']) ? trim($grnDataItem['Grn_Price']) : '';
			  $Receipt_Date = !empty($grnDataItem['Receipt_Date']) && isset($grnDataItem['Receipt_Date']) ? trim($grnDataItem['Receipt_Date']) : '';
			  $Inserted_By = !empty($grnDataItem['Inserted_By']) && isset($grnDataItem['Inserted_By']) ? trim($grnDataItem['Inserted_By']) : '';
$Updated_By = !empty($grnDataItem['Updated_By']) && isset($grnDataItem['Updated_By']) ? trim($grnDataItem['Updated_By']) : '';
			 
			  
			 $grn[] = [
        "GRN_Id" => $GRN_Id,
	"Company_Id" => $Company_Id,
	"Supplier_Id" => $Supplier_Id,
	"Supplier_Id_Main" =>  $supplierIdMain, // suppliers table id
	"Supplier_Code" => $scode,
"Supplier_Name" => $sname,
"Product_AC_4" => $ProductAC4,
	"Product_Id_Main" =>  $prodIdMain, //prod_id from table products
	"Product_Id" =>  $Product_Id,
	"Product_Code" => $Product_Code,
	//"Depot_Id" => $Depot_Id
	//"Foreign_Currency_Id" => $Foreign_Currency_Id
	//"PurchaseOrder_Id" => $PurchaseOrder_Id
	//"PurchaseOrder_Operator_Id" => $PurchaseOrder_Operator_Id
	"Grn_No" => $Grn_No,
	"Grn_Qty" => $Grn_Qty,
	//"Grn_Value" => $Grn_Value,
	"Grn_Price" => $Grn_Price,
	'Product_Desc' => $description,
 	'Pack_Size' => $packSize,
	//"GRN_Exchange_Rate" => $GRN_Exchange_Rate,
	//"Purchase_Order_No" => $Purchase_Order_No,
	//"Purchase_Order_Line_No" => $Purchase_Order_Line_No,
	//"Purchase_Order_Line_Desc" => $Purchase_Order_Line_Desc,
	//"PurchaseOrder_Value" => $PurchaseOrder_Value,
	//"PurchaseOrder_Qty" => $PurchaseOrder_Qty,
	//"PurchaseOrder_FC_Value" => $PurchaseOrder_FC_Value ,
	//"PurchaseOrder_Exchange_Rate" => $PurchaseOrder_Exchange_Rate,
	//"PurchaseOrder_Type" => $PurchaseOrder_Type,
	//"Master_Order_No" => $Master_Order_No,
	//"Sales_Order_No" => $Sales_Order_No,
	//"Sales_Order_Line_No" => $Sales_Order_Line_No,
	//"Sell_By_Date" => $Sell_By_Date,
	//"Due_Date" => $Due_Date,
	//"Order_Date" => $Order_Date,
	"Receipt_Date" => $Receipt_Date,
	//"Qty_Desc" => $Qty_Desc,
	//"Price_Desc" => $Price_Desc,
	//"Weight" => $Weight,
	//"Period" => $Period,
	//"Days_Late" => $Days_Late,
	//"Late_Qty" => $Late_Qty,
	//"Return_Qty" => $Return_Qty,
	//"Claim_Qty" => $Claim_Qty,
	//"Trans_Anal_6" => $Trans_Anal_6,
	//"Account_Year" => $Account_Year,
	//"Account_Month" => $Account_Month ,
	//"Cost_in_Currency" => ,
	"Insert_DateTime" => \Carbon\Carbon::now(),
	"Inserted_By" => $Inserted_By,
	"Updated_By" =>  $Updated_By
];
			
			 
			 
		 }
			//Truncate table
			DB::table('GRN')->truncate();
            //Get the total count of latest inserted from staging GRN table
            $stagingGRNCnt = count($grn);

            foreach (array_chunk($grn, (2100 / 19) - 2) as $chunk) {
                GRN::insert($chunk);
            }
            echo PHP_EOL . "Inserted fresh  $stagingGRNCnt records" . PHP_EOL;
         
    }

}
