<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Supplier;
use Auth;

class Supplier extends Model
{

    use Notifiable;

    public $table = 'suppliers';
    public $primaryKey = 'id';

    // public $fillable = [
    // 	'Company_Id',
    // 	'Currency_Id',
    // 	'Supplier_Code',
    // 	'Supplier_Name',
    // 	'Supplier_Add1',
    // 	'Supplier_Add2',
    // 	'Supplier_Add3',
    // 	'Supplier_PostCOde',
    // 	'Supplier_Contact',
    // 	'Supplier_TelNo',
    // 	'Supplier_Email',
    // 	'Buyer_Code',
    // 	'Group_Code',
    // 	'Category_Code',
    // 	'Supplier_Contact_TelNo',
    // 	'Supplier_Contact_Mobile',
    // 	'Supplier_Contact_Email',
    // 	'Stop_Ind',
    // 	'LastPaid_Date',
    // 	'LastPaid_Amount',
    // 	'Inserted_DateTime',
    // 	'Updated_DateTime',
    // 	'Inserted_By',
    // 	'Updated_By',
    // ];
	
	protected $fillable = [
        'id',
        'code',
        'name'
    ];

    /**
     * Get Supplier Code and Name
     *
     * @param int $supplierId supplier id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSupplier($supplierId)
    {
        $supplier = DB::table('suppliers')->where('id', $supplierId)->select('name', 'code')->first(); //Suppliername and Code
        return $supplier;
    }

    /**
     * Get Supplier with its Mapping Products
     * @param int $supplierId supplier id
	 * @param integer $page The Page Number
     * @return \Illuminate\Http\Response 
     */
  public static function getSupplierWithProduct($supplierId, $page, $sortcolumn, $sort) //, $sort
    {
        //$sort = (int) $sort;
       $col = $sortcolumn;
		$rowCount = 0;
        //$supplier = DB::table('suppliers')->where('Supplier_id', $supplierId)->select('name', 'code')->first();
        //$supplierName = !empty($supplier) && isset($supplier->Supplier_Name) ? trim($supplier->Supplier_Name) : '';
        // $supplierCode = !empty($supplier) && isset($supplier->Supplier_Code) ? trim($supplier->Supplier_Code) : '';
        //$pricingSupplierId = DB::table('suppliers')->where(['name' => $supplierName, 'code' => $supplierCode])->pluck('id')->first();
		$sortcolumn = trim($sortcolumn);
		if(empty($sortcolumn)) {
			$sortcolumn = 'pricing_data.price_from_date';
			$sorder = "DESC";
		} else {
 		if($sort == 1) {
			$sorder = "ASC";
		} else {
			$sorder = "DESC";
		}
			 switch ($sortcolumn) {
            case 'productcode':
		$sortcolumn = 'pricing_data.parent_product_code';
			break;
			 case 'child_code':
		$sortcolumn = 'pricing_data.product_code';
			break;
				 case 'price':
		$sortcolumn = 'pricing_data.price';
			break;
				 case 'negotiated_price':
		$sortcolumn = 'pricing_data.negotiated_price';
			break;
					 case 'price_from_date':
		$sortcolumn = 'pricing_data.price_from_date';
			break;
					 case 'price_untill_date':
		$sortcolumn = 'pricing_data.price_untill_date';
			break;
				 case 'forecast':
		$sortcolumn = 'pricing_data.forecast';

 			//case 'description':
		//$sortcolumn = DB::raw('CAST(products.clean_description AS NVARCHAR(500))');
		//break;

		case 'description':
		$sortcolumn = 'pricing_data.parent_product_code';
		break;
					 
            default:
                break;
        }
		}

		$limit = 30;
        $today = date("Y-m-d");

        $sourceId = 10; // Source = Supplier Pricing
        $lastDayofPreviousMonth = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString(); //Getting last date of previous month
		 $last1Month = date('Y-m-d', strtotime('last day of -1 month'));
		  $last2Month = date('Y-m-d', strtotime('last day of -2 month'));
	  $last3Month = date('Y-m-d', strtotime('last day of -3 month'));
	  $negotiatedProducts = [];
		 $negotiatedProducts = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
						 ->where('pricing_data.price_type', 3)
                        ->where('pricing_data.supplier_id', $supplierId)
                       ->where('price_from_date', '>', $last1Month)->get('pricing_data.id as pcid')->toArray();
	  $negotiatedProducts = array_column($negotiatedProducts, "pcid");

	

	$pricingRecords = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')
->select('pricing_data.id as pcid', 'pricing_data.source_id', 'products.prod_id as product_id', 'pricing_data.supplier_id', 'pricing_data.parent_product_code as productcode', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(pricing_data.negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), 'pricing_data.forecast', 'products.clean_description as description', 'products.product_desc','pricing_data.comments', 'pricing_data.product_code as child_code', 'pricing_data.price_from_date', 'pricing_data.price_untill_date')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
					 	->whereNull('pricing_data.price_type')
                        //->where('price_from_date', '>', $last1Month)
                        ->orderBy($sortcolumn, $sorder)
						->limit($limit)->offset(($page - 1) * $limit)
                        ->get();
	

	  if(!empty($negotiatedProducts)) {

		$supplier_with_product = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')
->select('pricing_data.id as pcid', 'pricing_data.source_id', 'products.prod_id as product_id', 'pricing_data.supplier_id', 'pricing_data.parent_product_code as productcode', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(pricing_data.negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), 'pricing_data.forecast', 'products.clean_description as description', 'products.product_desc','pricing_data.comments', 'pricing_data.product_code as child_code', 'pricing_data.price_from_date', 'pricing_data.price_untill_date')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
					 	//->whereNull('pricing_data.price_type')
                       // ->where('price_from_date', '>', $last1Month)
						->orWhereIn('pricing_data.id', $negotiatedProducts)
                        ->orderBy($sortcolumn, $sorder)
						->limit($limit)->offset(($page - 1) * $limit)
                        ->get();


	  } else {
		 		$supplier_with_product = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')
->select('pricing_data.id as pcid', 'pricing_data.source_id', 'products.prod_id as product_id', 'pricing_data.supplier_id', 'pricing_data.parent_product_code as productcode', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(pricing_data.negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), 'pricing_data.forecast', 'products.clean_description as description', 'products.product_desc','pricing_data.comments', 'pricing_data.product_code as child_code', 'pricing_data.price_from_date', 'pricing_data.price_untill_date')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
                       //->where('price_from_date', '>', $last1Month)
                        ->orderBy($sortcolumn, $sorder)
						->limit($limit)->offset(($page - 1) * $limit)
                        ->get(); 
	  }
        
			$supplier_with_product = self::checkVisitedProduct($supplier_with_product, $col, $sorder);
		
		  $rowCount = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')->select('pricing_data.id as pcid')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
                       //->where('price_from_date', '>', $last1Month)
			  			->whereNull('pricing_data.price_type')
                        ->count();
	
		$rowCount = $rowCount + count($negotiatedProducts);
	
        if (empty($pricingRecords) || count($pricingRecords) == 0) {


		
				  $supplier_with_product = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')
->select('pricing_data.id as pcid', 'pricing_data.source_id', 'products.prod_id as product_id', 'pricing_data.supplier_id', 'pricing_data.parent_product_code as productcode', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(pricing_data.negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), 'pricing_data.forecast', 'products.clean_description as description', 'products.product_desc','pricing_data.comments', 'pricing_data.product_code as child_code', 'pricing_data.price_from_date', 'pricing_data.price_untill_date')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
                       //->where('price_from_date', '>', $last2Month)
                        ->orderBy($sortcolumn, $sorder)
						->limit($limit)->offset(($page - 1) * $limit)
                        ->get();
		
			$supplier_with_product = self::checkVisitedProduct($supplier_with_product, $col, $sorder);
		
		  $rowCount = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')->select('pricing_data.id as pcid')
                         ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
                       ->where('price_from_date', '>', $last2Month)
                        ->count();

			//$rowCount = $rowCount + count($negotiatedProducts2M);
        }
	  
	     if (empty($pricingRecords) || count($pricingRecords) == 0) {


		
				  $supplier_with_product = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')
->select('pricing_data.id as pcid', 'pricing_data.source_id', 'products.prod_id as product_id', 'pricing_data.supplier_id', 'pricing_data.parent_product_code as productcode', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(pricing_data.negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), 'pricing_data.forecast', 'products.clean_description as description', 'products.product_desc','pricing_data.comments', 'pricing_data.product_code as child_code', 'pricing_data.price_from_date', 'pricing_data.price_untill_date')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
                      // ->where('price_from_date', '>', $last3Month)
                        ->orderBy($sortcolumn, $sorder)
						->limit($limit)->offset(($page - 1) * $limit)
                        ->get();
		
			$supplier_with_product = self::checkVisitedProduct($supplier_with_product, $col, $sorder);
		
		  $rowCount = DB::table('pricing_data')->join('products', 'pricing_data.product_code', '=', 'products.product_code')->select('pricing_data.id as pcid')
                         ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 0)
                        ->where('pricing_data.supplier_id', $supplierId)
                       ->where('price_from_date', '>', $last3Month)
                        ->count();

			//$rowCount = $rowCount + count($negotiatedProducts2M);
        }
	


        $data = [];
	  
       // foreach ($supplier_with_product as $key => $p) {
            /* if (!isset($supplier_with_product[$p->productcode])) {
              if (empty($p->productcode)) {
              $p->productcode = " - ";
              }
              } */
           // $products[$p->pcid] = $p;
       // }
        //ksort($products);
       // $products = array_values($products);
	
		$data = ["products" => $supplier_with_product, "rowCount" => $rowCount];
        return $data;
    }
	
	private static function checkVisitedProduct($products, $col, $sorder) {

			    $userId = Auth::user()->id;
				$pricingItems = [];
			foreach ($products as $key => $p) {
				$pricingId = !empty($p->pcid) && isset($p->pcid) ? trim($p->pcid) : '';
				
				$isVisited = DB::table('user_visited_products')->where("user_id",$userId)->where("pricing_id",$pricingId)
								->whereNull("expired_at")->first("id");

				if(!empty($isVisited)) {
					$p->is_visited = 1;
				} else {
					$p->is_visited = 0;
				}
			if (empty($p->description)) {
				$p->description = $p->product_desc;
			}
				$pricingItems[] = $p;
			}
		
		if($col == 'description') {
	$desc = array_column($pricingItems, 'description');
		if($sorder == "ASC") {
			$sorder = SORT_ASC;
		} else {
			$sorder = SORT_DESC;
		}

		array_multisort($desc, $sorder, $pricingItems);
		}
		return $pricingItems;
	}


    /**
     * Get Supplier with its Mapping Products
     * @param int $supplierId supplier id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSupplierWithProductData($productId)
    {
        $today = date("Y-m-d");
        $sourceId = 10; // Source = Supplier Pricing
        $lastDayofPreviousMonth = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString(); //Getting last date of previous month
        $prevOneMon = date('Y-m-d', strtotime('-1 month'));
        $prevTwoMon = date('Y-m-d', strtotime('-2 month'));
        $prevThreeMon = date('Y-m-d', strtotime('-3 month'));

        $supplier_with_product = DB::table('pricing_data')
                        ->leftJoin('suppliers', 'pricing_data.supplier_id', '=', 'suppliers.id')
                        ->join('products', 'pricing_data.parent_product_code', '=', 'products.ac4')
                        ->select('products.product_code', 'suppliers.code as supplier_code', 'pricing_data.supplier_id', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), 'pricing_data.forecast', 'pricing_data.price_untill_date')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 1)
                        ->where('products.prod_id', $productId)
                        ->where('pricing_data.price_from_date', '>', $lastDayofPreviousMonth)
                        ->orderBy('pricing_data.price_untill_date', 'DESC')
                        ->get()->toArray();

        $supplier_with_product = DB::table('pricing_data')
                        ->leftJoin('suppliers', 'pricing_data.supplier_id', '=', 'suppliers.id')
                        ->join('products', 'pricing_data.parent_product_code', '=', 'products.ac4')
                        ->select('products.product_code', 'suppliers.code as supplier_code', 'pricing_data.supplier_id', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), 'pricing_data.forecast', 'pricing_data.price_untill_date')
                        ->where('pricing_data.source_id', $sourceId)
                        ->where('products.is_parent', 1)
                        ->where('products.prod_id', $productId)
                        ->where('pricing_data.price_from_date', '>', $prevOneMon)
                        ->orderBy('pricing_data.price_untill_date', 'DESC')
                        ->get()->toArray();  //return Supplier Data with its products


        if (empty($supplier_with_product)) {

            $supplier_with_product = DB::table('pricing_data')
                            ->leftJoin('suppliers', 'pricing_data.supplier_id', '=', 'suppliers.id')
                            ->join('products', 'pricing_data.parent_product_code', '=', 'products.ac4')
                            ->select('products.product_code', 'suppliers.code as supplier_code', 'pricing_data.supplier_id', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), 'pricing_data.forecast', 'pricing_data.price_untill_date')
                            ->where('pricing_data.source_id', $sourceId)
                            ->where('products.is_parent', 1)
                            ->where('products.prod_id', $productId)
                            ->where('pricing_data.price_from_date', '>', $prevTwoMon)
                            ->orderBy('pricing_data.price_untill_date', 'DESC')
                            ->get()->toArray();
        }
        if (empty($supplier_with_product)) {

            $supplier_with_product = DB::table('pricing_data')
                            ->leftJoin('suppliers', 'pricing_data.supplier_id', '=', 'suppliers.id')
                            ->join('products', 'pricing_data.parent_product_code', '=', 'products.ac4')
                            ->select('products.product_code', 'suppliers.code as supplier_code', 'pricing_data.supplier_id', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), 'pricing_data.forecast', 'pricing_data.price_untill_date')
                            ->where('pricing_data.source_id', $sourceId)
                            ->where('products.is_parent', 1)
                            ->where('products.prod_id', $productId)
                            ->where('pricing_data.price_from_date', '>', $prevThreeMon)
                            ->orderBy('pricing_data.price_untill_date', 'DESC')
                            ->get()->toArray();
        }

        return $supplier_with_product;
    }

    /**
     * Gets the Purchase Orders of Supplier
     *
     * @param id $supplierId The supplier id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSupplierWithPo($supplierId)
    {
        //  $lastDayofPreviousMonth = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString(); //Getting last date of previous month
        $supplier_with_po = $pos = [];
        $now = date('Y-m-d H:i:s');
        $fiveWeeksAgo = date('Y-m-d H:i:s', strtotime('-5 weeks'));

        /* $supplier_with_po = DB::table('purchase_order_items')->join('products', 'purchase_order_items.product_id', '=', 'products.prod_id')->where('supplier_id', $supplierId)->where('purchase_order_items.created_at', '>', $lastDayofPreviousMonth)->select('purchase_order_items.po_item_id', 'products.product_code as child_code', 'purchase_order_items.po_item_id as note', 'purchase_order_items.quantity', DB::raw('CAST(purchase_order_items.price AS DECIMAL(10,2)) AS price'), 'purchase_order_items.created_at')->orderBy('purchase_order_items.created_at', 'DESC')->get(); //Supplier with Purchase order */
        $supplier_with_po = DB::table('purchase_orders as po')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'po.supplier_id')
                ->leftJoin("users as u", "u.id", "=", "po.inserted_by")
				->leftJoin("users as us", "us.id", "=", "po.lastchanged_by")
				->leftJoin("users as v", "v.id", "=", "po.verified_by")
                ->whereBetween('po.created_at', [$fiveWeeksAgo, $now])
                ->where('po.supplier_id', '=', $supplierId)
                ->select('po.po_id', 'po.po_ref_id as po_num', 'suppliers.id as supplier_id', 'suppliers.code', 'po.notes', 'po.status', 'po.created_at', DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"), 'po.updated_at',DB::raw("CONCAT(us.firstname,' ',us.lastname) AS updated_by"),'po.verified_at',DB::raw("CONCAT(v.firstname,' ',v.lastname) AS verified_by"),  'po.is_downloaded')
                ->orderBy('po.created_at', 'DESC')
                ->get();
        foreach ($supplier_with_po as $item) {
            $pos[$item->po_id] = $item;
        }
        return array_values($pos);
    }

    /**
     * Gets the GRN of Supplier
     *
     * @param id $supplierId The supplier id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSuppliergrn($supplierId)
    {
        $lastDayofPreviousMonth = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString(); //Getting last date of previous month
        $supplierObj = Supplier::select("code", "name")->where(["id" => $supplierId])->first();
        $name = !empty($supplierObj) && isset($supplierObj->name) ? $supplierObj->name : "";
        $code = !empty($supplierObj) && isset($supplierObj->code) ? $supplierObj->code : "";
        /* $supplier_with_grn = DB::table('GRNMain')->leftJoin('staging.dw_Product', 'GRNMain.Product_id', '=', 'staging.dw_Product.Product_id')->leftJoin('staging.dw_Supplier', 'dw_Supplier.Supplier_Id', '=', 'GRNMain.Supplier_Id')
          ->where('Supplier_Code', $code)->where('Supplier_Name', $name)->where('GRNMain.Receipt_Date', '>', $lastDayofPreviousMonth)
          ->select('dw_Supplier.Supplier_Code', 'GRNMain.Grn_No', 'GRNMain.Grn_Qty','GRNMain.Receipt_Date','staging.dw_Product.Product_Code',DB::raw('CAST(GRNMain.Grn_Price AS DECIMAL(10,2)) AS price'))->orderBy('price', 'DESC')->get(); *///Supplier with GRN
        $supplier_with_grn = DB::table('GRN')
                        ->where('Supplier_Code', $code)
                        ->where('Supplier_Name', $name)
                        ->where('GRN.Receipt_Date', '>', $lastDayofPreviousMonth)
                        ->select('Supplier_Code', 'Grn_No', 'Grn_Qty', 'Receipt_Date', 'Product_Code', 'Product_Desc', 'Pack_Size', DB::raw('CAST(Grn_Price AS DECIMAL(10,2)) AS price'))->orderBy('Receipt_Date', 'DESC')->get();

        if (empty($supplier_with_grn) || count($supplier_with_grn) < 1) {
            $supplier_with_grn = DB::table('GRN')
                            ->where('Supplier_Code', $code)
                            ->where('Supplier_Name', $name)
                            ->select('Supplier_Code', 'Grn_No', 'Grn_Qty', 'Receipt_Date', 'Product_Code', 'Product_Desc', 'Pack_Size', DB::raw('CAST(Grn_Price AS DECIMAL(10,2)) AS price'))->orderBy('Receipt_Date', 'DESC')->limit(25)->get();
        }



        return $supplier_with_grn; // return Supplier GRN
    }

    /**
     * Gets Product and Supplier Mapping Data
     *
     * @param int $supplierid supplier id
     * @param int $productId The AC4
     *
     * @return \Illuminate\Http\Response
     */
    /* public static function getProductWithSupplier($supplierid,$productcode)
      {
      $supplier_with_po = DB::table('purchase_order')->where('supplier_id', $supplierid AND 'parent_product_code', $productcode)->select('po_id', 'product_id as child_code', 'notes', 'suggested_quantity', 'preferred_price', 'created_at')->get(); //Product and Supplier Data Retrive
      return $supplier_with_po;
      } */

    /**
     * Get Supplier Code and Name
     *
     * @param id $supplierId The supplier id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSupplierHead($supplierId)
    {
        $header = [];

        $pricingSupplier = self::getPricingSupplier($supplierId);
        $pricingSupplierName = !empty($pricingSupplier) && isset($pricingSupplier->name) ? trim($pricingSupplier->name) : '';
        $pricingSupplierCode = !empty($pricingSupplier) && isset($pricingSupplier->code) ? trim($pricingSupplier->code) : '';

        $header = ['Supplier_Name' => $pricingSupplierName, 'Supplier_Code' => $pricingSupplierCode];
        return $header;
    }

    /**
     * Get Supplier Code and Name and Aggregate product code
     *
     * @param id $supplierId The supplier id
     * @param id $productId The Aggregate product id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSupplierHeader($supplierId, $productId)
    {
        $header = [];

        $pricingSupplier = self::getPricingSupplier($supplierId);
        $pricingSupplierName = !empty($pricingSupplier) && isset($pricingSupplier->name) ? trim($pricingSupplier->name) : '';
        $pricingSupplierCode = !empty($pricingSupplier) && isset($pricingSupplier->code) ? trim($pricingSupplier->code) : '';

        // $supplier = DB::table('suppliers')->where(['Supplier_Name' => $pricingSupplierName, 'Supplier_Code' => $pricingSupplierCode])->select('Supplier_Name', 'Supplier_Code')->first(); //Suppliername and Code
        $acCode = DB::table('products')->where('prod_id', $productId)->pluck('products.ac4')->first();
        //$supplierName = !empty($supplier) && isset($supplier->Supplier_Name) ? trim($supplier->Supplier_Name) : '';
        // $supplierCode = !empty($supplier) && isset($supplier->Supplier_Code) ? trim($supplier->Supplier_Code) : '';

        $header = ['Supplier_Name' => $pricingSupplierName, 'Supplier_Code' => $pricingSupplierCode, 'AC4' => $acCode];
        return $header;
    }

    /**
     * Get Supplier with its Mapping Products
     *
     * @param id $supplierId The supplier id
     * @param id $productId The Aggregate product id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPricingDetailsOfSupplier($supplierId, $productId)
    {
        $acCode = DB::table('products')->where('prod_id', $productId)->pluck('products.ac4')->first();

        //$lastDayofPreviousMonth = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString(); //Getting last date of previous month
        $today = date("Y-m-d");

        $products = DB::table('pricing_data')->select('pricing_data.source_id', DB::raw("$productId as product_id"), 'pricing_data.supplier_id', 'pricing_data.parent_product_code as productcode', DB::raw('CAST(pricing_data.price AS DECIMAL(10,2)) AS price'), 'pricing_data.forecast', 'products.clean_description as description', 'pricing_data.comments', 'pricing_data.product_code as child_code', 'pricing_data.price_from_date')->leftjoin('products', 'pricing_data.parent_product_code', '=', 'products.ac4')->where('products.is_parent', 1)->where('pricing_data.supplier_id', $supplierId)->where('pricing_data.parent_product_code', $acCode)->where('price_untill_date', '>', $today)->orderBy('pricing_data.price_untill_date', 'DESC')
                ->get();  //return Supplier Data with its products
		



        return $products;
    }

    /**
     * Get Supplier with its Mapping PO
     *
     * @param id $supplierId The supplier id
     * @param id $productId The Aggregate product id
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPODetailsOfSupplier($supplierId, $productId)
    {
        //$pricingSupplier = self::getPricingSupplier($supplierId);
        //$pricingSupplierName = !empty($pricingSupplier) && isset($pricingSupplier->name) ? trim($pricingSupplier->name) : '';
        //$pricingSupplierCode = !empty($pricingSupplier) && isset($pricingSupplier->code) ? trim($pricingSupplier->code) : '';
        //Gets the supplier Id of product portal
        //  $supplierIdPP = DB::table('suppliers')->where(['Supplier_Name' => $pricingSupplierName, 'Supplier_Code' => $pricingSupplierCode])->pluck('Supplier_Id')->first();

        $acCode = DB::table('products')->where('prod_id', $productId)->pluck('products.ac4')->first();
        //Gets the child product of provided parent
        $children = DB::table('products')->where('ac4', $acCode)->pluck('prod_id')->toArray();
        //dd($children);
        $lastDayofPreviousMonth = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString(); //Getting last date of previous month

        /* $supplier_with_po = DB::table('purchase_orders')->join('products', 'purchase_orders.product_id', '=', 'products.prod_id')->where('supplier_id', $supplierId)->whereIn('product_code', $children)->where('purchase_orders.created_at', '>', $lastDayofPreviousMonth)->select('purchase_orders.po_id', 'products.product_code as child_code', 'purchase_orders.notes', 'purchase_orders.quantity', DB::raw('CAST(purchase_orders.price AS DECIMAL(10,2)) AS price'), 'purchase_orders.created_at')->orderBy('purchase_orders.created_at', 'DESC')->get(); *///Supplier with Purchase order

        /* $supplier_with_po = DB::table('purchase_orders')->join('products', 'purchase_orders.product_id', '=', 'products.prod_id')->where('supplier_id', $supplierId)->whereIn('product_code', $children)->select('purchase_orders.po_id', 'products.product_code as child_code', 'purchase_orders.notes', 'purchase_orders.quantity', DB::raw('CAST(purchase_orders.price AS DECIMAL(10,2)) AS price'), 'purchase_orders.created_at')->orderBy('purchase_orders.created_at', 'DESC')->get();

          return $supplier_with_po; */// return Supplier PO

        $pos = [];
        $supplier_with_po = DB::table('purchase_orders as po')->join('purchase_order_items', 'purchase_order_items.po_id', '=', 'po.po_id')
                ->leftJoin('products', 'purchase_order_items.product_id', '=', 'products.prod_id')
                ->select('po.po_id', 'po.po_ref_id as po_num', 'po.notes', 'po.status', 'po.created_at', DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
                ->leftJoin("users as u", "u.id", "=", "po.inserted_by")
                ->where('po.created_at', '>', $lastDayofPreviousMonth)
                ->where('purchase_order_items.supplier_id', $supplierId)
                ->whereIn('purchase_order_items.product_id', $children)
                ->orderBy('po.created_at', 'DESC')
                ->get();
        foreach ($supplier_with_po as $item) {
            $pos[$item->po_id] = $item;
        }
        return array_values($pos);
    }

    /*
     * Gets the supllier name and code from price capture table
     *
     * @param id $supplierId The supplier id
     *
     * @return \Illuminate\Http\Response
     */

    private static function getPricingSupplier($supplierId)
    {
        $pricingSupplier = null;
        $pricingSupplier = DB::table('suppliers')->where('id', $supplierId)->select('name', 'code')->first(); //Suppliername and Code
        return $pricingSupplier;
    }

}