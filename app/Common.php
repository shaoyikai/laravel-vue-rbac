<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use PHPZen\LaravelRbac\Model\Role;

class Common extends Model {
    public static $deviceDict = [
        'projector' => 1,
        'server' => 3,
        'audio' => 2,
        'power' => 4
    ];

    // 当前登录角色权限列表
    public static function getCurPermissions()
    {
        $user = Auth::user();
        $firstRole = $user->roles()->first();

        if(empty($firstRole)){
            return json_encode([]);
        } else {
            $firstRole = $firstRole->toArray();
        }
        $role = Role::find($firstRole['id']);
        $own = $role->permissions->all();
        $rbac = [];
        foreach ($own as $key => $o) {
            $slug = $o->slug;
            $rbac[$slug] = Auth::user()->canDo($o->slug);
        }
        return json_encode($rbac);
    }
}