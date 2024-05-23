<?php

namespace App\Repositories\Supplier;

use App\Models\Supplier;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSupplier implements SupplierRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Supplier::paginate();
        }
        return Supplier::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $supplierId
     * @return Supplier
     */
    public function findOrFail($supplierId): Supplier
    {
        return Supplier::whereId($supplierId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Supplier
     */
    public function store(array $attributes): Supplier
    {
        $supplier = new Supplier();
        $supplier->fill($attributes);
        $supplier->save();
        return $supplier;
    }


    /**
     * @param array $attributes
     * @param $supplierId
     * @return Supplier
     */
    public function update(array $attributes, $supplierId): Supplier
    {
        $supplier = $this->findOrFail($supplierId);
        $supplier->fill($attributes);
        $supplier->save();
        return $supplier;
    }


    /**
     * @param $supplierId
     * @param $forceDelete
     * @return Supplier
     * @throws Exception
     */
    public function destroyOrFail($supplierId, $forceDelete = false): Supplier
    {
        $supplier = $this->findOrFail($supplierId);
        if ($forceDelete) {
            $supplier->forceDelete();
        } else {
            $supplier->delete();
        }
        return $supplier;
    }


    /**
     * @param $supplierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPriceData($supplierId, $search = '', $filter = null): LengthAwarePaginator
    {
        $supplier = $this->findOrFail($supplierId);
        return $supplier->PriceData()->paginate()->appends('search', $search);
    }


    /**
     * @param $supplierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPurchaseOrders($supplierId, $search = '', $filter = null): LengthAwarePaginator
    {
        $supplier = $this->findOrFail($supplierId);
        return $supplier->PurchaseOrders()->paginate()->appends('search', $search);
    }


    /**
     * @param $supplierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUsageData($supplierId, $search = '', $filter = null): LengthAwarePaginator
    {
        $supplier = $this->findOrFail($supplierId);
        return $supplier->UsageData()->paginate()->appends('search', $search);
    }
}
