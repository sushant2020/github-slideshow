<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsFeedRequest;
use App\Models\NewsFeed;
use App\Repositories\NewsFeed\NewsFeedRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * @property NewsFeedRepository newsFeedRepository
 */
class NewsFeedController extends Controller
{

    protected $newsFeedRepository = null;

    /**
     * @param NewsFeedRepository $newsFeedRepository
     */
    public function __construct(NewsFeedRepository $newsFeedRepository)
    {
        $this->newsFeedRepository = $newsFeedRepository;
    }

    /**
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('viewAny', NewsFeed::class);
        $search = Request::get('search', '');
        $filter = Request::get('filter');
        return $this->newsFeedRepository->index($search, $filter);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @return NewsFeed
     */
    public function show($id)
    {
        $newsFeed = $this->newsFeedRepository->findOrFail($id);
        $this->authorize('view', $newsFeed);
        return $newsFeed;
    }

    /**
     * @param StoreNewsFeedRequest $request
     * @throws AuthorizationException
     * @return NewsFeed
     */
    public function store(StoreNewsFeedRequest $request)
    {
        $this->authorize('create', NewsFeed::class);
        return $this->newsFeedRepository->store($request->all());
    }

    /**
     * @param StoreNewsFeedRequest $request
     * @param $id
     * @throws AuthorizationException
     * @return NewsFeed
     */
    public function update(StoreNewsFeedRequest $request, $id)
    {
        $newsFeed = $this->newsFeedRepository->findOrFail($id);
        $this->authorize('update', $newsFeed);
        return $this->newsFeedRepository->update($request->all(), $id);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     * @throws Exception
     * @return NewsFeed
     */
    public function destroy($id)
    {
        $newsFeed = $this->newsFeedRepository->findOrFail($id);
        $this->authorize('delete', $newsFeed);
        return $this->newsFeedRepository->destroyOrFail($id);
    }

}
