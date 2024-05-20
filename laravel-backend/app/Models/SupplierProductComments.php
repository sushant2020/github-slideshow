<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProductComments extends Model
{

    use HasFactory;

    public $table = 'supplier_product_comments';


    public $fillable = ['comment_id','product_id', 'pc_id', 'updated_at', 'inserted_by', 'lastchanged_by'];


    public function pricing()
    {
        return $this->belongsToMany(PricingSupplierPriceData::class);
    } 

}
