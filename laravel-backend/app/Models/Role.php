<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{

    public $table = 'roles';
    public $fillable = ['name', 'guard_name', 'description', 'inserted_by', 'lastchanged_by'];
    public $casts = ['name' => 'string'];

}
