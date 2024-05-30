<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CommentRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $commentId
     * @return Comment
     */
    public function findOrFail($commentId): Comment;


    /**
     * @param array $attributes
     * @return Comment
     */
    public function store(array $attributes): Comment;


    /**
     * @param array $attributes
     * @param $commentId
     * @return Comment
     */
    public function update(array $attributes, $commentId): Comment;


    /**
     * @param $commentId
     * @param $forceDelete
     * @return Comment
     * @throws Exception
     */
    public function destroyOrFail($commentId, $forceDelete = false): Comment;


    /**
     * @param $commentId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductComments($commentId, $search = '', $filter = null): LengthAwarePaginator;
}
