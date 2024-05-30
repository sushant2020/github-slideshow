<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{

    public $table = 'user_activity';
    public $primaryKey = 'user_id';
    public $fillable = ['activity_id', 'user_id'];
    public $casts = [];
    public $hidden = ['Activity', 'User'];
    public $appends = [
        'activity_activity_id',
        'activity_description',
        'activity_inserted_by',
        'activity_lastchanged_by',
        'activity_module_id',
        'activity_name',
        'user_email',
        'user_firstname',
        'user_inserted_by',
        'user_is_deleted',
        'user_lastchanged_by',
        'user_lastname',
        'user_password',
        'user_remember_token',
        'user_user_id',
    ];

    public function Activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActivityActivityIdAttribute()
    {
        return data_get($this->Activity, 'activity_id', null);
    }

    public function getActivityDescriptionAttribute()
    {
        return data_get($this->Activity, 'description', null);
    }

    public function getActivityInsertedByAttribute()
    {
        return data_get($this->Activity, 'inserted_by', null);
    }

    public function getActivityLastchangedByAttribute()
    {
        return data_get($this->Activity, 'lastchanged_by', null);
    }

    public function getActivityModuleIdAttribute()
    {
        return data_get($this->Activity, 'module_id', null);
    }

    public function getActivityNameAttribute()
    {
        return data_get($this->Activity, 'name', '');
    }

    public function getUserEmailAttribute()
    {
        return data_get($this->User, 'email', '');
    }

    public function getUserFirstnameAttribute()
    {
        return data_get($this->User, 'firstname', '');
    }

    public function getUserInsertedByAttribute()
    {
        return data_get($this->User, 'inserted_by', null);
    }

    public function getUserIsDeletedAttribute()
    {
        return data_get($this->User, 'is_deleted', null);
    }

    public function getUserLastchangedByAttribute()
    {
        return data_get($this->User, 'lastchanged_by', null);
    }

    public function getUserLastnameAttribute()
    {
        return data_get($this->User, 'lastname', '');
    }

    public function getUserPasswordAttribute()
    {
        return data_get($this->User, 'password', '');
    }

    public function getUserRememberTokenAttribute()
    {
        return data_get($this->User, 'remember_token', '');
    }

    public function getUserUserIdAttribute()
    {
        return data_get($this->User, 'user_id', null);
    }

}
