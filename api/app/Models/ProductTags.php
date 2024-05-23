<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductTags extends Model
{

    use HasFactory;

    public $table = 'product_tags';

    public const PRODUCT_TAG_LOW = 1;
    public const PRODUCT_TAG_MEDIUM = 2;
    public const PRODUCT_TAG_HIGH = 3;

    public $fillable = ['tag_id', 'product_id', 'updated_at', 'severity', 'end_date', 'inserted_by', 'lastchanged_by'];


    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Gets all active tags mapped to provided Product ID
     *
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAllCurrentTag($productid)
    {

        $product_current_tags = DB::table('product_tags as pt')->join("tags as t", "pt.tag_id", "=", "t.tag_id")
                ->select('pt.id', 'pt.tag_id', 'pt.product_id', 't.name', 'pt.severity')
                ->where('t.is_deleted', '=', 0)
                ->where('pt.product_id', $productid)
                ->whereNull('pt.end_date')->orderBy('pt.severity', 'DESC')
                ->get();
        return $product_current_tags;
    }

    /**
     * Gets the list of all historical tags mapped/attached to provided product id one month ago
     *
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAllHistoricalTag($productid)
    {


        $product_history_tags = DB::table('product_tags as pt')
                ->join("tags as t", "pt.tag_id", "=", "t.tag_id")
                ->select('pt.id', 'pt.tag_id', 'pt.product_id', 't.name', 'pt.severity', "pt.created_at as start_date",
                        "pt.end_date")
                ->where('t.is_deleted', '=', 0)
                ->where('pt.product_id', $productid)
                ->whereNotNull('pt.end_date')
                ->get();
        return $product_history_tags;
    }

    /**
     * Gets the list of all historical tags mapped/attached to provided product id latest one month
     *
     *
     * @return \Illuminate\Http\Response
     */
	public static function getHistoricalTag($productid)
    {
		
		//$effectiveDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveDate)));
		$prevSixMonDate = date('Y-m-d', strtotime('-6 month'));
        $tillDate = date('Y-m-d', strtotime('last day of last month'));
        $fromDate = date('Y-m-d', strtotime('first day of last month'));
        $product_history_tags = DB::table('product_tags as pt')
                ->join("tags as t", "pt.tag_id", "=", "t.tag_id")
                ->select('pt.id', 'pt.tag_id', 'pt.product_id', 't.name', 'pt.severity', "pt.created_at as start_date",
                        "pt.end_date")
                ->where('t.is_deleted', '=', 0)
                ->where('pt.product_id', $productid)
				->where('pt.created_at', '>', $prevSixMonDate)
				//->whereBetween('pt.created_at', [$date_now, $prevSixMonDate])
                ->whereNotNull('pt.end_date')->orderBy("pt.created_at", "desc")
                ->get();
		//dd($effectiveDate);
        return $product_history_tags;
    }

    /**
     * Get List of all active Tags
     *
     * it return tag_id and name
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAllTag()
    {
        $taglist = DB::table('tags')
                ->select('tag_id', 'name')
                ->where('is_deleted', '=', 0)
                ->get();
        return $taglist;
    }

    /**
     * Get Severity List
     *
     * It returns severity name and constant value
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAllSeverity()
    {

		
		
        $severitylist = [["id"=> 1, "name" => "LOW"],["id"=> 2 ,"name"=> "MEDIUM"], ["id"=> 3, "name" => "HIGH"]];

        return $severitylist;
    }

}
