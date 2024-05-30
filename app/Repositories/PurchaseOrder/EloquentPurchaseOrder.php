<?php

namespace App\Repositories\PurchaseOrder;

use App\Models\PurchaseOrder;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPurchaseOrder implements PurchaseOrderRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return PurchaseOrder::paginate();
        }
        return PurchaseOrder::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $purchaseOrderId
     * @return PurchaseOrder
     */
    public function findOrFail($purchaseOrderId): PurchaseOrder
    {
        return PurchaseOrder::whereId($purchaseOrderId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return PurchaseOrder
     */
    public function store(array $attributes): PurchaseOrder
    {
        $purchaseOrder = new PurchaseOrder();
        $purchaseOrder->fill($attributes);
        $purchaseOrder->save();
        return $purchaseOrder;
    }


    /**
     * @param array $attributes
     * @param $purchaseOrderId
     * @return PurchaseOrder
     */
    public function update(array $attributes, $purchaseOrderId): PurchaseOrder
    {
        $purchaseOrder = $this->findOrFail($purchaseOrderId);
        $purchaseOrder->fill($attributes);
        $purchaseOrder->save();
        return $purchaseOrder;
    }


    /**
     * @param $purchaseOrderId
     * @param $forceDelete
     * @return PurchaseOrder
     * @throws Exception
     */
    public function destroyOrFail($purchaseOrderId, $forceDelete = false): PurchaseOrder
    {
        $purchaseOrder = $this->findOrFail($purchaseOrderId);
        if ($forceDelete) {
            $purchaseOrder->forceDelete();
        } else {
            $purchaseOrder->delete();
        }
        return $purchaseOrder;
    }
}
