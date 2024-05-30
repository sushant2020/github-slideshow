<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImportLoggerRequest;
use App\Models\ImportLogger;
use App\Repositories\ImportLogger\ImportLoggerRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * @property ImportLoggerRepository importLoggerRepository
 */
class ImportLoggerController extends Controller
{

    protected $importLoggerRepository = null;

    /**
     * @param ImportLoggerRepository $importLoggerRepository
     */
    public function __construct(ImportLoggerRepository $importLoggerRepository)
    {
        $this->importLoggerRepository = $importLoggerRepository;
    }

    /**
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('viewAny', ImportLogger::class);
        $search = Request::get('search', '');
        $filter = Request::get('filter');
        return $this->importLoggerRepository->index($search, $filter);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @return ImportLogger
     */
    public function show($id)
    {
        $importLogger = $this->importLoggerRepository->findOrFail($id);
        $this->authorize('view', $importLogger);
        return $importLogger;
    }

    /**
     * @param StoreImportLoggerRequest $request
     * @throws AuthorizationException
     * @return ImportLogger
     */
    public function store(StoreImportLoggerRequest $request)
    {
        $this->authorize('create', ImportLogger::class);
        return $this->importLoggerRepository->store($request->all());
    }

    /**
     * @param StoreImportLoggerRequest $request
     * @param $id
     * @throws AuthorizationException
     * @return ImportLogger
     */
    public function update(StoreImportLoggerRequest $request, $id)
    {
        $importLogger = $this->importLoggerRepository->findOrFail($id);
        $this->authorize('update', $importLogger);
        return $this->importLoggerRepository->update($request->all(), $id);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @throws Exception
     * @return ImportLogger
     */
    public function destroy($id)
    {
        $importLogger = $this->importLoggerRepository->findOrFail($id);
        $this->authorize('delete', $importLogger);
        return $this->importLoggerRepository->destroyOrFail($id);
    }

    /**
     * @param $importLoggerId
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function getPriceData($importLoggerId)
    {
        $importLogger = $this->importLoggerRepository->findOrFail($importLoggerId);
        $this->authorize('view', $importLogger);
        $search = Request::get('search', '');
        $filter = Request::get('filter', null);
        return $this->importLoggerRepository->getPriceData($importLoggerId, $search, $filter);
    }

}
