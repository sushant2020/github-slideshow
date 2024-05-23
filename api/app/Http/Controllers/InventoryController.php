<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Models\Inventory;
use App\Repositories\Inventory\InventoryRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * @property InventoryRepository inventoryRepository
 */
class InventoryController extends Controller
{

    protected $inventoryRepository = null;

    /**
     * @param InventoryRepository $inventoryRepository
     */
    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('viewAny', Inventory::class);
        $search = Request::get('search', '');
        $filter = Request::get('filter');
        return $this->inventoryRepository->index($search, $filter);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @return Inventory
     */
    public function show($id)
    {
        $inventory = $this->inventoryRepository->findOrFail($id);
        $this->authorize('view', $inventory);
        return $inventory;
    }

    /**
     * @param StoreInventoryRequest $request
     * @throws AuthorizationException
     * @return Inventory
     */
    public function store(StoreInventoryRequest $request)
    {
        $this->authorize('create', Inventory::class);
        return $this->inventoryRepository->store($request->all());
    }

    /**
     * @param StoreInventoryRequest $request
     * @param $id
     * @throws AuthorizationException
     * @return Inventory
     */
    public function update(StoreInventoryRequest $request, $id)
    {
        $inventory = $this->inventoryRepository->findOrFail($id);
        $this->authorize('update', $inventory);
        return $this->inventoryRepository->update($request->all(), $id);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @throws Exception
     * @return Inventory
     */
    public function destroy($id)
    {
        $inventory = $this->inventoryRepository->findOrFail($id);
        $this->authorize('delete', $inventory);
        return $this->inventoryRepository->destroyOrFail($id);
    }

}
