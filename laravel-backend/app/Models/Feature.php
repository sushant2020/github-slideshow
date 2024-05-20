<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{

    public $table = 'feature';
    public $primaryKey = 'feature_id';
    public $fillable = ['description', 'feature_id', 'inserted_by', 'lastchanged_by', 'name'];
    public $casts = ['name' => 'string'];
    public $hidden = ['ProductFeatures'];
    public $appends = [];

    public function ProductFeatures()
    {
        return $this->hasMany(ProductFeature::class, 'feature_id');
    }

}
