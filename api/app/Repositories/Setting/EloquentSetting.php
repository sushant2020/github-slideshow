<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSetting implements SettingRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Setting::paginate();
        }
        return Setting::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $settingId
     * @return Setting
     */
    public function findOrFail($settingId): Setting
    {
        return Setting::whereId($settingId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Setting
     */
    public function store(array $attributes): Setting
    {
        $setting = new Setting();
        $setting->fill($attributes);
        $setting->save();
        return $setting;
    }


    /**
     * @param array $attributes
     * @param $settingId
     * @return Setting
     */
    public function update(array $attributes, $settingId): Setting
    {
        $setting = $this->findOrFail($settingId);
        $setting->fill($attributes);
        $setting->save();
        return $setting;
    }


    /**
     * @param $settingId
     * @param $forceDelete
     * @return Setting
     * @throws Exception
     */
    public function destroyOrFail($settingId, $forceDelete = false): Setting
    {
        $setting = $this->findOrFail($settingId);
        if ($forceDelete) {
            $setting->forceDelete();
        } else {
            $setting->delete();
        }
        return $setting;
    }
}
