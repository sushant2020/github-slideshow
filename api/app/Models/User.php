<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Auth;

class User extends Authenticatable
{
	
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $guard_name = 'web';
    public $table = 'users';
    public $primaryKey = 'id';
    public $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'is_deleted',
        'inserted_by',
        'lastchanged_by'
    ];
    public $casts = [
        'email' => 'string',
        'firstname' => 'string',
        'lastname' => 'string',
        'password' => 'string',
        'remember_token' => 'string',
    ];
    protected $dates = ['created_at', 'updated_at'];

    //	public $hidden = ['PurchaseOrders', 'UserActivities', 'UserRoles', 'password'];

    /**

     * The attributes that should be hidden for arrays.
     *
     * @var array

     */
    protected $hidden = [
        'password',
        'remember_token',
        'inserted_by',
        'lastchanged_by'
    ];
    public $appends = [];

    /**
     * Get Roles of User model
     */
    public function roles()
    {
        return $this->belongsToMany(
                        'Spatie\Permission\Models\Role',
                        'model_has_roles',
                        'model_id',
                        'role_id'
        );
    }

    /**
     * Gets the full name of user
     *
     * @return string Full name
     */
    public function getName()
    {
        return $this->firstname . " " . $this->lastname;
    }

    public function getType()
    {
        $user = Auth::user();
        $roles = !empty($user) ? $user->getRoleNames()->toArray() : [];
        $group = '';
        if(in_array('RDS-Reviewer', $roles)) {
            $group = 3;
        } else if(in_array('Sigma-Reviewer', $roles)) {
            $group = 1;
        }
        return $group;
    }
}
