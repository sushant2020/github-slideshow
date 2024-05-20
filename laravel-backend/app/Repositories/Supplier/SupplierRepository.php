<?php

namespace App\Repositories\Supplier;

use App\Models\Supplier;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $supplierId
     * @return Supplier
     */
    public function findOrFail($supplierId): Supplier;


    /**
     * @param array $attributes
     * @return Supplier
     */
    public function store(array $attributes): Supplier;


    /**
     * @param array $attributes
     * @param $supplierId
     * @return Supplier
     */
    public function update(array $attributes, $supplierId): Supplier;


    /**
     * @param $supplierId
     * @param $forceDelete
     * @return Supplier
     * @throws Exception
     */
    public function destroyOrFail($supplierId, $forceDelete = false): Supplier;


    /**
     * @param $supplierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPriceData($supplierId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $supplierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPurchaseOrders($supplierId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $supplierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUsageData($supplierId, $search = '', $filter = null): LengthAwarePaginator;
}
