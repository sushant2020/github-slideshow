<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTaskUser extends Model
{

    protected $table = 'product_task_user';
    public $fillable = ['task_id', 'product_id', 'user_id', 'created_at', 'inserted_by'];

}
