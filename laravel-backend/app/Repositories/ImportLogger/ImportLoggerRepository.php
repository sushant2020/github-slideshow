<?php

namespace App\Repositories\ImportLogger;

use App\Models\ImportLogger;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ImportLoggerRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $importLoggerId
     * @return ImportLogger
     */
    public function findOrFail($importLoggerId): ImportLogger;


    /**
     * @param array $attributes
     * @return ImportLogger
     */
    public function store(array $attributes): ImportLogger;


    /**
     * @param array $attributes
     * @param $importLoggerId
     * @return ImportLogger
     */
    public function update(array $attributes, $importLoggerId): ImportLogger;


    /**
     * @param $importLoggerId
     * @param $forceDelete
     * @return ImportLogger
     * @throws Exception
     */
    public function destroyOrFail($importLoggerId, $forceDelete = false): ImportLogger;


    /**
     * @param $importLoggerId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPriceData($importLoggerId, $search = '', $filter = null): LengthAwarePaginator;
}
