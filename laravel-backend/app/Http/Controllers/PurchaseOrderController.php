<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderItem;
use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use App\Models\TempPoItem;
use App\Components\Helper;

/**
 * 
 */
class PurchaseOrderController extends BaseController
{
	
	
    /**
     * Product API which connects to Azure SQL Database and return json array of purchase order items
     *
     * @return json The purchase order Items json array
     */
    public function getPos()
    {
		$pos = [];
		$now = date('Y-m-d H:i:s');
		$ThreeWeeksAgo = date('Y-m-d H:i:s', strtotime('-3 weeks'));
	

		//Gets the 3 weeks ago created Pos
        $pos['latest'] = DB::table('purchase_orders as po')
			->leftJoin('suppliers as sup', 'sup.id', '=', 'po.supplier_id')
			->leftJoin("users as u", "u.id", "=", "po.inserted_by")
			->leftJoin("users as us", "us.id", "=", "po.lastchanged_by")
			->leftJoin("users as v", "v.id", "=", "po.verified_by")
			->select('po.po_id', 'sup.code', 'po.supplier_id', 'po.po_ref_id as po_num', 'po.notes', 'po.status', 'po.created_at',  DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"), 'po.updated_at',DB::raw("CONCAT(us.firstname,' ',us.lastname) AS updated_by"),  'po.verified_at',DB::raw("CONCAT(v.firstname,' ',v.lastname) AS verified_by"),  'po.is_downloaded')
					
				
				->whereBetween('po.created_at', [$ThreeWeeksAgo, $now])
                ->orderBy('po.created_at', 'DESC')
                ->get();
			//Gets all POs older than 3 weeks
		 $pos['historical'] = DB::table('purchase_orders as po')
			->join('suppliers as sup', 'sup.id', '=', 'po.supplier_id')
			->leftJoin("users as u", "u.id", "=", "po.inserted_by")
			 
			->select('po.po_id', 'sup.code', 'po.supplier_id', 'po.po_ref_id as po_num', 'po.notes', 'po.notes', 'po.created_at','po.status', DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
               
			 ->where('po.created_at', '<=', $ThreeWeeksAgo)
			   ->orderBy('po.created_at', 'DESC')
              ->get();
			//Get PO with status APPROVAL_PENDING
		 $pos['approval_pending'] = DB::table('purchase_orders as po')
			->join('suppliers as sup', 'sup.id', '=', 'po.supplier_id')
			->leftJoin("users as u", "u.id", "=", "po.inserted_by")
			 ->select('po.po_id', 'sup.code', 'po.supplier_id', 'po.po_ref_id as po_num', 'po.notes', 'po.notes', 'po.status', 'po.created_at',DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
               ->where('po.status', PurchaseOrder::PO_APPROVAL_PENDING)
			   ->orderBy('po.created_at', 'DESC')
              ->get();
       
        if (!empty($pos)) {
            return $this->sendResponse($pos, 'Purchase Order retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order data found']);
        }
    }
	
	
	  /**
     * Gets the recent POs completed in last 24 working hours
     *
     * @return json The purchase orders json array
     */
    public function getRecentPos()
    {
		$pos = [];
		
		$ids = TempPoItem::pluck("ids")->first();
		$ids = !empty($ids) ? explode(",",$ids) : [];

        $pos = DB::table('purchase_orders as po')
			->leftJoin('suppliers as sup', 'sup.id', '=', 'po.supplier_id')
			->leftJoin("users as u", "u.id", "=", "po.inserted_by")
			->select('po.po_id', 'sup.code', 'po.supplier_id', 'po.po_ref_id as po_num', 'po.notes', 'po.status', 'po.created_at',  DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
					->whereIn('po.po_id', $ids)
                ->orderBy('po.created_at', 'DESC')
                ->get();
       
        if (!empty($pos) && count($pos) > 0) {
            return $this->sendResponse($pos, 'Purchase Order retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order data found']);
        }
    }



  

    /**
     * Product API which connects to Azure SQL Database and return json array of purchase order items
     *
     * @return json The purchase order Items json array
     */
    public function index()
    {
		$pos = [];
		$now = date('Y-m-d H:i:s');
		$ThreeWeeksAgo = date('Y-m-d H:i:s', strtotime('-3 weeks'));
	

		//Gets the 3 weeks ago created Pos
        $pos['latest'] = DB::table('purchase_order_items as poitem')->select('poitem.po_id', 'poitem.po_item_id', 'p.product_code', 's.id as supplier_id', 's.code AS supplier', 'poitem.quantity', DB::raw('CAST(price AS DECIMAL(10,2)) AS price'),'poitem.created_at',  DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
                 ->join("products as p", "p.prod_id", "=", "poitem.product_id")
                 ->join("suppliers as s", "s.id", "=", "poitem.supplier_id")
				 ->leftJoin("users as u", "u.id", "=", "poitem.inserted_by")
				->whereBetween('poitem.created_at', [$ThreeWeeksAgo, $now])
                ->orderBy('poitem.created_at', 'DESC')
                ->get();
			//Gets all POs older than 3 weeks
		 $pos['historical'] = DB::table('purchase_order_items as poitem')->select('poitem.po_id', 'poitem.po_item_id', 'p.product_code', 's.id as supplier_id', 's.code AS supplier', 'poitem.quantity', DB::raw('CAST(price AS DECIMAL(10,2)) AS price'),'poitem.created_at as created_at')
                 ->join("products as p", "p.prod_id", "=", "poitem.product_id")
                 ->join("suppliers as s", "s.id", "=", "poitem.supplier_id")
				 ->leftJoin("users as u", "u.id", "=", "poitem.inserted_by")
			 ->where('poitem.created_at', '<=', $ThreeWeeksAgo)
			   ->orderBy('poitem.created_at', 'DESC')
              ->get();
	
		
       
        if (!empty($pos)) {
            return $this->sendResponse($pos, 'Purchase Order retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order data found']);
        }
    }
	
	
	
    /**
     * Product API which connects to Azure SQL Database and return json array of purchase order items
     *
     * @return json The purchase order Items json array
     */
    public function getPoItems()
    {
		$pos = [];
		$now = date('Y-m-d H:i:s');
		$ThreeWeeksAgo = date('Y-m-d H:i:s', strtotime('-3 weeks'));
	

		//Gets the 3 weeks ago created Pos
        $pos = DB::table('purchase_order_items as poitem')->select( 'poitem.po_item_id', 'p.product_code', 'p.clean_description as desc','p.product_desc','p.is_parent','p.prod_id','s.id as supplier_id', 's.code AS supplier', 'poitem.quantity', DB::raw('CAST(price AS DECIMAL(10,2)) AS price'),'poitem.created_at',  DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
                 ->join("products as p", "p.prod_id", "=", "poitem.product_id")
                 ->join("suppliers as s", "s.id", "=", "poitem.supplier_id")
				 ->leftJoin("users as u", "u.id", "=", "poitem.inserted_by")
				->whereNull('poitem.po_id')
				->whereBetween('poitem.created_at', [$ThreeWeeksAgo, $now])
                ->orderBy('poitem.created_at', 'DESC')
                ->get()->map(function ($pos) {
			if (empty($pos->desc)) {
				$pos->desc = $pos->product_desc;
			};
				if ($pos->is_parent == 1) {
				 $pos->prod_id = $pos->prod_id;
			};
			return $pos;
		});
		
       
        if (!empty($pos)) {
            return $this->sendResponse($pos, 'Purchase Order Items retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order Items data found']);
        }
    }
    
	
     /**
     * Product API which connects to Azure SQL Database and return json array of pending [status = 0] purchase order items
     *
     * @return json The purchase order Items json array
     */
    public function pendingPos()
    {
        $pos = DB::table('purchase_orders as po')->select('po.po_id', 'po.po_ref_id as po_num', 'po.created_at as raised_on',  DB::raw("CONCAT(u.firstname,' ',u.lastname) as raised_by"), 'po.status')
			->leftJoin("users as u", "u.id", "=", "po.inserted_by")
				->where('po.status', PurchaseOrder::PO_IN_PROGRESS)
                ->orderBy('po.created_at', 'DESC')
				->limit(15)
                ->get();
       
        if (!empty($pos)) {
            return $this->sendResponse($pos, 'Purchase Order retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order Itemsdata found']);
        }
    }
    

    /**
     * @param $id
     *
     * @return PurchaseOrder
     */
    public function show($poid)
    {	
		$pos = [];

		//Gets the 3 weeks ago created Pos
        $pos= DB::table('purchase_order_items as poitem')->select(DB::raw("$poid AS poid"), 'poitem.po_item_id', 'p.product_code', 'p.clean_description as desc','p.product_desc','p.is_parent','p.prod_id','p.ac4','s.id as supplier_id', 's.code AS supplier', 'poitem.quantity', DB::raw('CAST(price AS DECIMAL(10,2)) AS price'),'poitem.created_at',  DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
                 ->join("products as p", "p.prod_id", "=", "poitem.product_id")
                 ->join("suppliers as s", "s.id", "=", "poitem.supplier_id")
				 ->leftJoin("users as u", "u.id", "=", "poitem.inserted_by")
				->where('poitem.po_id',$poid)
				->orderBy('poitem.created_at', 'DESC')
                ->get()->map(function ($pos) {
			if (empty($pos->desc)) {
				$pos->desc = $pos->product_desc;
			};
			$parentP =  DB::table('products')
                    ->where('ac4', '=', $pos->ac4)->where('is_parent', 1)->select("prod_id")
                    ->first();
			$parentPId = !empty($parentP->prod_id) &&  isset($parentP->prod_id) ? $parentP->prod_id : '';
				
				 $pos->prod_id = $parentPId;
				
			return $pos;
			});
			
       
        if (!empty($pos) && count($pos) > 0) {
            return $this->sendResponse($pos, 'Purchase Order Items retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order Items data found']);
        }
    }

    /**
     * Creates Purchase order items
     *
     * @param \Illuminate\Http\Request $request Request with Product Id, Supplier Id, quantity, price
     * @throws AuthorizationException
	 *
     * @return void
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
		$poitems = !empty($requestData['poitems']) && isset($requestData['poitems']) ? $requestData['poitems'] : [];

        try {
			
            (new PurchaseOrderItem)->createPO($poitems);
        
            return $this->sendResponse('PO Items Created', 'Purchase Order Items created successfully.');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to create PO Items', 'Failed : ' . $error->getMessage(), 209);
        }
    }

	
	  /**
     * Add PO Items to the selected PO
     *
     * @param int $id Purchase Order Id|Primary Key column
     * 
     * @return void
     */
    public function addPOItems(Request $request, $poId)
    {
        $requestData = $request->all();
		$poitems = !empty($requestData['poitems']) && isset($requestData['poitems']) ? $requestData['poitems'] : [];

        try {
            (new PurchaseOrderItem)->addPOItems($poitems, $poId);
        
            return $this->sendResponse('PO Items Added', 'Purchase Order Items added successfully.');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to add PO Items', 'Failed : ' . $error->getMessage(), 209);
        }
    }
	
	
	private function countDigits($MyNum){
	  $MyNum = (int)abs($MyNum);
	  $MyStr = strval($MyNum);
	  return strlen($MyStr);
	}
	 /**
     * Creates Purchase order items
     *
     * @param \Illuminate\Http\Request $request Request with Product Id, Supplier Id, quantity, price
     * @throws AuthorizationException
	 *
     * @return void
     */
	 public function createPO(Request $request)
    {
       
        try {
		$requestData = $request->all();
		 $currentDateTime = \Carbon\Carbon::now();
        $insertedBy = \Auth::user()->id;
		$poitems = !empty($requestData['poitems']) && isset($requestData['poitems']) ? $requestData['poitems'] : [];
		$supplierId = PurchaseOrderItem::where("po_item_id", '=', $poitems)->pluck('purchase_order_items.supplier_id')->first();
												
	     //Get Last inserted PO id
        $lastPO = DB::table('purchase_orders')->orderBy('po_id', 'DESC')->pluck('po_id')->first();
        $lastPO = !empty($lastPO) ? $lastPO +1 : 1 ;
				
		$lastPOs = !empty($lastPO) ? array($lastPO) : [];
						
		$poID =  !empty($lastPO) ? Helper:: generatePONumber($lastPOs) : '';

		$poNumber = [];
			$existing =  PurchaseOrder::join('purchase_order_items', 'purchase_order_items.po_id', '=', 'purchase_orders.po_id')
				->whereIn("purchase_order_items.po_item_id", $poitems)
				->whereNotNull('purchase_order_items.po_id')
				->pluck('purchase_orders.po_ref_id')->first();

		if(!empty($existing)) {
			 return $this->sendResponse('PO Duplicate', 'Purchase Order number '. $existing. ' alredy available for same records.');
		}
			$itemsValid =  PurchaseOrderItem::whereIn("purchase_order_items.po_item_id", $poitems)->select('purchase_order_items.po_item_id')->get()->toArray();
					
			if(empty($itemsValid)) {
			 return $this->sendResponse('PO Items Invalid', 'Provided PO Items don\'t exist in the system.');
		}
	
			
		$po = PurchaseOrder::create([
                    'po_ref_id' => $poID,
                    'status' => PurchaseOrder::PO_IN_PROGRESS,
                    "created_at" => $currentDateTime,
               		"inserted_by" => $insertedBy,
					"supplier_id" => $supplierId
        ]);

		if(!empty($po)) {
			$poId = $po->po_id;
			$poNum = $po->po_ref_id;
			$poNumber = ['poNumber' => $poNum];
	  		DB::table('purchase_order_items')
                    ->whereIn("po_item_id", $poitems)
                    ->update(["po_id" => $poId]);
	}
        	   return $this->sendResponse($poNumber, 'Purchase Order raised successfully.');
   
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to create PO Items', 'Failed : ' . $error->getMessage(), 209);
        }
    }
	
	 /**
     * 	At the time of raising PO , we must see - if PO is completed in last 24 hours , and those that are not completed  and not 		expired without any time limit  - for the aggregate code
     *
     * @param \Illuminate\Http\Request $request Request with POIems Ids
	 *
     * @return void
     */
	public function checkPOExists(Request $request) {
		 try {
			 $response = FALSE;
			 $message = 'Recent PO doses not exist.';
			$requestData = $request->all();

			$poitems = !empty($requestData['poitems']) && isset($requestData['poitems']) ? $requestData['poitems'] : [];
			$existingPO  = (new PurchaseOrderItem)->checkExistingPO($poitems);
			 
			 if(!empty($existingPO) && count($existingPO) > 0) {
				 $response = TRUE;
				 $message = 'Recent PO exists.';
				}
			 return $this->sendResponse($response, $message);
			 } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to check if recent PO exists', 'Failed : ' . $error->getMessage(), 209);
        }
	}
	

		/**
     * 	Right now user can download a PO at a time. There should be provision to download mutiple POs at a time.
     *
     * @param \Illuminate\Http\Request $request Request with PO Ids
	 *
     * @return void
     */
	public function DownloadPos(Request $request) {
		
		 try {
			 $data = $poItems =  [];
			 $response = FALSE;
			 $filename = '';
			 $now = \Carbon\Carbon::now();
			 $userId = !empty(Auth::user()->id) ? Auth::user()->id : '';
			 $message = 'PO Items doses not exist.';
			$requestData = $request->all();
		 
			$pos = !empty($requestData['pos']) && isset($requestData['pos']) ? $requestData['pos'] : [];
			 sort($pos);
			 $itemCnt = count($pos);
			 $allDownld = $allCompted = 0;
 	
			 $poItems= DB::table('purchase_order_items as poitem')->select('po.po_ref_id','poitem.po_item_id', 'p.product_code', 'p.clean_description as desc','p.product_desc', 's.code AS supplier', 'poitem.quantity', DB::raw('CAST(price AS DECIMAL(10,2)) AS price'),'poitem.created_at',  DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"))
				  ->join("purchase_orders as po", "po.po_id", "=", "poitem.po_id")
                 ->join("products as p", "p.prod_id", "=", "poitem.product_id")
                 ->join("suppliers as s", "s.id", "=", "poitem.supplier_id")
				 ->leftJoin("users as u", "u.id", "=", "poitem.inserted_by")
				->whereIn('poitem.po_id',$pos)
				->orderBy('poitem.created_at', 'DESC')
                ->get()->map(function ($poItems) {
			if (empty($poItems->desc)) {
				$poItems->desc = $poItems->product_desc;
			};
				
			return $poItems;
			});

			$filename =  Helper:: generatePONumber($pos);
			
			 if(!empty($poItems) && count($poItems) > 0) {

				$allDownloaded =  DB::table('purchase_orders')
                    ->whereIn("po_id", $pos)->where("is_downloaded", 1)->count();
				$allCompleted =  DB::table('purchase_orders')
                    ->whereIn("po_id", $pos)->where("status", PurchaseOrder::PO_COMPLETED)->count();
				 
				if($allDownloaded == $itemCnt) {
					$allDownld = 1;
				}
				if($allCompleted == $itemCnt) {
					$allCompted = 1;
				}

			    $lastGroupId = DB::table('purchase_orders')->orderBy('group_id', 'DESC')->pluck('group_id')->first();
        		$lastGroupId = !empty($lastGroupId) ? $lastGroupId +1 : 1;
					DB::table('purchase_orders')
                    ->whereIn("po_id", $pos)
                    ->update(["group_id" => $lastGroupId, "lastchanged_by" => $userId, "updated_at" => $now]);
				 
				 	//Stores in temporary table
				 	DB::table('temp_pos')->where('userid ', $userId)->delete();
				 $posStr = implode(",",$pos);
				 	DB::table('temp_pos')->insert(
					['ids' => $posStr, 'userid' => $userId]
				);
				
				 $response = TRUE;
				 $message = 'PO Items fetched successfully.';
				}
			 $data = ['filename' => $filename, 'poitems' => $poItems, 'all_downloaded' => $allDownld, 'all_completed' => $allCompted]; 
			 return $this->sendResponse($data, $message);
			 } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to download PO Items', 'Failed : ' . $error->getMessage(), 209);
        }
	}

	 public function generatePONum()
    {
        
              $poObj = \DB::table('purchase_orders')->select('po_ref_id')->latest('po_id')->first();
         
   
        if ($poObj) {
            $poNr = $poObj->po_ref_id;
					 
            $removed1char = substr($poNr, 1);
		
            $generatePO_nr = $stpad = str_pad($removed1char + 1, 8, "PPPO", STR_PAD_LEFT);
		
        } else {
            $generatePO_nr = str_pad(1, 8, "PPPO", STR_PAD_LEFT);
        }
        return $generatePO_nr;
    }

    
    /**
     * Updates the status of Purchase order| Mark as APPROVAL PENDING | APPROVED | COMPLETED
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Purchase Order Id|Primary Key column
     * @throws AuthorizationException
     * 
     * @return PurchaseOrder
     */
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $status = !empty($requestData['status']) && isset($requestData['status']) ? (int) trim($requestData['status']) : 0;
  
        try {
            $purchaseOrder = PurchaseOrder::find($id);
            
           
            
            if(empty($purchaseOrder)) {
                return $this->sendErrorResponse('Failed to update PO', 'Purchase order does not exist', 208);
        
            }
            
            switch ($status) {
				case 1:
					  $message = 'Purchase Order marked as "APPROVAL PENDING"';
				break;

				case 2:
					 $message = 'Purchase Order marked as "APPROVED"';
					 $verifiedBy = Auth::user()->id;
					 $purchaseOrder->verified_at = \Carbon\Carbon::now();
					 $purchaseOrder->verified_by = $verifiedBy;
				break;

				case 3:
					  $message = 'Purchase Order marked as "COMPLETED"';
				break;
				
				default:
                break;
        	}
                $purchaseOrder->updated_at = \Carbon\Carbon::now();
              
                $purchaseOrder->lastchanged_by = Auth::user()->id;
               
				$purchaseOrder->status = $status;
				$purchaseOrder->update();
               
            
           
           
            return $this->sendResponse('PO Updated', $message);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update PO', 'Failed : ' . $error->getMessage(), 209);
        }
    }
	
	
	/**
     * Updates the status of Purchase order| Mark as COMPLETED as per the confirmation from user
	 * Stores the information about PO downloaded by and downloaded at
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Purchase Order Id|Primary Key column
     * @throws AuthorizationException
     * 
     * @return PurchaseOrder
     */
    public function updateStatus(Request $request, $id)
    {
        $requestData = $request->all();
        $isCompleted = !empty($requestData['is_completed']) && isset($requestData['is_completed']) ? (int) trim($requestData['is_completed']) : NULL;
		$data = [];
			
  		$verifiedBy = NULL;
        try {
            $purchaseOrder = PurchaseOrder::find($id);
            
           
            
            if(empty($purchaseOrder)) {
                return $this->sendErrorResponse('Failed to update PO', 'Purchase order does not exist', 208);
        
            }
            $message = 'Purchase Order downloaded suceesfully';
			
          	if($isCompleted == 1 && $purchaseOrder->status == 2) {
				$message = 'Purchase Order downloaded suceesfully and marked as "COMPLETED"';
			}
			
			$purchaseOrder->updated_at = \Carbon\Carbon::now();
			$purchaseOrder->lastchanged_by = Auth::user()->id;
			$purchaseOrder->is_downloaded = 1;
			if($isCompleted ==1) {
				$purchaseOrder->status  = PurchaseOrder::PO_COMPLETED;
			}

				$purchaseOrder->update();
               $data['poid'] = $id;
            return $this->sendResponse($data, $message);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update PO', 'Failed : ' . $error->getMessage(), 209);
        }
    }

/**
     * Updates the status of Purchase order| Mark as COMPLETED as per the confirmation from user
	 * Stores the information about PO downloaded by and downloaded at
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Purchase Order Id|Primary Key column
     * @throws AuthorizationException
     * 
     * @return PurchaseOrder
     */
    public function updateStatusOfAllPOs(Request $request)
    {
       

        try {
			 $requestData = $request->all();
        $isCompleted = !empty($requestData['is_completed']) && isset($requestData['is_completed']) ? (int) trim($requestData['is_completed']) : NULL;
		$now = \Carbon\Carbon::now();
			$userId = !empty(Auth::user()) ?  Auth::user()->id : '';
			 $message = 'POs are downloaded suceesfully';
		
			if(empty($userId)) {
                return $this->sendErrorResponse('Failed to update POs', 'User does not exist', 208);
        
            }

			if(!empty($userId)) {
				$posStr = DB::table('temp_pos')->where('userid', $userId)->first('ids');
				$posStr = !empty($posStr->ids) && isset($posStr->ids) ?  $posStr->ids : '';
				$pos = !empty($posStr) ? explode(",", $posStr) : [];
			
			if(empty($pos)) {
                return $this->sendErrorResponse('Failed to update POs', 'POs do not exist', 208);
        
            }
				
				if($isCompleted == 1 && !empty($pos)) {
					 DB::table('purchase_orders')
                    ->whereIn("po_id", $pos)
                    ->update(["status" => PurchaseOrder::PO_COMPLETED, "lastchanged_by" => $userId, "updated_at" => $now]);
				$message = 'POs are downloaded suceesfully and marked as "COMPLETED"';
				 }

				 DB::table('purchase_orders')
                    ->whereIn("po_id", $pos)
                    ->update(["is_downloaded" => 1, "lastchanged_by" => $userId, "updated_at" => $now]);
				
				DB::table('temp_pos')->where('userid ', $userId)->delete();
				
			}
         
           
               
            return $this->sendResponse('PO Updated', $message);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update PO', 'Failed : ' . $error->getMessage(), 209);
        }
    }



        /**
     * Remove PO Item from the selected PO
     *
     * @param int $id Purchase Order Item Id|Primary Key column
     * 
     * @return void
     */
    public function removePOItem($id)
    {

        try {
            $purchaseOrderItem = PurchaseOrderItem::find($id);
            
           	if(empty($purchaseOrderItem)) {
                return $this->sendErrorResponse('Failed to update PO Item', 'Purchase order item does not exist', 208);
        
            }
            
				$purchaseOrderItem->po_id = NULL;
                $purchaseOrderItem->updated_at = \Carbon\Carbon::now();
                $purchaseOrderItem->lastchanged_by = Auth::user()->id;
				$purchaseOrderItem->update();
               
            	return $this->sendResponse('PO Item Updated', "PO Item removed sucessfully from the current PO");
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update PO Item', 'Failed : ' . $error->getMessage(), 209);
        }
    }
	
	
	/**
	* Gets the PO value as per suppllier for current month and last month
	* This data is used to generate graph of PO data
	*
	*/
	  public function getPOInsight()
    {
		$pos = $data = [];
		
		  $suppliers = $this->getSupplierList();
      
		  //return $suppliers;
		   foreach($suppliers as $supplierId) {
			   $supplier = \DB::table('suppliers')->where("id",$supplierId)->pluck('code')->first();
			   //$supplier = \DB::table('suppliers')->where("id",$supplierId)->select('suppliers.code')->get();
	
			   	$pos['labels'][] = $supplier;
			    $currentMPoVal = $this->getPOIValueCurrentMonth($supplierId);
			
			   $lastMPoVal = $this->getPOIValueLastMonth($supplierId);
			
			   if(!empty($currentMPoVal) && empty($lastMPoVal) ) {
			    	$pos['datasets']["current"][] = $currentMPoVal;
					$pos['datasets']["last"][] = 0;
			   }
				if(!empty($currentMPoVal) && !empty($lastMPoVal) ) {
			    	$pos['datasets']["current"][] = $currentMPoVal;
					$pos['datasets']["last"][] = $lastMPoVal;
			   }

				if(empty($currentMPoVal) && !empty($lastMPoVal) ) {
			    	$pos['datasets']["current"][] = 0;
					$pos['datasets']["last"][] = $lastMPoVal;
			   }
			   if(empty($currentMPoVal) && empty($lastMPoVal) ) {
			    	$pos['datasets']["current"][] = 0;
					$pos['datasets']["last"][] = 0;
			   }
			}
	
		 $data['po_insight'] = $pos;
        if (!empty($pos)) {
            return $this->sendResponse($data, 'Purchase Order Insight retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order data found']);
        }
    }

	/**
	* Gets the total of PO value for current month for the supplier
	*
	* @param int $supplierId The supplier ID
	* @return string
	*/
  private function getPOIValueCurrentMonth($supplierId)
    {
		$pos = [];
		$now = date('Y-m-d H:i:s');
		$ThreeWeeksAgo = date('Y-m-d H:i:s', strtotime('-3 weeks'));
		$startDate = Carbon::now(); //returns current day
		$fromDate = $startDate->firstOfMonth()->format("Y-m-d");
		$toDate = $startDate->lastOfMonth()->format("Y-m-d");  
	
		//Gets the 3 weeks ago created Pos
        $currentMPoVal = PurchaseOrderItem::join('purchase_orders as po', 'po.po_id', '=', 'purchase_order_items.po_id')
			->where("purchase_order_items.supplier_id", $supplierId)
			->where('po.status', PurchaseOrder::PO_COMPLETED)->whereBetween('po.created_at', [$fromDate, $toDate])
			->select(DB::raw("SUM(purchase_order_items.price) as povalue"))->groupBy("purchase_order_items.supplier_id")->first();
			$currentMPoVal = isset($currentMPoVal->povalue) && !empty($currentMPoVal->povalue) ? (int) $currentMPoVal->povalue : 0;
       return $currentMPoVal;
        
    }

	/**
	* Gets the total of PO value for last month for the supplier
	*
	* @param int $supplierId The supplier ID
	* @return string
	*/
  private function getPOIValueLastMonth($supplierId)
    {
		$fromDate = new Carbon('first day of last month');
		$toDate = new Carbon('last day of last month');
	  $fromDate = $fromDate->format("Y-m-d");
	  $toDate = $toDate->format("Y-m-d");
		
		//Gets the 3 weeks ago created Pos
        $lastMPoVal = PurchaseOrderItem::join('purchase_orders as po', 'po.po_id', '=', 'purchase_order_items.po_id')
			->where("purchase_order_items.supplier_id", $supplierId)
			->where('po.status', PurchaseOrder::PO_COMPLETED)->whereBetween('po.created_at', [$fromDate, $toDate])
			->select(DB::raw("SUM(purchase_order_items.price) as povalue"))->groupBy("purchase_order_items.supplier_id")->first();
		$lastMPoVal = isset($lastMPoVal->povalue) && !empty($lastMPoVal->povalue) ? (int) $lastMPoVal->povalue : 0;
       return $lastMPoVal;
        
    }
	
	/**
	* Gets the list of supplier ids for whom the PO is complated in current month and last month
	*
	* @return array
	*/
	private function getSupplierList()
    {
		$startDate = Carbon::now(); //returns current day
		$fromDate = new Carbon('first day of last month');
		
	  $fromDate = $fromDate->format("Y-m-d");
	  $toDate = $startDate->lastOfMonth()->format("Y-m-d");  
		
		//Gets the 3 weeks ago created Pos
        $suppliers = PurchaseOrderItem::join('purchase_orders as po', 'po.po_id', '=', 'purchase_order_items.po_id')
			->where('po.status', PurchaseOrder::PO_COMPLETED)->whereBetween('po.created_at', [$fromDate, $toDate])->groupBy("purchase_order_items.supplier_id")->pluck("purchase_order_items.supplier_id")->toArray();
			
       return $suppliers;
        
    }
	
	
	  /**
     * Get the list of downloaded POs
     *
     * @return json The purchase order Items json array
     */
    public function getDownloadedPos()
    {

		$pos = $poGr =  [];
		$now = date('Y-m-d H:i:s');
		$ThreeWeeksAgo = date('Y-m-d H:i:s', strtotime('-3 weeks'));
	

		//Gets the 3 weeks ago created Pos
        $pos = DB::table('purchase_orders as po')
			->leftJoin('suppliers as sup', 'sup.id', '=', 'po.supplier_id')
			->leftJoin("users as u", "u.id", "=", "po.inserted_by")
			->leftJoin("users as us", "us.id", "=", "po.lastchanged_by")
			->leftJoin("users as v", "v.id", "=", "po.verified_by")
			->select('po.po_id', 'sup.code', 'po.supplier_id', 'po.po_ref_id as po_num',"concerto_reference", 'po.group_id as groupid', DB::raw("CONCAT('GROUP',po.group_id) AS group_num"), 'po.notes', 'po.status', 'po.created_at',  DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"), 'po.updated_at',DB::raw("CONCAT(us.firstname,' ',us.lastname) AS updated_by"),  'po.verified_at',DB::raw("CONCAT(v.firstname,' ',v.lastname) AS verified_by"),  'po.is_downloaded')
					->where("po.is_downloaded",1)
                ->orderBy('po.group_id', 'DESC')

                ->get();
		
	  if(!empty($pos)) {
	

			foreach($pos as $po) {
			
				$poId = !empty($po->po_id) && isset($po->po_id) ? $po->po_id : '';
				$concerto_reference = !empty($po->concerto_reference) && isset($po->concerto_reference) ? $po->concerto_reference : '';
			$code = !empty($po->code) && isset($po->code) ? $po->code : '';
			$supplier_id = !empty($po->supplier_id) && isset($po->supplier_id) ? $po->supplier_id : '';
			$po_num = !empty($po->po_num) && isset($po->po_num) ? $po->po_num : '';
			$group_id = !empty($po->groupid) && isset($po->groupid) ? $po->groupid: '';
			$group_num = !empty($po->group_num) && isset($po->group_num) ? $po->group_num : '';
			$notes = !empty($po->notes) && isset($po->notes) ? $po->notes : '';
			$status = !empty($po->status) && isset($po->status) ? $po->status: '';
			$created_at = !empty($po->created_at) && isset($po->created_at) ? $po->created_at : '';
		$created_by = !empty($po->created_by) && isset($po->created_by) ? $po->created_by : '';
		$updated_at = !empty($po->updated_at) && isset($po->updated_at) ? $po->updated_at : '';
		$updated_by = !empty($po->updated_by) && isset($po->updated_by) ? $po->updated_by : '';
		$verified_at = !empty($po->verified_at) && isset($po->verified_at) ? $po->verified_at : '';
		$verified_by = !empty($po->verified_by) && isset($po->verified_by) ? $po->verified_by : '';
		$is_downloaded = !empty($po->is_downloaded) && isset($po->is_downloaded) ? $po->is_downloaded : '';
				
				$groupCount = PurchaseOrder::where("group_id", $group_id)->count();
				if(!empty($group_id)) {
			  $poGr[] = [
            "po_id" => $poId,
			"concerto_reference" => $concerto_reference,
            "code" => $code,
            "supplier_id" => $supplier_id,
            "po_num" => $po_num,
            "group_id" => $group_id,
			"po_count" => $groupCount,
            "group_num" => $group_num,
            "notes" => $notes,
            "status" => $status,
            "created_at" => $created_at,
            "created_by" => $created_by,
            "updated_at" => $updated_at,
            "updated_by" => $updated_by,
            "verified_at" => $verified_at,
            "verified_by" => $verified_by,
            "is_downloaded" => $is_downloaded,
        ];
				   }
			}
		  }
		
	
        if (!empty($poGr)) {
            return $this->sendResponse($poGr, 'Purchase Order retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Purchase Order data found']);
        }
    }
	
	   /**
     * Updates concerto number to the PO in PP
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Purchase Order Id|Primary Key column
     * @throws AuthorizationException
     * 
     * @return PurchaseOrder
     */
    public function addConcertOReference(Request $request, $id)
    {
    
        $concertoReference = !empty($request->concerto_reference) && isset($request->concerto_reference) ? trim($request->concerto_reference) : '';

        try {
            $purchaseOrderCnt = PurchaseOrder::where("group_id", $id)->count();
            
           
            
            if(empty($purchaseOrderCnt)) {
                return $this->sendErrorResponse('Failed to add concerto reference', 'Purchase order does not exist', 208);
        
            }
            
          DB::table('purchase_orders')
                    ->where("group_id", $id)
                    ->update(["concerto_reference" => $concertoReference]);
			
               return $this->sendResponse('Added concerto reference', 'Added concerto reference successfully');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to add concerto reference', 'Failed : ' . $error->getMessage(), 209);
        }
    }

}
