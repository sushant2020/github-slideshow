<?php

namespace App\Repositories\Permission;

use App\Models\Permission;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPermission implements PermissionRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Permission::paginate();
        }
        return Permission::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $permissionId
     * @return Permission
     */
    public function findOrFail($permissionId): Permission
    {
        return Permission::whereId($permissionId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Permission
     */
    public function store(array $attributes): Permission
    {
        $permission = new Permission();
        $permission->fill($attributes);
        $permission->save();
        return $permission;
    }


    /**
     * @param array $attributes
     * @param $permissionId
     * @return Permission
     */
    public function update(array $attributes, $permissionId): Permission
    {
        $permission = $this->findOrFail($permissionId);
        $permission->fill($attributes);
        $permission->save();
        return $permission;
    }


    /**
     * @param $permissionId
     * @param $forceDelete
     * @return Permission
     * @throws Exception
     */
    public function destroyOrFail($permissionId, $forceDelete = false): Permission
    {
        $permission = $this->findOrFail($permissionId);
        if ($forceDelete) {
            $permission->forceDelete();
        } else {
            $permission->delete();
        }
        return $permission;
    }


    /**
     * @param $permissionId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getRolePermissions($permissionId, $search = '', $filter = null): LengthAwarePaginator
    {
        $permission = $this->findOrFail($permissionId);
        return $permission->RolePermissions()->paginate()->appends('search', $search);
    }
}
