<?php

namespace App\Repositories\Tag;

use App\Models\Tag;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTag implements TagRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Tag::paginate();
        }
        return Tag::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $tagId
     * @return Tag
     */
    public function findOrFail($tagId): Tag
    {
        return Tag::whereId($tagId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Tag
     */
    public function store(array $attributes): Tag
    {
        $tag = new Tag();
        $tag->fill($attributes);
        $tag->save();
        return $tag;
    }


    /**
     * @param array $attributes
     * @param $tagId
     * @return Tag
     */
    public function update(array $attributes, $tagId): Tag
    {
        $tag = $this->findOrFail($tagId);
        $tag->fill($attributes);
        $tag->save();
        return $tag;
    }


    /**
     * @param $tagId
     * @param $forceDelete
     * @return Tag
     * @throws Exception
     */
    public function destroyOrFail($tagId, $forceDelete = false): Tag
    {
        $tag = $this->findOrFail($tagId);
        if ($forceDelete) {
            $tag->forceDelete();
        } else {
            $tag->delete();
        }
        return $tag;
    }


    /**
     * @param $tagId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProducts($tagId, $search = '', $filter = null): LengthAwarePaginator
    {
        $tag = $this->findOrFail($tagId);
        return $tag->Products()->paginate()->appends('search', $search);
    }
}
