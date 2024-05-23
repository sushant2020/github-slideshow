<?php

namespace App\Repositories\Permission;

use App\Models\Permission;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PermissionRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $permissionId
     * @return Permission
     */
    public function findOrFail($permissionId): Permission;


    /**
     * @param array $attributes
     * @return Permission
     */
    public function store(array $attributes): Permission;


    /**
     * @param array $attributes
     * @param $permissionId
     * @return Permission
     */
    public function update(array $attributes, $permissionId): Permission;


    /**
     * @param $permissionId
     * @param $forceDelete
     * @return Permission
     * @throws Exception
     */
    public function destroyOrFail($permissionId, $forceDelete = false): Permission;


    /**
     * @param $permissionId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getRolePermissions($permissionId, $search = '', $filter = null): LengthAwarePaginator;
}
