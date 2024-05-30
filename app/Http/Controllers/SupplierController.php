<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\BaseController;

class SupplierController extends BaseController
{

    /**
     * Displays a listing of the suppliers.
     *
     * @param integer $page Page number
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page,  $sortcolumn, $sort)
    {
		
		$sdata = [];
		$sortcolumn;
		$limit = 30;
		$sortcolumn = trim($sortcolumn);
		
		if(empty($sortcolumn)) {
			$sortcolumn = 'Supplier_Code';
			$sorder = "ASC";
		} else {
 		if($sort == 1) {
			$sorder = "ASC";
		} else {
			$sorder = "DESC";
		}
		}
		if($sortcolumn == 'Supplier_Name') {
			$sortcolumn = 'name';
		} else if($sortcolumn == 'Supplier_Code') {
			$sortcolumn = 'code';
		} else if($sortcolumn == 'Supplier_Type') {
			$sortcolumn = 'type';
		} 


		
		$rowCnt = Supplier::where('type',1)->count();
	
		
$suppliers = Supplier::select('id as Supplier_Id', 'name as Supplier_Name', 'code as Supplier_Code')->where('type',1)
	->orderBy($sortcolumn, $sorder)
	->limit($limit)->offset(($page - 1) * $limit)
	->get();
		
		$sdata["suppliers"] = $suppliers;
		$sdata["rowCnt"] = $rowCnt;

       
        if (count($suppliers) > 0) {
            return $this->sendResponse($sdata, 'Suppliers retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier data found']);
        }

        
    }
	
	
	 /**
     * Displays a listing of the suppliers.
     *
     * @param integer $page Page number
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomers($page,  $sortcolumn, $sort)
    {
		
		$sdata = [];
		$sortcolumn;
		$limit = 30;
		$sortcolumn = trim($sortcolumn);
		
		if(empty($sortcolumn)) {
			$sortcolumn = 'Customer_Code';
			$sorder = "ASC";
		} else {
 		if($sort == 1) {
			$sorder = "ASC";
		} else {
			$sorder = "DESC";
		}
		}
		if($sortcolumn == 'Customer_Name') {
			$sortcolumn = 'name';
		} else if($sortcolumn == 'Customer_Code') {
			$sortcolumn = 'code';
		}


		
		$rowCnt = Supplier::where('type',2)->count();
	
		
$customers = Supplier::select('id as Customer_Id', 'name as Customer_Name', 'code as Customer_Code')->where('type',2)
	->orderBy($sortcolumn, $sorder)
	->limit($limit)->offset(($page - 1) * $limit)
	->get();
		
		$sdata["customers"] = $customers;
		$sdata["rowCnt"] = $rowCnt;

       
        if (count($customers) > 0) {
            return $this->sendResponse($sdata, 'Customers retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any customer data found']);
        }

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Gets the supplier header and pricing details of respective supplier
     *
     * @param int $supplierId The supplier Id
     *
     * @return json The Json Array of matching pricing data of Supplier
     */
    public function showSupplierDetails($supplierId, $page, $sortcolumn = NULL, $sort = NULL)
    {
        $supplierId = (int) $supplierId;
        $data = [];
		$sdata = Supplier::getSupplierWithProduct($supplierId, $page, $sortcolumn, $sort);
		$products = !empty($sdata["products"]) && isset($sdata["products"]) ? $sdata["products"] : '';
		$rowCount = !empty($sdata["rowCount"]) && isset($sdata["rowCount"]) ? $sdata["rowCount"] : '';
        $data['supplier'] = Supplier::getSupplier($supplierId);
        $data['pricing'] = $products;
		$data['rowCount'] = $rowCount;
        if (!empty($data)) {
            return $this->sendResponse($data, 'Supplier pricing details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier pricing details found']);
        }
    }

    /**
     * Gets the supplier's PO details
     *
     * @param int $supplierId The supplier Id
     *
     * @return json The Json Array of matching PO data of Supplier
     */
    public function showSupplierPoDetails($supplierId)
    {
        $supplierId = (int) $supplierId;
        $data = [];
        $data['po'] = Supplier::getSupplierWithPo($supplierId);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Supplier PO details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier po details found']);
        }
    }

    /**
     * Gets GRN data of respective supllier
     *
     * @param int $supplierId The supplier Id
     *
     * @return json The Json Array of matching pricing and PO data of Supplier
     */
    public function getSupplierGRNData($supplierId)
    {
        $supplierId = (int) $supplierId;
        $data = [];

        $data['grn'] = Supplier::getSuppliergrn($supplierId);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Supplier GRN retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier GRN details found']);
        }
    }

    /**
     * Gets the details of the products of different suppliers
     *
     * @param int $supplierId The supplier Id
     *
     * @return json The Json Array of matching pricing and PO data of Supplier
     */
    public function suppProductData($productId)
    {
        $productId = (int) $productId;
        $data = [];

        $data['products'] = Supplier::getSupplierWithProductData($productId);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product and supplier details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product and supplier details found']);
        }
    }

    /**
     * API to search supplier
     * Search supplier by keyword of supplier name or code

     * @param string $term The search Parameter
     *
     * @return json The Json Array of Supplier Matching list
     */
    public function searchSupplier($keyword)
    {
        $suppliers = DB::table('suppliers')
                        ->where(['company_id' => '207791'])
                        ->where(function ($query) use ($keyword) {
                            $query->where('code', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('name', 'LIKE', '%' . $keyword . '%');
                        })->select('id', 'name', 'code')->get(); // Removed Space from right side and left side from column

        return response()->json(["supplier" => $suppliers]);
    }

    /**
     * Other supplier page for specific product code and supplier
     * On Product Page Price cature section inside Supplier Pricing Source, when user click on any supplier code
     * it will redirect to other page where the pricing and PO details for respective supplier and product would be shown
     *
     * @param id $supplierId The supplier id
     * @param id $productId The Aggregate product id
     *
     * @return json The Json Array of matching pricing and PO data
     */
    public function getSupplierProductPcPoData($supplierId, $productId = NULL)
    {
        $supplierId = (int) $supplierId;
        $productId = (int) $productId;
        $data = [];

        $data['supplier'] = Supplier::getSupplierHeader($supplierId, $productId);
        $data['supplier_with_product'] = Supplier::getPricingDetailsOfSupplier($supplierId, $productId);
        $data['supplier_with_po'] = Supplier::getPODetailsOfSupplier($supplierId, $productId);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Supplier Pricing and PO details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier details found']);
        }
    }

}