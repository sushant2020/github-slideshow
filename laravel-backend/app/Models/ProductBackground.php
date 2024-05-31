<?php

namespace App\Models;
use Exception;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Models\ProductTaskUser;
use App\Models\Product;
use App\Models\User;
use App\Models\Comment;

class ProductBackground extends Model
{

    public $table = 'product_background';
    public $primaryKey = 'product_background_id';
    public $fillable = ['product_background_id', 'info', 'product_id', 'created_at', 'inserted_by'];
    public $casts = [];
    public $appends = [];

    public const TASK_IN_PROCESS = 0;
    public const TASK_COMPLETED = 0;
    public const CATEGORY_BACKGROUND = 1;
    public const CATEGORY_COMMENT = 2;
    public const CATEGORY_TASK = 3;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**


     * Get List of all Roles(Groups)
     *
     * it return role_id and role_name
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAllGroups()
    {
        $groups = DB::table('roles')
                ->select('id', 'name')
                ->where('is_deleted', '=', 0)
                ->where('name', '!=', 'Administrator')
                ->get();

        return $groups;
    }

    /**
     * Get List of all Users
     *
     * it return user_id and user_name
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAllUsers()
    {
        $users = DB::table('users')
                ->select('id', 'email', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS username"))
                ->where('is_deleted', '=', 0)
                ->orderBy('username', 'ASC')
                ->get();
        return $users;
    }

    /**
     * Get List of all SPOT Codes
     *
     *
     * @return \Illuminate\Http\Response
     */
    public static function getSpots($productid)
    {
        $spots = [];
        $product = Product::where('prod_id', $productid)->select('ac4')->first();
        $ac4 = !empty($product->ac4) && isset($product->ac4) ? $product->ac4 : '';
//        $spots = DB::table('DwProduct as dp')
//                 ->join('spots as p', 'p.Product_Code', '=', 'dp.Product_Code')
//                ->select('dp.Product_Id', 'dp.Product_Code')
//                ->where('dp.Product_Status', '=', 'Live')
//                ->where('dp.Product_AC_4', '=', $ac4)
//                ->where('p.SPOT', '=', 1)
//                ->orderBy('dp.Product_Code', 'ASC')
//                ->get();
         $spots = DB::table('DwProduct as dp')
                 ->join('spot_codes as p', 'p.SPOT', '=', 'dp.Product_Code')
                ->select('dp.Product_Id', 'p.SPOT as Product_Code')
                 
                ->where('dp.Product_Status', '=', 'Live')
                ->where('p.Agg_Code', '=', $ac4)
                ->orderBy('p.SPOT', 'ASC')
                 ->distinct()
                ->get();
        
      if(empty($spots) || count($spots) == 0 ) {
            $spots = DB::table('DwProduct')
                ->select('Product_Id', 'Product_Code')
                ->where('Product_Status', '=', 'Live')
                ->where('Product_AC_4', '=', $ac4)
                ->where('Product_AC_5', '=', 'SPOT')
                ->orderBy('Product_Code', 'ASC')
                ->get();
        }
        return $spots;
    }
    
    
    
     

    /**
     * Gets the list of latest 5 comments mapped/attached to provided product id
     *
     *  @param  \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public function getComments($productid)
    {
        $comments = DB::table('product_background as c')
                ->join('users', 'users.id', '=', 'c.inserted_by')
                ->where('c.category', '=', self::CATEGORY_COMMENT)
                ->where('c.is_deleted', '=', 0)
                ->where('c.product_id', '=', $productid)
                ->select('comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                ->orderBy('c.created_at', 'DESC')
                ->offset(0)->limit(7)
                ->get();

        return $comments;
    }

    /**
     * Get the all active Users List and Latest comments as well as All comments added for product
     *
     * @param  \App\Models\Product $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public function getCommetsDetails($productid)
    {

        $users = [];
        $limit = 7;
        $users = self::getAllUsers();
        $spots = self::getSpots($productid);
       
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");

        $background = DB::table('product_background as c')
                ->join('users', 'users.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.product_id', '=', $productid)
                ->select('info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                ->orderBy('c.created_at', 'DESC')
                ->offset(0)->limit($limit)
                ->get();

        $product_latest_comments = DB::table('comments as c')
                ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                ->join('users', 'users.id', '=', 'spc.inserted_by')
                ->whereIn('c.cgroup', [1, 4])
                ->where('c.is_deleted', '=', 0)
                ->whereIn('spc.product_id', $productIds)
                ->select('c.comment_id','c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                ->orderBy('c.created_at', 'DESC')
                ->offset(0)->limit($limit)
                ->get();

        return ["spots" => $spots, "users" => $users, "background" => $background, "latest_comments" => $product_latest_comments];
    }

    /**
     * Get the all active Users List and Latest comments as well as All comments added for product
     *
     * @param  \App\Models\Product $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestBackground()
    {


        $limit = 10;

        $background = DB::table('product_background as pb')
                ->join('users', 'users.id', '=', 'pb.inserted_by')
                ->join('products as p', 'p.prod_id', '=', 'pb.product_id')
                ->where('pb.is_deleted', '=', 0)
                //->where('pb.product_id', '=', $productid)
                ->select('p.ac4', 'pb.info', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'pb.created_at')
                ->orderBy('pb.created_at', 'DESC')
                ->offset(0)->limit($limit)
                ->get();

        return $background;
    }

    /**
     * Gets the list of historical comments mapped/attached to provided product id 
     * as per pagination
     *
     * @param  \App\Models\Product  $productid The Product ID
     * @param  int  $page The Page number
     *
     * @return \Illuminate\Http\Response
     */
    public function getHistoricalCommentsDetails($productid, $page, $sort)
    {
        $limit = 10;
        $page = (int) $page;
        $sort = (int) $sort;

        $sorting = "DESC";
        if ($sort == 1)
            $sorting = "ASC";

        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        $comments = DB::table('comments as c')
                ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                ->join('users', 'users.id', '=', 'c.inserted_by')
                ->whereIn('c.cgroup', [1, 2])
                ->where('c.is_deleted', '=', 0)
                ->whereIn('spc.product_id', $productIds)
                ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                ->orderBy('c.created_at', $sorting)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        return $comments;
    }

    public function getHistoricalCommentsDetailsCount($productid)
    {

        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");

        $comments = DB::table('comments as c')
                ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                ->join('users', 'users.id', '=', 'c.inserted_by')
                ->whereIn('c.cgroup', [1, 2])
                ->where('c.is_deleted', '=', 0)
                ->whereIn('spc.product_id', $productIds)
                ->count();

        return $comments;
    }

    /**
     * Gets the list of historical Background mapped/attached to provided product id 
     * as per pagination
     *
     * @param  \App\Models\Product  $productid The Product ID
     * @param  int  $page The Page number
     *
     * @return \Illuminate\Http\Response
     */
    public function getHistoricalBackground($productid, $page, $sort)
    {
        $limit = 10;
        $page = (int) $page;
        $sort = (int) $sort;

        $sorting = "DESC";
        if ($sort == 1)
            $sorting = "ASC";

        $comments = DB::table('product_background as c')
                ->join('users', 'users.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.product_id', '=', $productid)
                ->select('info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                ->orderBy('c.created_at', $sorting)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        return $comments;
    }

    public function getHistoricalBackgroundCount($productid, $page)
    {
        $page = (int) $page;

        $comments = DB::table('product_background as c')
                ->join('users', 'users.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.product_id', '=', $productid)
                ->select('info as comment')
                ->count();

        return $comments;
    }

    /*
     * Gets top 10 tasks with status "Pending" assigned to me listed by task creation date in decending order
     * 
     * 
     */

    public function getPendingTasksAssignedToMe()
    {
        $limit = 10;

        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;

        //If user is Administrator, show all tasks
        if ($user->hasRole('Administrator')) {

            $usersTask = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                    })
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('pcu.status', '=', 0)
                    ->select('c.comment_id as task_id','pp.prod_id as productpage_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                            DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                            'pcu.completed_at as completed_on')
                    ->orderBy('c.created_at', "DESC")
                    ->limit($limit)
                    ->get();

            return $usersTask;
        } else {
            $usersTask = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                    })
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('pcu.status', '=', 0)
                    ->where('pcu.user_id', '=', $userId)
                    ->select('c.comment_id as task_id','pp.prod_id as productpage_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                            DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                            'pcu.completed_at as completed_on')
                    ->orderBy('c.created_at', "DESC")
                    ->limit($limit)
                    ->get();
            return $usersTask;
        }
    }

    /*
     * Gets top 10 tasks with status "Pending" assigned to me listed by task creation date in decending order
     * 
     * 
     */

    public static function getProductPendingTasksAssignedToMe($prodid)
    {
        $limit = 10;

        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $prodid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        //If user is Administrator, show all tasks
        if ($user->hasRole('Administrator')) {

            $usersTask = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('pcu.status', '=', 0)
                    ->whereIn('pcu.product_id', $productIds)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                            DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                            'pcu.completed_at as completed_on')
                    ->orderBy('c.created_at', "DESC")
                    ->limit($limit)
                    ->get();

            return $usersTask;
        } else {
            $usersTask = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('pcu.status', '=', 0)
                    ->where('pcu.user_id', '=', $userId)
                    ->whereIn('pcu.product_id', $productIds)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                            DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                            'pcu.completed_at as completed_on')
                    ->orderBy('c.created_at', "DESC")
                    ->limit($limit)
                    ->get();
            return $usersTask;
        }
    }

    /*
     * Gets tasks with status "Pending" assigned by me listed by task creation date in decending order
     * 
     * 
     */

    public function getPendingTasksAssignedByMe()
    {
        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;
        $limit = 10;

        $usersTask = DB::table('comments as c')
                ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                 ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                    })
                ->join('users as u', 'u.id', '=', 'pcu.user_id')
                ->join('users as us', 'us.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.cgroup', '=', 3)
                ->where('u.is_deleted', '=', 0)
                ->where('pcu.status', '=', 0)
                ->where('pcu.inserted_by', '=', $userId)
                ->select('c.comment_id as task_id', 'pp.prod_id as productpage_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                        DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                        'pcu.completed_at as completed_on')
                ->orderBy('c.created_at', "DESC")
                ->limit($limit)
                ->get();
        return $usersTask;
    }

    /*
     * Gets tasks assigned to me listed by task creation date in decending order
     * 
     * 
     */

    public function getUsersTasks($page, $sort)
    {
        $limit = 10;
        $page = (int) $page;
        $sort = (int) $sort;

        $sorting = "DESC";
        if ($sort == 1) {
            $sorting = "ASC";
        }


        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;

        //If user is Administrator, show all tasks
        if ($user->hasRole('Administrator')) {

            $usersTask = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                            DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                            'pcu.completed_at as completed_on')
                    ->orderBy('c.created_at', $sorting)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            return $usersTask;
        } else {
            $usersTask = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('pcu.user_id', '=', $userId)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                            DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                            'pcu.completed_at as completed_on')
                    ->orderBy('c.created_at', $sorting)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();
            return $usersTask;
        }
    }

    //count of the entries of the above function
    public function getUsersTasksCount()
    {
        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;

        //If user is Administrator, show all tasks
        if ($user->hasRole('Administrator')) {

            $taskCount = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->select('c.comment_id as task_id')
                    ->count();

            return $taskCount;
        } else {
            $taskCount = DB::table('comments as c')
                    ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                    ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                    ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('pcu.user_id', '=', $userId)
                    ->select('c.comment_id as task_id')
                    ->count();
            return $taskCount;
        }
    }

    // the tasks which are assigned by the user/admin
    public function getUsersTasksAssignByMe($page, $sort)
    {
        $limit = 10;
        $page = (int) $page;
        $sort = (int) $sort;

        $sorting = "DESC";
        if ($sort == 1) {
            $sorting = "ASC";
        }

        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;

        $usersTask = DB::table('comments as c')
                ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                ->join('users as u', 'u.id', '=', 'pcu.user_id')
                ->join('users as us', 'us.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.cgroup', '=', 3)
                ->where('u.is_deleted', '=', 0)
                ->where('pcu.inserted_by', '=', $userId)
                ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                        DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                        'pcu.completed_at as completed_on')
                ->orderBy('c.created_at', $sorting)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();
        return $usersTask;
    }

    //count of the entries of the above function
    public function getUsersTasksAssignByMeCount()
    {
        $userId = !empty(Auth::user()) ? Auth::user()->id : NULL;

        $taskCount = DB::table('comments as c')
                ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                ->join('users as u', 'u.id', '=', 'pcu.user_id')
                ->join('users as us', 'us.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.cgroup', '=', 3)
                ->where('u.is_deleted', '=', 0)
                ->where('pcu.inserted_by', '=', $userId)
                ->select('c.comment_id as task_id')
                ->count();

        return $taskCount;
    }

    /* public function getUserGroupsTasks($pending)
      {
      $user = Auth::user();
      $userId = !empty($user) ? $user->id : NULL;
      $roleIds = !empty($user->roles()->get()) ? $user->roles()->get()->pluck('id')->toArray() : [];

      if ($pending == 'pending') {
      //If user is Administrator, show all tasks
      if ($user->hasRole('Administrator')) {

      $usersRoleTask = DB::table('comments as c')
      ->join('product_comment_role as pcr', 'c.comment_id', '=', 'pcr.comment_id')
      ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
      ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'pcr.role_id')
      ->join('users as u', 'u.id', '=', 'mhr.model_id')
      ->join('users as us', 'us.id', '=', 'c.inserted_by')
      ->where('c.is_deleted', '=', 0)
      ->where('u.is_deleted', '=', 0)
      ->where('c.cgroup', '=', 3)
      ->where('c.status', '=', 0)
      ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE
      WHEN c.status = '0' THEN 'Pending'
      WHEN c.status = '1' THEN 'Completed'
      ELSE 'Pending'
      END) AS status"), 'pcu.status as status_id',
      DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
      'c.completed_at as completed_on')
      ->get();

      return $usersRoleTask;
      } else {
      $usersRoleTask = DB::table('comments as c')
      ->join('product_comment_role as pcr', 'c.comment_id', '=', 'pcr.comment_id')
      ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
      ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'pcr.role_id')
      ->join('users as u', 'u.id', '=', 'mhr.model_id')
      ->join('users as us', 'us.id', '=', 'c.inserted_by')
      ->where('c.is_deleted', '=', 0)
      ->where('u.is_deleted', '=', 0)
      ->where('c.cgroup', '=', 3)
      ->where('c.status', '=', 0)
      ->whereIn('pcr.role_id', $roleIds)
      ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE
      WHEN c.status = '0' THEN 'Pending'
      WHEN c.status = '1' THEN 'Completed'
      ELSE 'Pending'
      END) AS status"), 'pcu.status as status_id',
      DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
      'c.completed_at as completed_on')
      ->get();

      return $usersRoleTask;
      }
      } else {
      //If user is Administrator, show all tasks
      if ($user->hasRole('Administrator')) {

      $usersRoleTask = DB::table('comments as c')
      ->join('product_comment_role as pcr', 'c.comment_id', '=', 'pcr.comment_id')
      ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
      ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'pcr.role_id')
      ->join('users as u', 'u.id', '=', 'mhr.model_id')
      ->join('users as us', 'us.id', '=', 'c.inserted_by')
      ->where('c.is_deleted', '=', 0)
      ->where('u.is_deleted', '=', 0)
      ->where('c.cgroup', '=', 3)
      ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE
      WHEN c.status = '0' THEN 'Pending'
      WHEN c.status = '1' THEN 'Completed'
      ELSE 'Pending'
      END) AS status"), 'pcu.status as status_id',
      DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
      'c.completed_at as completed_on')
      ->get();

      return $usersRoleTask;
      } else {
      $usersRoleTask = DB::table('comments as c')
      ->join('product_comment_role as pcr', 'c.comment_id', '=', 'pcr.comment_id')
      ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
      ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'pcr.role_id')
      ->join('users as u', 'u.id', '=', 'mhr.model_id')
      ->join('users as us', 'us.id', '=', 'c.inserted_by')
      ->where('c.is_deleted', '=', 0)
      ->where('u.is_deleted', '=', 0)
      ->where('c.cgroup', '=', 3)
      ->whereIn('pcr.role_id', $roleIds)
      ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE
      WHEN c.status = '0' THEN 'Pending'
      WHEN c.status = '1' THEN 'Completed'
      ELSE 'Pending'
      END) AS status"), 'pcu.status as status_id',
      DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
      'c.completed_at as completed_on')
      ->get();

      return $usersRoleTask;
      }
      }
      }
     */

    /**
     * Gets logged in user pending tasks assigned to me
     *
     * */
    public function getLoggedInUserPendingTasks()
    {

        $userId = !empty(Auth::user()) ? Auth::user()->id : NULL;

        $usersTask = DB::table('comments as c')
                ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                ->join('users as u', 'u.id', '=', 'pcu.user_id')
                ->join('users as us', 'us.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.cgroup', '=', 3)
                ->where('u.is_deleted', '=', 0)
                ->where('pcu.status', '=', 0)
                ->where('pcu.user_id', '=', $userId)
                ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', 'c.created_at as created_on')
                ->orderBy('c.created_at', "DESC")
                ->get();
        return $usersTask;
    }

    /**
     * Gets logged in user pending tasks assigned to me
     *
     * */
    public function getUserPendingTasks()
    {

        $userId = !empty(Auth::user()) ? Auth::user()->id : NULL;

        $usersTask = DB::table('comments as c')
                ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                ->join('users as u', 'u.id', '=', 'pcu.user_id')
                ->join('users as us', 'us.id', '=', 'c.inserted_by')
                ->where('c.is_deleted', '=', 0)
                ->where('c.cgroup', '=', 3)
                ->where('u.is_deleted', '=', 0)
                ->where('pcu.status', '=', 0)
                ->where('pcu.user_id', '=', $userId)
                ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                        DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                        'pcu.completed_at as completed_on')
                ->orderBy('c.created_at', "DESC")
                ->get();
        return $usersTask;
    }

    /**
     * Sends email notification for user to which task is assigned
     * 
     * @param array $userIds The array of user ids
     * @param  string $ac4Code The AC4
     * @param  string $task The Task
     * 
     * @return void
     */
    public static function sendTaskNotification($userIds, $ac4Code, $spotCode, $task)
    {
        $userEmails = DB::table('users')->whereIn('id', $userIds)->where("users.is_deleted", 0)
                        ->select('email')->get()->pluck('email')->toArray();
        $sendData = ["email" => $userEmails, "ac4" => $ac4Code, "spot" => $spotCode, "task" => $task];
        $subject = "Task is assigned for product " . $ac4Code . " - " . $spotCode;
        \Mail::send('emails.task_notification', ['data' => $sendData], function ($message) use ($sendData, $subject) {
            $message->to($sendData["email"])->subject($subject);
        });
    }

    /**
     * Sends email notification for user to which task is assigned
     * 
     * @param array $userIds The array of user ids
     * @param  string $ac4Code The AC4
     * @param  string $task The Task
     * 
     * @return void
     */
    public static function sendTaskCompletionNotification($task, $assignedTo, $completedAt, $completedBy, $ac4Code, $spotCode, $assignedby)
    {
        $adminName = !empty(config('services.admin.name')) ? config('services.admin.name') : '';
        //$adminEmail = !empty(config('services.admin.email')) ? config('services.admin.email') : '';
        $adminEmail = 'sushant@webdezign.co.uk';
        $completedAt = date_format($completedAt, "d-M-Y H:i:s");

        $sendData = ["name" => $adminName, "email" => $adminEmail, "product" => $ac4Code, "spot" => $spotCode, "task" => $task, "assignedTo" => $assignedTo,
            "completedAt" => $completedAt, "completedBy" => $completedBy];
        $subject = "Task is completed for product " . $ac4Code . ' -' . $spotCode;
       
        \Mail::send('emails.task_completion_notification', ['data' => $sendData], function ($message) use ($assignedby, $subject) {
            $message->to($assignedby)->subject($subject);
        });
        
    }

    /**
     * Update task || Mark as complete or incomplete
     *
     * @param array $request
     * @param int $taskid TaskId
     * 
     * @return array The array of error } success messages
     */
    public static function updateTask($requestData, $taskid)
    {
        $status = (int) trim($requestData['status']);
        $productId = !empty($requestData['product_id']) && isset($requestData['product_id']) ? (int) trim($requestData['product_id']) : NULL;
        $userId = !empty($requestData['user_id']) && isset($requestData['user_id']) ? (int) trim($requestData['user_id']) : NULL;
        $currentDateTime = \Carbon\Carbon::now();
        $loggedInUser = Auth::user()->id;
        $data = $errors = [];
        $message = '';

        $userTask = ProductTaskUser::where(["task_id" => $taskid, "product_id" => $productId, "user_id" => $userId])->first();
        $assignedBy = !empty($userTask->inserted_by) && isset($userTask->inserted_by) ? $userTask->inserted_by : '';
       
        if (empty($userTask)) {
            $errors[] = 'The user task does not exist';
        }

        if (!empty($userTask)) {
            $task = Comment::where(["comment_id" => $userTask->task_id])->pluck('title')->first();
            $dwProduct = DB::table('dbo.DwProduct')->where(["Product_Id" => $productId])->select('Product_AC_4', 'Product_Code')->first();

            $ac4Code = !empty($dwProduct->Product_AC_4) && isset($dwProduct->Product_AC_4) ? $dwProduct->Product_AC_4 : '';
            $spotCode = !empty($dwProduct->Product_Code) && isset($dwProduct->Product_Code) ? $dwProduct->Product_Code : '';
            if (empty($ac4Code)) {
                $errors[] = 'Product does not exist or product is not live';
            }
            $userData = User::where(["id" => $userId, "is_deleted" => 0])->select(DB::raw("CONCAT(users.firstname,' ',users.lastname) AS assignedTo"))->first();
            $asuserData = User::where(["id" => $assignedBy, "is_deleted" => 0])->select(DB::raw("users.email AS assignedby"))->first();
            $loggedInUserData = User::where(["id" => $loggedInUser])->select(DB::raw("CONCAT(users.firstname,' ',users.lastname) AS completedBy"))->first()->toArray();
            $completedBy = !empty($loggedInUserData['completedBy']) && isset($loggedInUserData['completedBy']) ? trim($loggedInUserData['completedBy']) : '';
            $assignedTo = !empty($userData->assignedTo) && isset($userData->assignedTo) ? trim($userData->assignedTo) : '';
            $assignedby = !empty($asuserData->assignedby) && isset($asuserData->assignedby) ? trim($asuserData->assignedby) : '';
     
            if (empty($userData)) {
                $errors[] = 'User does not exist or User account is disabled';
            }
            if (!empty($ac4Code) && !empty($userData)) {
                if ($status == 1) {
                    $message = 'Task is marked as complete successfully';
                    DB::table('product_task_user')
                            ->where(["task_id" => $taskid, "product_id" => $productId, "user_id" => $userId])
                            ->update(['status' => $status, 'updated_at' => $currentDateTime, 'lastchanged_by' => $loggedInUser,
                                'completed_at' => $currentDateTime, 'completed_by' => $loggedInUser]);

                    self::sendTaskCompletionNotification($task, $assignedTo, $currentDateTime, $completedBy, $ac4Code, $spotCode, $assignedby);
                }
                if ($status == 0) {
                    $message = 'Task is marked as incomplete successfully';
                    DB::table('product_task_user')
                            ->where(["task_id" => $taskid, "product_id" => $productId, "user_id" => $userId])
                            ->update(['status' => $status, 'updated_at' => $currentDateTime, 'lastchanged_by' => $loggedInUser, 'completed_at' => NULL, 'completed_by' => NULL]);
                }
            }
        }
        $data = ['message' => $message, 'errors' => $errors];
        return $data;
    }

    public static function searchComments($requestData)
    {

        $input = [];
        $productid = !empty($requestData['productid']) && isset($requestData['productid']) ? trim($requestData['productid']) : "";
        $from_date = !empty($requestData['from_date']) && isset($requestData['from_date']) ? trim($requestData['from_date']) : "";
        $to_date = !empty($requestData['to_date']) && isset($requestData['to_date']) ? trim($requestData['to_date']) : "";
        $comment = !empty($requestData['comment']) && isset($requestData['comment']) ? (string) trim($requestData['comment']) : "";
        $added_by = !empty($requestData['added_by']) && isset($requestData['added_by']) ? (int) trim($requestData['added_by']) : "";
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");

        if (empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {

            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->select('c.title as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'spc.created_at')
                    ->orderBy('spc.created_at', 'DESC')
                    ->get();
            return $comments;
        }
    }

    public static function searchCommentsCount($requestData)
    {

        $input = [];
        $productid = !empty($requestData['productid']) && isset($requestData['productid']) ? trim($requestData['productid']) : "";
        $from_date = !empty($requestData['from_date']) && isset($requestData['from_date']) ? trim($requestData['from_date']) : "";
        $to_date = !empty($requestData['to_date']) && isset($requestData['to_date']) ? trim($requestData['to_date']) : "";
        $comment = !empty($requestData['comment']) && isset($requestData['comment']) ? (string) trim($requestData['comment']) : "";
        $added_by = !empty($requestData['added_by']) && isset($requestData['added_by']) ? (int) trim($requestData['added_by']) : "";
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");

        if (empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('comments as c')
                    ->join('supplier_product_comments as spc', 'spc.comment_id', '=', 'c.comment_id')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->whereIn('spc.product_id', $productIds)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.title', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->whereIn('c.cgroup', [1, 2])
                    ->count();
            return $comments;
        }
    }

    public static function searchBackground($requestData)
    {
        $input = [];
        $productid = !empty($requestData['productid']) && isset($requestData['productid']) ? trim($requestData['productid']) : "";
        $from_date = !empty($requestData['from_date']) && isset($requestData['from_date']) ? trim($requestData['from_date']) : "";
        $to_date = !empty($requestData['to_date']) && isset($requestData['to_date']) ? trim($requestData['to_date']) : "";
        $comment = !empty($requestData['comment']) && isset($requestData['comment']) ? (string) trim($requestData['comment']) : "";
        $added_by = !empty($requestData['added_by']) && isset($requestData['added_by']) ? (int) trim($requestData['added_by']) : "";

        if (empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {

            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->select('c.info as comment', DB::raw("CONCAT(users.firstname,' ',users.lastname) AS added_by"), 'c.created_at')
                    ->orderBy('c.created_at', 'DESC')
                    ->get();
            return $comments;
        }
    }

    public static function searchBackgroundCount($requestData)
    {

        $input = [];
        $productid = !empty($requestData['productid']) && isset($requestData['productid']) ? trim($requestData['productid']) : "";
        $from_date = !empty($requestData['from_date']) && isset($requestData['from_date']) ? trim($requestData['from_date']) : "";
        $to_date = !empty($requestData['to_date']) && isset($requestData['to_date']) ? trim($requestData['to_date']) : "";
        $comment = !empty($requestData['comment']) && isset($requestData['comment']) ? (string) trim($requestData['comment']) : "";
        $added_by = !empty($requestData['added_by']) && isset($requestData['added_by']) ? (int) trim($requestData['added_by']) : "";

        if (empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->count();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->count();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->count();
            return $comments;
        }
        if (empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
        if (empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
        if (!empty($from_date) && !empty($to_date) && !empty($comment) && !empty($added_by)) {
            $comments = DB::table('product_background as c')
                    ->join('users', 'users.id', '=', 'c.inserted_by')
                    ->where('c.product_id', '=', $productid)
                    ->whereDate('c.created_at', '>=', $from_date)
                    ->whereDate('c.created_at', '<=', $to_date)
                    ->where('c.info', 'LIKE', '%' . $comment . '%')
                    ->where('c.inserted_by', '=', $added_by)
                    ->count();
            return $comments;
        }
    }

    // searching the tasks which are assigned to me
    public static function searchTasksToMe($requestData)
    {

        $input = [];
        $ac4 = !empty($requestData['ac4']) && isset($requestData['ac4']) ? trim($requestData['ac4']) : "";
        $from_date = !empty($requestData['from_date']) && isset($requestData['from_date']) ? trim($requestData['from_date']) : "";
        $to_date = !empty($requestData['to_date']) && isset($requestData['to_date']) ? trim($requestData['to_date']) : "";
        $task = !empty($requestData['task']) && isset($requestData['task']) ? (string) trim($requestData['task']) : "";

        $assigned_to = !empty($requestData['assigned_to']) && isset($requestData['assigned_to']) ? $requestData['assigned_to'] : "";
        $assigned_by = !empty($requestData['assigned_by']) && isset($requestData['assigned_by']) ? $requestData['assigned_by'] : "";

        //$limit = 10;
        //$page = (int) $page;
        //$sort = (int) $sort;

        $sorting = "DESC";
        //if($sort == 1) $sorting = "ASC";

        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;

        //If user is Administrator, show all tasks
        if ($user->hasRole('Administrator')) {

            $usersTask = DB::table('comments as c')
                        ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                     ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'pcu.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                ->where('c.cgroup', '=', 3)
                ->where('u.is_deleted', '=', 0)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4',
                            'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"),'pcu.status as status_id',
                    DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'pcu.created_at as created_on',
                    'pcu.completed_at as completed_on');

            if (!empty($ac4))
                $usersTask->where('p.ac4', $ac4);
            if (!empty($from_date))
                $usersTask->where('c.created_at', '>', $from_date);
            if (!empty($to_date))
                $usersTask->where('c.created_at', '<', $to_date);
            if (!empty($task))
                $usersTask->where('c.title', 'LIKE', '%' . $task . '%');
            if (!empty($assigned_to))
                $usersTask->where('pcu.user_id', '=', $assigned_to);
            if (!empty($assigned_by))
                $usersTask->where('pcu.inserted_by', '=', $assigned_by);

            $usersTask->orderBy('c.created_at', $sorting);

            return $usersTask->get();
        } else {
            $usersTask = DB::table('comments as c')
                        ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                     ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'pcu.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                ->where('c.cgroup', '=', 3)
                ->where('u.is_deleted', '=', 0)
                    ->where('pcu.user_id', '=', $userId)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task',  \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                    DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                    'pcu.completed_at as completed_on');

            if (!empty($ac4))
                $usersTask->where('p.ac4', $ac4);
            if (!empty($from_date))
                $usersTask->where('c.created_at', '>', $from_date);
            if (!empty($to_date))
                $usersTask->where('c.created_at', '<', $to_date);
            if (!empty($task))
                $usersTask->where('c.title', 'LIKE', '%' . $task . '%');
            if (!empty($assigned_to))
                $usersTask->where('pcu.user_id', '=', $assigned_to);
            if (!empty($assigned_by))
                $usersTask->where('pcu.inserted_by', '=', $assigned_by);



            $usersTask->orderBy('c.created_at', $sorting);

            return $usersTask->get();
        }
    }

    // searching the tasks which are assigned by me
    public static function searchTasksByMe($requestData)
    {

        $input = [];
        $ac4 = !empty($requestData['ac4']) && isset($requestData['ac4']) ? trim($requestData['ac4']) : "";
        $from_date = !empty($requestData['from_date']) && isset($requestData['from_date']) ? trim($requestData['from_date']) : "";
        $to_date = !empty($requestData['to_date']) && isset($requestData['to_date']) ? trim($requestData['to_date']) : "";
        $task = !empty($requestData['task']) && isset($requestData['task']) ? (string) trim($requestData['task']) : "";
        $assigned_to = !empty($requestData['assigned_to']) && isset($requestData['assigned_to']) ? (int) trim($requestData['assigned_to']) : "";
        $assigned_by = !empty($requestData['assigned_by']) && isset($requestData['assigned_by']) ? $requestData['assigned_by'] : "";
        //$limit = 10;
        //$page = (int) $page;
        //$sort = (int) $sort;

        $sorting = "DESC";
        //if($sort == 1) $sorting = "ASC";

        $user = Auth::user();
        $userId = !empty($user) ? $user->id : NULL;

        //If user is Administrator, show all tasks
        if ($user->hasRole('Administrator')) {

            $usersTask = DB::table('comments as c')
                        ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                     ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                      ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('pcu.inserted_by', '=', $userId)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task', \DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                    DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                    'pcu.completed_at as completed_on');

            if (!empty($ac4))
                $usersTask->where('p.ac4', $ac4);
            if (!empty($from_date))
                $usersTask->where('c.created_at', '>', $from_date);
            if (!empty($to_date))
                $usersTask->where('c.created_at', '<', $to_date);
            if (!empty($task))
                $usersTask->where('c.title', 'LIKE', '%' . $task . '%');
            if (!empty($assigned_to))
                $usersTask->where('pcu.user_id', '=', $assigned_to);
            if (!empty($assigned_by))
                $usersTask->where('pcu.inserted_by', '=', $assigned_by);
            $usersTask->orderBy('c.created_at', $sorting);

            return $usersTask->get();
        } else {
            $usersTask = DB::table('comments as c')
                        ->join('product_task_user as pcu', 'c.comment_id', '=', 'pcu.task_id')
                     ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pcu.product_id')
                    ->join('users as u', 'u.id', '=', 'pcu.user_id')
                    ->join('users as us', 'us.id', '=', 'c.inserted_by')
                    ->where('c.is_deleted', '=', 0)
                     ->where('c.cgroup', '=', 3)
                    ->where('u.is_deleted', '=', 0)
                    ->where('c.inserted_by', '=', $userId)
                    ->select('c.comment_id as task_id', 'pcu.product_id as product_id', 'u.id as user_id', 'p.Product_AC_4 as ac4', 'c.title as task',\DB::raw("(CASE 
                        WHEN pcu.status = '0' THEN 'Pending'
                        WHEN pcu.status = '1' THEN 'Completed' 
                        ELSE 'Pending' 
                       END) AS status"), 'pcu.status as status_id',
                    DB::raw("CONCAT(u.firstname,' ',u.lastname) AS assigned_to"), DB::raw("CONCAT(us.firstname,' ',us.lastname) AS assigned_by"), 'c.created_at as created_on',
                    'pcu.completed_at as completed_on');

            if (!empty($ac4))
                $usersTask->where('p.ac4', $ac4);
            if (!empty($from_date))
                $usersTask->where('c.created_at', '>', $from_date);
            if (!empty($to_date))
                $usersTask->where('c.created_at', '<', $to_date);
            if (!empty($task))
                $usersTask->where('c.title', 'LIKE', '%' . $task . '%');
            if (!empty($assigned_to))
                $usersTask->where('pcu.user_id', '=', $assigned_to);

            $usersTask->orderBy('c.created_at', $sorting);
            
            return $usersTask->get();
        }
    }

}
