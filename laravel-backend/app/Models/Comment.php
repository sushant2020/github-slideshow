<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    public $table = 'comments';
    public $primaryKey = 'comment_id';
    public $fillable = ['inserted_by', 'lastchanged_by', 'title', 'comment_id', 'is_deleted','cgroup'];
    public $casts = ['title' => 'string'];
    public $hidden = ['Products'];
    public $appends = [];

    public function products()
    {
        return $this->belongsToMany(DwProduct::class);
    }
    
    
    /*
     * Gets Comments
     * 
     */

    public function getComments($page, $sortcolumn, $sort)
    {
        $limit = 10;
        $page = (int) $page;
        $sort = (int) $sort;

        $sorting = "DESC";
        if ($sort == 1) {
            $sorting = "ASC";
        }

          if ($sortcolumn == 'created_on') {
            $sortcolumn = 'c.created_at';
            } else if ($sortcolumn == 'created_by') {
            $sortcolumn = 'u.firstname';
            } else if ($sortcolumn == 'comment') {
            $sortcolumn = 'c.title';
            } else if ($sortcolumn == 'spot') {
            $sortcolumn = 'p.Product_Code';
            } else if ($sortcolumn == 'ac4') {
            $sortcolumn = 'p.Product_AC_4';
            } 
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'c.comment_id', '=', 'spc.comment_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'spc.product_id')
                     ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                        })
                    ->join('users as u', 'u.id', '=', 'spc.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                     ->whereIn('c.cgroup',  [1,4])
                    ->where('u.is_deleted', '=', 0)
                    ->select('c.comment_id','pp.prod_id as productpage_id', 'spc.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4','p.Product_Code as spot', 'c.title as comment',
                       DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"), 'spc.created_at as created_on',
                           )
                     ->orderBy($sortcolumn, $sorting)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            return $comments;
        
    }
    
    
    
       /*
     * Gets last 24hrs added comments Comments
     * 
     */

    public function getTopComments()
    {
       
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'c.comment_id', '=', 'spc.comment_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'spc.product_id')
                     ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                        })
                    ->join('users as u', 'u.id', '=', 'spc.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->whereIn('c.cgroup',  [1,4])
//                    ->where('u.is_deleted', '=', 0)
                     ->where('spc.created_at', '>', Carbon::now()->subDay())
                    ->select('c.comment_id','pp.prod_id as productpage_id', 'spc.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4','p.Product_Code as spot', 'c.title as comment',
                       DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"), 'spc.created_at as created_on',
                           )
                     ->orderBy("c.created_at", "DESC")
                    ->get();

            return $comments;
        
    }
    
    
     /*
     * Gets Comments
     * 
     */

    public function getCommentsCount()
    {
            $commentsCount = 0;
            $commentsCount = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'c.comment_id', '=', 'spc.comment_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'spc.product_id')
                    ->join('users as u', 'u.id', '=', 'spc.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                     ->whereIn('c.cgroup',  [1,4])
                    ->where('u.is_deleted', '=', 0)
                    ->count();

            return $commentsCount;
        
    }
    
    
     /**
     * Update task |Comment's text
     *
     * @param array $request
     * @param int $id Task or comment Id
     * 
     * @return array The array of error } success messages
     */
    public static function updateTaskComment($requestData, $id)
    {
        $content = !empty($requestData['content']) && isset($requestData['content']) ? $requestData['content'] : '';
     
        $currentDateTime = \Carbon\Carbon::now();
        $loggedInUser = Auth::user()->id;
        $data = $errors = [];
        $message = '';
   
        
        $message = 'Task/Comment is updated successfully';
        DB::table('comments')
                ->where(["comment_id" => $id])
                ->update(['title' => $content, 'updated_at' => $currentDateTime, 'lastchanged_by' => $loggedInUser]);
                
            
        
        $data = ['message' => $message, 'errors' => $errors];
        return $data;
    }
    
    
      // searching the tasks which are assigned by me
    public static function filterComments($requestData, $page, $sortcolumn, $sort)
    {

        $cdata = [];
        
        $from_date = !empty($requestData['from_date']) && isset($requestData['from_date']) ? trim($requestData['from_date']) : "";
        $to_date = !empty($requestData['to_date']) && isset($requestData['to_date']) ? trim($requestData['to_date']) : "";
        $keyword = !empty($requestData['keyword']) && isset($requestData['keyword']) ? (string) trim($requestData['keyword']) : "";
        $added_by = !empty($requestData['added_by']) && isset($requestData['added_by']) ? $requestData['added_by'] : "";
        $sortcolumn = trim($sortcolumn);
        $limit = 10;
        if($sortcolumn == 'created_on') {
             $sortcolumn = 'spc.created_at';
        }
        
        if (empty($sortcolumn)) {
            $sortcolumn = 'spc.created_at';
            $sorder = "DESC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;

        //If user is Administrator, show all tasks
    

             $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'c.comment_id', '=', 'spc.comment_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'spc.product_id')
                     ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                        })
                    ->join('users as u', 'u.id', '=', 'spc.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                     ->whereIn('c.cgroup',  [1,4])
                    ->where('u.is_deleted', '=', 0)
                    ->select('c.comment_id','pp.prod_id as productpage_id', 'spc.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4','p.Product_Code as spot', 'c.title as comment',
                       DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"), 'spc.created_at as created_on',
                           );

            if (!empty($keyword))
                $comments->where(function ($query) use ($keyword) {
                 $query->where('c.title', 'LIKE', '%' . $keyword . '%')
                       ->orWhere('p.Product_AC_4', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('p.Product_Code', 'LIKE', '%' . $keyword . '%');
                });

            if (!empty($from_date) && !empty($to_date))
               $comments->whereDate('spc.created_at', '>=', $from_date)->whereDate('spc.created_at', '<=', $to_date);
            
        
            if (!empty($added_by))
                $comments->where('spc.inserted_by', '=', $added_by);
            
             $cdata["count"] = $comments->count();
               
               $cdata["comments"] = $comments->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)->get();
         
           
            return $cdata;
        
    }

}
