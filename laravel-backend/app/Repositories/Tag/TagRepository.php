<?php

namespace App\Repositories\Tag;

use App\Models\Tag;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $tagId
     * @return Tag
     */
    public function findOrFail($tagId): Tag;


    /**
     * @param array $attributes
     * @return Tag
     */
    public function store(array $attributes): Tag;


    /**
     * @param array $attributes
     * @param $tagId
     * @return Tag
     */
    public function update(array $attributes, $tagId): Tag;


    /**
     * @param $tagId
     * @param $forceDelete
     * @return Tag
     * @throws Exception
     */
    public function destroyOrFail($tagId, $forceDelete = false): Tag;


    /**
     * @param $tagId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProducts($tagId, $search = '', $filter = null): LengthAwarePaginator;
}
