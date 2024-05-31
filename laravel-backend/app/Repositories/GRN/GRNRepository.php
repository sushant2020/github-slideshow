<?php

namespace App\Repositories\GRN;

use App\Models\GRN;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GRNRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $gRNId
     * @return GRN
     */
    public function findOrFail($gRNId): GRN;


    /**
     * @param array $attributes
     * @return GRN
     */
    public function store(array $attributes): GRN;


    /**
     * @param array $attributes
     * @param $gRNId
     * @return GRN
     */
    public function update(array $attributes, $gRNId): GRN;


    /**
     * @param $gRNId
     * @param $forceDelete
     * @return GRN
     * @throws Exception
     */
    public function destroyOrFail($gRNId, $forceDelete = false): GRN;
}
