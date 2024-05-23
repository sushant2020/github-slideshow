<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{

    public $table = 'tags';
    public $primaryKey = 'tag_id';
    public $fillable = ['inserted_by', 'lastchanged_by', 'name', 'tag_id', 'is_deleted'];
    public $casts = ['name' => 'string'];
    public $hidden = ['Products'];
    public $appends = [];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    
    
    
     public static function getPropertyValueById($id, $propertyName)
    {
        // Retrieve the user instance by ID
        $model = self::find($id);

        // If the user is found, return the value of the specified property
        if ($model) {
            return $model->{$propertyName};
        }

        // Return null if the user is not found
        return null;
    }

}
