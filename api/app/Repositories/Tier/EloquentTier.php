<?php

namespace App\Repositories\Tier;

use App\Models\Tier;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTier implements TierRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Tier::paginate();
        }
        return Tier::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $tierId
     * @return Tier
     */
    public function findOrFail($tierId): Tier
    {
        return Tier::whereId($tierId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Tier
     */
    public function store(array $attributes): Tier
    {
        $tier = new Tier();
        $tier->fill($attributes);
        $tier->save();
        return $tier;
    }


    /**
     * @param array $attributes
     * @param $tierId
     * @return Tier
     */
    public function update(array $attributes, $tierId): Tier
    {
        $tier = $this->findOrFail($tierId);
        $tier->fill($attributes);
        $tier->save();
        return $tier;
    }


    /**
     * @param $tierId
     * @param $forceDelete
     * @return Tier
     * @throws Exception
     */
    public function destroyOrFail($tierId, $forceDelete = false): Tier
    {
        $tier = $this->findOrFail($tierId);
        if ($forceDelete) {
            $tier->forceDelete();
        } else {
            $tier->delete();
        }
        return $tier;
    }


    /**
     * @param $tierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPriceData($tierId, $search = '', $filter = null): LengthAwarePaginator
    {
        $tier = $this->findOrFail($tierId);
        return $tier->PriceData()->paginate()->appends('search', $search);
    }


    /**
     * @param $tierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUsageData($tierId, $search = '', $filter = null): LengthAwarePaginator
    {
        $tier = $this->findOrFail($tierId);
        return $tier->UsageData()->paginate()->appends('search', $search);
    }
}
