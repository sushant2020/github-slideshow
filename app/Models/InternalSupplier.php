<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalSupplier extends Model
{

    public $table = 'internal_suppliers';
    public $primaryKey = 'id';
    public $fillable = ['code', 'inserted_by', 'lastchanged_by', 'supplier_type'];
    public $casts = ['code' => 'string', 'supplier_type' => 'boolean'];
    public $hidden = [];
    public $appends = [];

}
