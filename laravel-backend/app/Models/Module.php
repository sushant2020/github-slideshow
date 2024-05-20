<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{

    public $table = 'module';
    public $primaryKey = 'module_id';
    public $fillable = ['inserted_by', 'lastchanged_by', 'module_id', 'name'];
    public $casts = ['name' => 'string'];
    public $hidden = [];
    public $appends = [];

}
