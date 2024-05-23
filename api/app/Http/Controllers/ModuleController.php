<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModuleRequest;
use App\Models\Module;
use App\Repositories\Module\ModuleRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * @property ModuleRepository moduleRepository
 */
class ModuleController extends Controller
{

    protected $moduleRepository = null;

    /**
     * @param ModuleRepository $moduleRepository
     */
    public function __construct(ModuleRepository $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('viewAny', Module::class);
        $search = Request::get('search', '');
        $filter = Request::get('filter');
        return $this->moduleRepository->index($search, $filter);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @return Module
     */
    public function show($id)
    {
        $module = $this->moduleRepository->findOrFail($id);
        $this->authorize('view', $module);
        return $module;
    }

    /**
     * @param StoreModuleRequest $request
     * @throws AuthorizationException
     * @return Module
     */
    public function store(StoreModuleRequest $request)
    {
        $this->authorize('create', Module::class);
        return $this->moduleRepository->store($request->all());
    }

    /**
     * @param StoreModuleRequest $request
     * @param $id
     * @throws AuthorizationException
     * @return Module
     */
    public function update(StoreModuleRequest $request, $id)
    {
        $module = $this->moduleRepository->findOrFail($id);
        $this->authorize('update', $module);
        return $this->moduleRepository->update($request->all(), $id);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @throws Exception
     * @return Module
     */
    public function destroy($id)
    {
        $module = $this->moduleRepository->findOrFail($id);
        $this->authorize('delete', $module);
        return $this->moduleRepository->destroyOrFail($id);
    }

}
