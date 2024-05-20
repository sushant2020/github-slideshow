<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    public $table = 'settings';
    public $primaryKey = 'settings_id';
    public $fillable = [
        'name',
        'meta_key',
        'meta_value',
        'is_deleted',
		'updated_datetime',
        'updated_by',
        'settings_id'
    ];

}
