<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricierComments extends Model
{

    use HasFactory;

    public $table = 'pricier_comments';


    public $fillable = ['comment_id','product_id',  'updated_at', 'inserted_by', 'lastchanged_by'];


    public function product()
    {
        return $this->belongsToMany(DwProduct::class);
    } 

}
