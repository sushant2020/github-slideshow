<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    public $table = 'permissions';
    public $primaryKey = 'permission_id';
    public $fillable = ['description', 'inserted_by', 'lastchanged_by', 'name', 'permission_id'];
    public $casts = ['name' => 'string'];
    public $hidden = ['RolePermissions'];
    public $appends = [];

    public function RolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }

}
