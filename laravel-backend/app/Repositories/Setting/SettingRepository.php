<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SettingRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $settingId
     * @return Setting
     */
    public function findOrFail($settingId): Setting;


    /**
     * @param array $attributes
     * @return Setting
     */
    public function store(array $attributes): Setting;


    /**
     * @param array $attributes
     * @param $settingId
     * @return Setting
     */
    public function update(array $attributes, $settingId): Setting;


    /**
     * @param $settingId
     * @param $forceDelete
     * @return Setting
     * @throws Exception
     */
    public function destroyOrFail($settingId, $forceDelete = false): Setting;
}
