<?php
use \Illuminate\Support\Facades\Auth;

Route::get('/', [
    'middleware' => ['auth', 'rbac:can,admin'], function () {
        return view('layouts/admin');
    }
]);
Auth::routes();

// -------------------------------------------------------------------
// 前端路由
// -------------------------------------------------------------------
Route::get('home/403', function () {
    return view('layouts/home');
});
Route::get('home/404', function () {
    return view('layouts/home');
});
Route::get('home/{subs?}', [
    'middleware' => ['auth', 'rbac:is,admin|user'], function () {
        return view('layouts/home');
    }
])->where(['subs' => '.*']);
// -------------------------------------------------------------------
// 前端api
// -------------------------------------------------------------------

// 修改用户密码
Route::post('base/change-password', function () {
    $password_old = request()->input('password_old');
    $password_new = request()->input('password_new');
    $user = \Illuminate\Support\Facades\Auth::user();
    // 如果老密码错误
    if (!Auth::attempt(['id' => $user->id, 'password' => $password_old], 0)) {
        $res = ['status' => 1, 'msg' => '老密码未通过验证'];
    } else {
        // 存入新密码
        $user->password = bcrypt($password_new);
        if (!$user->save()) {
            $res = ['status' => 2, 'msg' => '修改失败'];
        }
        else{
            $res = ['status' => 0];
        }
    }
    return response()->json($res);
});

// -------------------------------------------------------------------
// 后台路由
// -------------------------------------------------------------------

// 公共基础信息
Route::get('base/info', function(){
    return response()->json([
        'user' => \Illuminate\Support\Facades\Auth::user(),
    ]);
});
Route::get('admin/users', [
    'middleware' => ['auth', 'rbac:can,rbac_manage'], function () {
        return view('layouts/admin');
    }
]);
Route::get('admin/roles', [
    'middleware' => ['auth', 'rbac:can,rbac_manage'], function () {
        return view('layouts/admin');
    }
]);
Route::get('admin/{subs?}', [
    'middleware' => ['auth', 'rbac:can,admin'], function () {
        return view('layouts/admin');
    }
])->where(['subs' => '.*']);
// -------------------------------------------------------------------
// 后端api
// -------------------------------------------------------------------
Route::group(['namespace' => 'Admin'], function () {
    // user
    Route::get('admin-api/users', 'AdminController@users');
    Route::get('admin-api/users/info', 'AdminController@user');
    Route::post('admin-api/users/add', 'AdminController@addUser');
    Route::post('admin-api/users/delete', 'AdminController@deleteUser');
    Route::post('admin-api/users/role-change', 'AdminController@userRoleChange');
    Route::post('admin-api/users/user-change-usable', 'AdminController@userUsableChange');
    // permission
    Route::get('admin-api/permissions', 'AdminController@permissions');
    Route::post('admin-api/permission/add', 'AdminController@addPermission');
    Route::post('admin-api/permission/update', 'AdminController@updatePermission');
    Route::post('admin-api/permission/delete', 'AdminController@deletePermission');
    // role
    Route::get('admin-api/roles', 'AdminController@roles');
    Route::post('admin-api/roles/add', 'AdminController@addRole');
    Route::post('admin-api/roles/update', 'AdminController@updateRole');
    Route::post('admin-api/roles/delete', 'AdminController@deleteRole');
    Route::get('admin-api/roles/role-permissions', 'AdminController@rolePermissions');
    Route::post('admin-api/roles/role-permissions-assign', 'AdminController@rolePermissionsAssign');
    Route::get('admin-api/roles/role-cinemas', 'AdminController@roleCinemas');
    Route::post('admin-api/roles/role-cinemas-assign', 'AdminController@roleCinemasAssign');
});

