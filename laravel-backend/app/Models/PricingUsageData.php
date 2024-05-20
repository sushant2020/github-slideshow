<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingUsageData extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usage_data';
    protected $fillable = [
        'tier_id',
        'source_id',
        'parent_product_code',
        'supplier_id',
        'internal_supplier_id',
        'volume',
        'volume_from_date',
        'volume_untill_date',
        'original_volume_from_date',
        'original_volume_untill_date',
        'comments',
        'logger_id',
        'inserted_by',
        'lastchanged_by'
    ];

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }

}
