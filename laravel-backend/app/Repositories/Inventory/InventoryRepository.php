<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InventoryRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $inventoryId
     * @return Inventory
     */
    public function findOrFail($inventoryId): Inventory;


    /**
     * @param array $attributes
     * @return Inventory
     */
    public function store(array $attributes): Inventory;


    /**
     * @param array $attributes
     * @param $inventoryId
     * @return Inventory
     */
    public function update(array $attributes, $inventoryId): Inventory;


    /**
     * @param $inventoryId
     * @param $forceDelete
     * @return Inventory
     * @throws Exception
     */
    public function destroyOrFail($inventoryId, $forceDelete = false): Inventory;
}
