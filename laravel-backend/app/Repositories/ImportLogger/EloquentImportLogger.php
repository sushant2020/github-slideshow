<?php

namespace App\Repositories\ImportLogger;

use App\Models\ImportLogger;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentImportLogger implements ImportLoggerRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return ImportLogger::paginate();
        }
        return ImportLogger::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $importLoggerId
     * @return ImportLogger
     */
    public function findOrFail($importLoggerId): ImportLogger
    {
        return ImportLogger::whereId($importLoggerId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return ImportLogger
     */
    public function store(array $attributes): ImportLogger
    {
        $importLogger = new ImportLogger();
        $importLogger->fill($attributes);
        $importLogger->save();
        return $importLogger;
    }


    /**
     * @param array $attributes
     * @param $importLoggerId
     * @return ImportLogger
     */
    public function update(array $attributes, $importLoggerId): ImportLogger
    {
        $importLogger = $this->findOrFail($importLoggerId);
        $importLogger->fill($attributes);
        $importLogger->save();
        return $importLogger;
    }


    /**
     * @param $importLoggerId
     * @param $forceDelete
     * @return ImportLogger
     * @throws Exception
     */
    public function destroyOrFail($importLoggerId, $forceDelete = false): ImportLogger
    {
        $importLogger = $this->findOrFail($importLoggerId);
        if ($forceDelete) {
            $importLogger->forceDelete();
        } else {
            $importLogger->delete();
        }
        return $importLogger;
    }


    /**
     * @param $importLoggerId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPriceData($importLoggerId, $search = '', $filter = null): LengthAwarePaginator
    {
        $importLogger = $this->findOrFail($importLoggerId);
        return $importLogger->PriceData()->paginate()->appends('search', $search);
    }
}
