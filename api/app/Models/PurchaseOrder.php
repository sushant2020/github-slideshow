<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{

    public $table = 'purchase_orders';
    public $primaryKey = 'po_id';
    public $fillable = [
        'po_ref_id',
        'notes',
        'status',
        'created_at',
        'updated_by',
        'inserted_by',
        'lastchanged_by',
		'supplier_id',
    ];
    public $casts = ['po_ref_id' => 'string', 'notes' => 'string'];
 
    
    public const PO_IN_PROGRESS = 0;
    public const PO_APPROVAL_PENDING = 1;
    public const PO_APPROVED = 2;
	public const PO_COMPLETED = 3;
	public const PO_EXPIRED = 4;
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function poitems()
    {
        return $this->belongsToMany(PurchaseOrderItem::class);
    }

}
