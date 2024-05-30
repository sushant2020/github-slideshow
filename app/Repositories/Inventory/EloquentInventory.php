<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentInventory implements InventoryRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Inventory::paginate();
        }
        return Inventory::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $inventoryId
     * @return Inventory
     */
    public function findOrFail($inventoryId): Inventory
    {
        return Inventory::whereId($inventoryId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Inventory
     */
    public function store(array $attributes): Inventory
    {
        $inventory = new Inventory();
        $inventory->fill($attributes);
        $inventory->save();
        return $inventory;
    }


    /**
     * @param array $attributes
     * @param $inventoryId
     * @return Inventory
     */
    public function update(array $attributes, $inventoryId): Inventory
    {
        $inventory = $this->findOrFail($inventoryId);
        $inventory->fill($attributes);
        $inventory->save();
        return $inventory;
    }


    /**
     * @param $inventoryId
     * @param $forceDelete
     * @return Inventory
     * @throws Exception
     */
    public function destroyOrFail($inventoryId, $forceDelete = false): Inventory
    {
        $inventory = $this->findOrFail($inventoryId);
        if ($forceDelete) {
            $inventory->forceDelete();
        } else {
            $inventory->delete();
        }
        return $inventory;
    }
}
