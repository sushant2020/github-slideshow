<?php

namespace App\Repositories\Users;

use App\Models\Users;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UsersRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $usersId
     * @return Users
     */
    public function findOrFail($usersId): Users;


    /**
     * @param array $attributes
     * @return Users
     */
    public function store(array $attributes): Users;


    /**
     * @param array $attributes
     * @param $usersId
     * @return Users
     */
    public function update(array $attributes, $usersId): Users;


    /**
     * @param $usersId
     * @param $forceDelete
     * @return Users
     * @throws Exception
     */
    public function destroyOrFail($usersId, $forceDelete = false): Users;


    /**
     * @param $usersId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPurchaseOrders($usersId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $usersId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUserActivities($usersId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $usersId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUserRoles($usersId, $search = '', $filter = null): LengthAwarePaginator;
}
