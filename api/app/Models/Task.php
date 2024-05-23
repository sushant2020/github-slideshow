<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    public $table = 'task';
    public $primaryKey = 'task_id';
    public $fillable = [
        'assigned_to',
        'inserted_by',
        'lastchanged_by',
        'module_id',
        'name',
        'priority',
        'product_id',
        'status',
        'supplier_id',
        'task_id',
    ];
    public $casts = ['priority' => 'boolean', 'status' => 'boolean'];
    public $hidden = [];
    public $appends = [];

}
