<?php

namespace App\Repositories\PurchaseOrder;

use App\Models\PurchaseOrder;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseOrderRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $purchaseOrderId
     * @return PurchaseOrder
     */
    public function findOrFail($purchaseOrderId): PurchaseOrder;


    /**
     * @param array $attributes
     * @return PurchaseOrder
     */
    public function store(array $attributes): PurchaseOrder;


    /**
     * @param array $attributes
     * @param $purchaseOrderId
     * @return PurchaseOrder
     */
    public function update(array $attributes, $purchaseOrderId): PurchaseOrder;


    /**
     * @param $purchaseOrderId
     * @param $forceDelete
     * @return PurchaseOrder
     * @throws Exception
     */
    public function destroyOrFail($purchaseOrderId, $forceDelete = false): PurchaseOrder;
}
