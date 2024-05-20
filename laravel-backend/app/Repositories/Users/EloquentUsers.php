<?php

namespace App\Repositories\Users;

use App\Models\Users;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUsers implements UsersRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Users::paginate();
        }
        return Users::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $usersId
     * @return Users
     */
    public function findOrFail($usersId): Users
    {
        return Users::whereId($usersId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Users
     */
    public function store(array $attributes): Users
    {
        $users = new Users();
        $users->fill($attributes);
        $users->save();
        return $users;
    }


    /**
     * @param array $attributes
     * @param $usersId
     * @return Users
     */
    public function update(array $attributes, $usersId): Users
    {
        $users = $this->findOrFail($usersId);
        $users->fill($attributes);
        $users->save();
        return $users;
    }


    /**
     * @param $usersId
     * @param $forceDelete
     * @return Users
     * @throws Exception
     */
    public function destroyOrFail($usersId, $forceDelete = false): Users
    {
        $users = $this->findOrFail($usersId);
        if ($forceDelete) {
            $users->forceDelete();
        } else {
            $users->delete();
        }
        return $users;
    }


    /**
     * @param $usersId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPurchaseOrders($usersId, $search = '', $filter = null): LengthAwarePaginator
    {
        $users = $this->findOrFail($usersId);
        return $users->PurchaseOrders()->paginate()->appends('search', $search);
    }


    /**
     * @param $usersId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUserActivities($usersId, $search = '', $filter = null): LengthAwarePaginator
    {
        $users = $this->findOrFail($usersId);
        return $users->UserActivities()->paginate()->appends('search', $search);
    }


    /**
     * @param $usersId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUserRoles($usersId, $search = '', $filter = null): LengthAwarePaginator
    {
        $users = $this->findOrFail($usersId);
        return $users->UserRoles()->paginate()->appends('search', $search);
    }
}
