<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ghost extends Model
{

    public $table = 'ghost';
    public $primaryKey = 'ghost_id';

    public $hidden = [];
    public $appends = [];
	
	
		   
}
