<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentComment implements CommentRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Comment::paginate();
        }
        return Comment::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $commentId
     * @return Comment
     */
    public function findOrFail($commentId): Comment
    {
        return Comment::whereId($commentId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Comment
     */
    public function store(array $attributes): Comment
    {
        $comment = new Comment();
        $comment->fill($attributes);
        $comment->save();
        return $comment;
    }


    /**
     * @param array $attributes
     * @param $commentId
     * @return Comment
     */
    public function update(array $attributes, $commentId): Comment
    {
        $comment = $this->findOrFail($commentId);
        $comment->fill($attributes);
        $comment->save();
        return $comment;
    }


    /**
     * @param $commentId
     * @param $forceDelete
     * @return Comment
     * @throws Exception
     */
    public function destroyOrFail($commentId, $forceDelete = false): Comment
    {
        $comment = $this->findOrFail($commentId);
        if ($forceDelete) {
            $comment->forceDelete();
        } else {
            $comment->delete();
        }
        return $comment;
    }


    /**
     * @param $commentId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductComments($commentId, $search = '', $filter = null): LengthAwarePaginator
    {
        $comment = $this->findOrFail($commentId);
        return $comment->ProductComments()->paginate()->appends('search', $search);
    }
}
