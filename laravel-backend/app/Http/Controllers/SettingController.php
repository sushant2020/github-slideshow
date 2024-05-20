<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingRequest;
use App\Models\Setting;
use App\Repositories\Setting\SettingRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * @property SettingRepository settingRepository
 */
class SettingController extends Controller
{

    protected $settingRepository = null;

    /**
     * @param SettingRepository $settingRepository
     */
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('viewAny', Setting::class);
        $search = Request::get('search', '');
        $filter = Request::get('filter');
        return $this->settingRepository->index($search, $filter);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @return Setting
     */
    public function show($id)
    {
        $setting = $this->settingRepository->findOrFail($id);
        $this->authorize('view', $setting);
        return $setting;
    }

    /**
     * @param StoreSettingRequest $request
     * @throws AuthorizationException
     * @return Setting
     */
    public function store(StoreSettingRequest $request)
    {
        $this->authorize('create', Setting::class);
        return $this->settingRepository->store($request->all());
    }

    /**
     * @param StoreSettingRequest $request
     * @param $id
     * @throws AuthorizationException
     * @return Setting
     */
    public function update(StoreSettingRequest $request, $id)
    {
        $setting = $this->settingRepository->findOrFail($id);
        $this->authorize('update', $setting);
        return $this->settingRepository->update($request->all(), $id);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @throws Exception
     * @return Setting
     */
    public function destroy($id)
    {
        $setting = $this->settingRepository->findOrFail($id);
        $this->authorize('delete', $setting);
        return $this->settingRepository->destroyOrFail($id);
    }

}
