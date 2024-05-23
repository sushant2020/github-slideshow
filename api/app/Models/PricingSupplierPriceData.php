<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;

class PricingSupplierPriceData extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'pricing_data';

    protected $fillable = [
        'source_id',
        'parent_product_code',
        'product_code',
        'supplier_id',
        'internal_supplier_id',
        'price',
        'price_from_date',
        'price_untill_date',
        'original_price',
        'original_price_from_date',
        'original_price_untill_date',
        'forecast',
        'comments',
        'price_type',
        'import_type',
        'logger_id',
        'inserted_by',
        'lastchanged_by'
    ];

    public const PRICE_TYPE_MANUAL_FORM_IMPORT = 1;
    public const PRICE_TYPE_FILE_IMPORT = 2;
    public const PRICE_TYPE_NEGOTIATED_PRICE = 3;

 

    public function source()
    {
        return $this->belongsTo('App\Source');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    public function getInternalSupplierCode($internal_supplier_id)
    {
        return \App\InternalSupplier::findOrFail($internal_supplier_id)->code;
    }

    /*
     * Gets DT Price from Product Parent Code
     * @param string $ppcode Product Parent Code
     *
     * @return decimal
     */

    public function getDTPrice($ppcode)
    {
        $priceObj = DB::table('pricing_data')
                ->where('parent_product_code', '=', $ppcode)->where('source_id', '=', 2)->select('price')
                ->orderBy('created_at', 'desc')
                ->first();
        $price = !empty($priceObj->price) ? number_format((float) $priceObj->price, 2) : '';
        return $price;
    }

}
