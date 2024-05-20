<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCommentRole extends Model
{

    protected $table = 'product_comment_role';
    public $fillable = ['comment_id', 'product_id', 'role_id', 'created_at', 'inserted_by'];

}
