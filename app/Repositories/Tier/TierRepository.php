<?php

namespace App\Repositories\Tier;

use App\Models\Tier;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TierRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $tierId
     * @return Tier
     */
    public function findOrFail($tierId): Tier;


    /**
     * @param array $attributes
     * @return Tier
     */
    public function store(array $attributes): Tier;


    /**
     * @param array $attributes
     * @param $tierId
     * @return Tier
     */
    public function update(array $attributes, $tierId): Tier;


    /**
     * @param $tierId
     * @param $forceDelete
     * @return Tier
     * @throws Exception
     */
    public function destroyOrFail($tierId, $forceDelete = false): Tier;


    /**
     * @param $tierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPriceData($tierId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $tierId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUsageData($tierId, $search = '', $filter = null): LengthAwarePaginator;
}
