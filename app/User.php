<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use PHPZen\LaravelRbac\Model\Role;
use PHPZen\LaravelRbac\Traits\Rbac;

class User extends Authenticatable
{
    use Notifiable;
    use Rbac;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles(){
        return $this->belongsToMany('PHPZen\LaravelRbac\Model\Role','role_user','user_id','role_id');
    }

    public static function userPermissions(){
        $user_id = Auth::id();
        $user = self::find($user_id);
        $firstRole = $user->roles()->first();
        if(empty($firstRole)){
            return false;
        } else {
            $firstRole = $firstRole->toArray();
        }
        $role = Role::find($firstRole['id']);
        $own = $role->permissions->all();
        return $own;
    }
}
