<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitorPricing extends Model
{

    public $table = 'competitor_prices';
    public $primaryKey = 'cprice_id';
	public $fillable = ['watchlist_id','product_id', 'phoenix','trident' ,'aah','colorama','bestway','phoenix_outofstock' ,'trident_outofstock','aah_outofstock','colorama_outofstock','bestway_outofstock',
              'phoenix_note','trident_note', 'aah_note', 'colorama_note','bestway_note',
            'AsOfDate','group', 
            'created_at','updated_at',  'inserted_by', 'lastchanged_by','actioned','watchlist_id'];

    public $hidden = [];
    public $appends = [];
	
	
		   
}
