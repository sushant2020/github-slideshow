<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserActivityRequest;
use App\Models\UserActivity;
use App\Repositories\UserActivity\UserActivityRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * @property UserActivityRepository userActivityRepository
 */
class UserActivityController extends Controller
{

    protected $userActivityRepository = null;

    /**
     * @param UserActivityRepository $userActivityRepository
     */
    public function __construct(UserActivityRepository $userActivityRepository)
    {
        $this->userActivityRepository = $userActivityRepository;
    }

    /**
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('viewAny', UserActivity::class);
        $search = Request::get('search', '');
        $filter = Request::get('filter');
        return $this->userActivityRepository->index($search, $filter);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @return UserActivity
     */
    public function show($id)
    {
        $userActivity = $this->userActivityRepository->findOrFail($id);
        $this->authorize('view', $userActivity);
        return $userActivity;
    }

    /**
     * @param StoreUserActivityRequest $request
     * @throws AuthorizationException
     * @return UserActivity
     */
    public function store(StoreUserActivityRequest $request)
    {
        $this->authorize('create', UserActivity::class);
        return $this->userActivityRepository->store($request->all());
    }

    /**
     * @param StoreUserActivityRequest $request
     * @param $id
     * @throws AuthorizationException
     * @return UserActivity
     */
    public function update(StoreUserActivityRequest $request, $id)
    {
        $userActivity = $this->userActivityRepository->findOrFail($id);
        $this->authorize('update', $userActivity);
        return $this->userActivityRepository->update($request->all(), $id);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @throws Exception
     * @return UserActivity
     */
    public function destroy($id)
    {
        $userActivity = $this->userActivityRepository->findOrFail($id);
        $this->authorize('delete', $userActivity);
        return $this->userActivityRepository->destroyOrFail($id);
    }

}
