<?php

namespace App\Repositories\GRN;

use App\Models\GRN;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentGRN implements GRNRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return GRN::paginate();
        }
        return GRN::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $gRNId
     * @return GRN
     */
    public function findOrFail($gRNId): GRN
    {
        return GRN::whereId($gRNId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return GRN
     */
    public function store(array $attributes): GRN
    {
        $gRN = new GRN();
        $gRN->fill($attributes);
        $gRN->save();
        return $gRN;
    }


    /**
     * @param array $attributes
     * @param $gRNId
     * @return GRN
     */
    public function update(array $attributes, $gRNId): GRN
    {
        $gRN = $this->findOrFail($gRNId);
        $gRN->fill($attributes);
        $gRN->save();
        return $gRN;
    }


    /**
     * @param $gRNId
     * @param $forceDelete
     * @return GRN
     * @throws Exception
     */
    public function destroyOrFail($gRNId, $forceDelete = false): GRN
    {
        $gRN = $this->findOrFail($gRNId);
        if ($forceDelete) {
            $gRN->forceDelete();
        } else {
            $gRN->delete();
        }
        return $gRN;
    }
}
