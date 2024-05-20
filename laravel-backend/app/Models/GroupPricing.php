<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class GroupPricing extends Model
{

    protected $table = 'group_pricing';
    protected $primaryKey = 'gprice_id';


    protected $fillable = ['gprice_id', 'agg_code', 'product_code', 'description', 'packsize', 'generic','topgen','spot','cost','true_cost','avg_cost','stock','avg_volume','cd',
        'RRP', 'ATOZ', 'PHD', 'c87', 'c122', 'DC', 'DG', 'RH', 'RBS', 'new_RRP', 'new_ATOZ', 'new_PHD', 'new_c87', 'new_c122',
        'new_DC', 'new_DG', 'new_RH', 'new_RBS','SC_flag','temp_stop','shortage','price_from_date','price_until_date','inserted_by','lastchanged_by','updated_at'];


}
