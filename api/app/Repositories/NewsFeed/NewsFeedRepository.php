<?php

namespace App\Repositories\NewsFeed;

use App\Models\NewsFeed;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NewsFeedRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $newsFeedId
     * @return NewsFeed
     */
    public function findOrFail($newsFeedId): NewsFeed;


    /**
     * @param array $attributes
     * @return NewsFeed
     */
    public function store(array $attributes): NewsFeed;


    /**
     * @param array $attributes
     * @param $newsFeedId
     * @return NewsFeed
     */
    public function update(array $attributes, $newsFeedId): NewsFeed;


    /**
     * @param $newsFeedId
     * @param $forceDelete
     * @return NewsFeed
     * @throws Exception
     */
    public function destroyOrFail($newsFeedId, $forceDelete = false): NewsFeed;
}
