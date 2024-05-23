<?php

namespace App\Repositories\NewsFeed;

use App\Models\NewsFeed;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentNewsFeed implements NewsFeedRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return NewsFeed::paginate();
        }
        return NewsFeed::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $newsFeedId
     * @return NewsFeed
     */
    public function findOrFail($newsFeedId): NewsFeed
    {
        return NewsFeed::whereId($newsFeedId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return NewsFeed
     */
    public function store(array $attributes): NewsFeed
    {
        $newsFeed = new NewsFeed();
        $newsFeed->fill($attributes);
        $newsFeed->save();
        return $newsFeed;
    }


    /**
     * @param array $attributes
     * @param $newsFeedId
     * @return NewsFeed
     */
    public function update(array $attributes, $newsFeedId): NewsFeed
    {
        $newsFeed = $this->findOrFail($newsFeedId);
        $newsFeed->fill($attributes);
        $newsFeed->save();
        return $newsFeed;
    }


    /**
     * @param $newsFeedId
     * @param $forceDelete
     * @return NewsFeed
     * @throws Exception
     */
    public function destroyOrFail($newsFeedId, $forceDelete = false): NewsFeed
    {
        $newsFeed = $this->findOrFail($newsFeedId);
        if ($forceDelete) {
            $newsFeed->forceDelete();
        } else {
            $newsFeed->delete();
        }
        return $newsFeed;
    }
}
