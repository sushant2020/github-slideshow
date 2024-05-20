<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\Helper;
use App\Models\TempPoItem;

class PurchaseOrderItem extends Model
{

    public $table = 'purchase_order_items';
    public $primaryKey = 'po_item_id';
    public $fillable = [
		'po_id',
        'product_id',
        'supplier_id',
        'quantity',
        'price',
        'created_at',
        'updated_by',
        'inserted_by',
        'lastchanged_by',
    ];
    public $casts = ['supplier_id' => 'int', 'product_id' => 'int'];
    public $hidden = ['Product', 'Supplier', 'User','PurchaseOrder'];
    
 

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function Supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
	
	public function PurchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }



    /**
     * Creates Purchanse Order Items
     * 
     * @param array $poitems Purchase Order Items
     * 
     * @return void
     */
    public function createPO($poitems) {
		$purchaseOrderItems = [];
        foreach($poitems as $poitem) {
        $product_id = !empty($poitem['product_id']) && isset($poitem['product_id']) ? (int) trim($poitem['product_id']) : "";
        $supplier_id = !empty($poitem['supplier_id']) && isset($poitem['supplier_id']) ? (int) trim($poitem['supplier_id']) : "";
        $price = !empty($poitem['price']) && isset($poitem['price']) ? trim($poitem['price']) : "";
        $quantity = !empty($poitem['quantity']) && isset($poitem['quantity']) ? trim($poitem['quantity']) : "";
        $currentDateTime = \Carbon\Carbon::now();
        $insertedBy = \Auth::user()->id;
			//Preparating the final array of PO items
        $purchaseOrderItems[] = [
                "product_id" => $product_id,
                "supplier_id" => $supplier_id,
                "price" => floatval($price),
                "quantity" => $quantity,
                "created_at" => $currentDateTime,
                "inserted_by" => $insertedBy
            ];
	}
		//Inserting records in bulk
          PurchaseOrderItem::insert($purchaseOrderItems);
        
    }
	
	public function checkExistingPO($poitems) {
		
		$holidays = [];
		$workingYesterday = Helper::GetWorkingHisDate($holidays,-1);
		$now = date('Y-m-d H:i:s');
		$status = [PurchaseOrder::PO_COMPLETED];
		//->whereIn("purchase_orders.status",$status)
		//At the time of raising PO , we must see - if PO is completed in last 24 hours , and those that are not completed  and not expired without any time limit  - for the aggregate code
		
$existingPOs = PurchaseOrder::join('purchase_order_items as poitem', 'poitem.po_id', '=', 'purchase_orders.po_id')
		->join("products as p1", "p1.prod_id", "=", "poitem.product_id")->whereIn('p1.ac4', PurchaseOrderItem::join("products as p", "p.prod_id", "=", "purchase_order_items.product_id")
		->whereIn('purchase_order_items.po_item_id', $poitems)->pluck('p.ac4')->all())
->whereBetween('purchase_orders.created_at', [$workingYesterday, $now])->pluck('purchase_orders.po_id')->all();
		$existingPOs = array_unique($existingPOs);
		TempPoItem::truncate();
		$ids = implode(",",$existingPOs);
		TempPoItem::insert(['ids' => $ids]);
		
		return $existingPOs;
	
	}


 /**
     * Creates Purchanse Order Items
     * 
     * @param array $poitems Purchase Order Items
     * 
     * @return void
     */
    public function addPOItems($poitems, $poId) {
		$purchaseOrderItems = [];
        foreach($poitems as $poitem) {
        $product_id = !empty($poitem['product_id']) && isset($poitem['product_id']) ? (int) trim($poitem['product_id']) : "";
        $supplier_id = !empty($poitem['supplier_id']) && isset($poitem['supplier_id']) ? (int) trim($poitem['supplier_id']) : "";
        $price = !empty($poitem['price']) && isset($poitem['price']) ? trim($poitem['price']) : "";
        $quantity = !empty($poitem['quantity']) && isset($poitem['quantity']) ? trim($poitem['quantity']) : "";
        $currentDateTime = \Carbon\Carbon::now();
        $insertedBy = \Auth::user()->id;
			//Preparating the final array of PO items
        $purchaseOrderItems[] = [
			   	"po_id" => $poId,
                "product_id" => $product_id,
                "supplier_id" => $supplier_id,
                "price" => floatval($price),
                "quantity" => $quantity,
                "created_at" => $currentDateTime,
                "inserted_by" => $insertedBy
            ];
	}
		//Inserting records in bulk
          PurchaseOrderItem::insert($purchaseOrderItems);
        
    }
}
