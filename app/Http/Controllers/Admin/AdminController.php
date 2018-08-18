<?php

namespace App\Http\Controllers\Admin;

use App\CinemaInfo;
use App\CinemaRole;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use PHPZen\LaravelRbac\Model\Permission;
use PHPZen\LaravelRbac\Model\Role;

class AdminController extends Controller
{
    // 用户信息
    public function users()
    {
        $pageSize = $_GET['pageSize'];

        $all = User::select(['id','phone','name','email','usable'])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);

        foreach ($all as $one) {
            $role = $one->roles()->first();

            $one->role = !is_null($role) ? $role->toArray() : [];
            $one->click_change = 0;
            $one->select_value = '';
        }

        return $all;
    }

    public function user()
    {
        $user = User::find($_GET['id'],['id','name','email','usable']);
        $info = $user->toArray();
        $firstRole = $user->roles()->first();
        $info['role'] = !is_null($firstRole) ? $firstRole->toArray() : [];
        return response()->json($info);
    }

    // 增加角色
    public function addUser()
    {
        $post = json_decode(file_get_contents("php://input"));
        // 验证slug是否已经存在
        $one = User::where('name', $post->name)
            ->orWhere('email',$post->email)
            ->orWhere('phone',$post->phone)
            ->first();
        if(!empty($one)){
            return response()->json(['status' => 1,'msg' => '用户名、手机号或者邮箱已经存在！']);
        }
        $user = new User();
        $user->name = $post->name;
        $user->phone = $post->phone;
        $user->email = $post->email;
        $user->password = bcrypt($post->password);
        $user->usable = 1;
        if(!$user->save()){
            return response()->json(['status' => 2,'msg' => '创建失败']);
        }
        // 创建成功后，需要给用户分配角色
        if($post->role_id){
            $user->roles()->attach($post->role_id);
        }
        return response()->json(['status' => 0, 'user' => $user]);
    }

    // 删除用户
    public function deleteUser()
    {
        $post = json_decode(file_get_contents("php://input"));
        $one = User::find($post->id);
        if(empty($one)){
            return response()->json(['status' => 1,'msg' => '记录不存在']);
        }

        if(!$one->delete()){
            return response()->json(['status' => 2,'删除失败']);
        }
        return response()->json(['status' => 0]);
    }

    public function userRoleChange()
    {
        $post = json_decode(file_get_contents("php://input"));
        $user = User::find($post->id);

        DB::transaction(function () use ($user,$post) {
            // 修改user
            DB::update('update users set phone = ? , email = ?  where id = ?',
                [$post->phone,$post->email,$post->id]);

            // 修改user的role
            if(isset($post->role_id) && $post->role_id){
                $user->roles()->detach();
                $user->roles()->attach($post->role_id);
            }
        });
        $firstRole = $user->roles()->first();
        return response()->json([
            'status' => 0,
            'role' => !is_null($firstRole) ? $firstRole->toArray() : []
        ]);
    }

    // 用户的激活或禁用
    public function userUsableChange()
    {
        $post = json_decode(file_get_contents("php://input"));

        $user = User::find($post->id);
        if($user->usable == 1){
            $user->usable = 0;
        }else{
            $user->usable = 1;
        }
        if(!$user->save()){
            return response()->json(['status' => 1,'msg'=> '保存失败']);
        }
        return response()->json(['status' => 0]);
    }

    // 所有权限
    public function permissions()
    {
        $all = Permission::all(['id','name','slug','description',DB::raw('0 as isActive')])->toJson();
        return $all;
    }

    // 增加角色
    public function addPermission()
    {
        $post = json_decode(file_get_contents("php://input"));
        // 验证slug是否已经存在
        $one = Permission::where('slug', $post->slug)->first();
        if(!empty($one)){
            return response()->json(['status' => 1,'msg' => '标识符已经存在']);
        }
        $permission = new Permission();
        $permission->name = $post->name;
        $permission->slug = $post->slug;
        $permission->description = isset($post->description) ? $post->description : '';
        if(!$permission->save()){
            return response()->json(['status' => 2,'保存失败']);
        }
        $permission->isActive = 0;
        return response()->json(['status' => 0, 'permission' => $permission]);
    }

    public function updatePermission()
    {
        $post = json_decode(file_get_contents("php://input"));

        // 找到permission
        $res = Permission::find($post->id)->update([
            'name' => $post->name,
            'description' => $post->description,
        ]);

        if(!$res){
            return response()->json(['status' => 2,'保存失败']);
        }
        return response()->json(['status' => 0]);
    }

    // 删除权限
    public function deletePermission()
    {
        $post = json_decode(file_get_contents("php://input"));
        $one = Permission::find($post->id);
        if(empty($one)){
            return response()->json(['status' => 1,'msg' => '记录不存在']);
        }

        if(!$one->delete()){
            return response()->json(['status' => 2,'删除失败']);
        }
        return response()->json(['status' => 0]);
    }

    // 所有权限
    public function roles()
    {
        $all = DB::table('roles')
            ->select(['id','name','slug','description',DB::raw('0 as isActive')])
            //->where('slug', '<>', 'admin') //不允许展示超级管理员
            ->orderBy('created_at','desc')
            ->get();
        return $all;
    }

    // 增加角色
    public function addRole()
    {
        $post = json_decode(file_get_contents("php://input"));
        // 验证name或slug是否已经存在
        $one = Role::where('slug', $post->slug)
            ->orWhere('name',$post->name)
            ->first();
        if(!empty($one)){
            return response()->json(['status' => 1,'msg' => '角色名或标识符已经存在']);
        }
        $role = new Role();
        $role->name = $post->name;
        $role->slug = $post->slug;
        $role->description = $post->description;
        if(!$role->save()){
            return response()->json(['status' => 2,'保存失败']);
        }
        $role->isActive = 0;
        return response()->json(['status' => 0, 'role' => $role]);
    }

    public function updateRole()
    {
        $post = json_decode(file_get_contents("php://input"));

        // 验证name是否已经存在
        $one = Role::where('name',$post->name)
            ->first();
        if(!empty($one)){
            return response()->json(['status' => 1,'msg' => '角色名已经存在']);
        }

        // 找到role
        $res = Role::find($post->id)->update([
            'name' => $post->name,
            'description' => $post->description,
        ]);

        if(!$res){
            return response()->json(['status' => 2,'保存失败']);
        }
        return response()->json(['status' => 0]);
    }

    // 删除角色
    public function deleteRole()
    {
        $post = json_decode(file_get_contents("php://input"));
        $one = Role::find($post->id);
        if(empty($one)){
            return response()->json(['status' => 1,'msg' => '记录不存在']);
        }
        if($one->slug == 'admin'){
            return response()->json(['status' => 3,'msg' => '系统管理员账号不允许被删除！']);
        }

        if(!$one->delete()){
            return response()->json(['status' => 2,'删除失败']);
        }
        return response()->json(['status' => 0]);
    }

    // 某个role拥有的所有permissions
    public function rolePermissions()
    {
        $role = Role::find($_GET['id']);
        $assigned = $role->permissions->all();
        return response()->json($assigned);
    }

    // 保存某个role选择的permissions
    public function rolePermissionsAssign()
    {
        $post = json_decode(file_get_contents("php://input"));
        $roleId = $post->id;
        $ids = $post->assigned_ids;

        $role = Role::find($roleId);
        $ownPermissions = $role->permissions->all();

        $ids_before = [];
        foreach ($ownPermissions as $o) {
            $ids_before[] = $o['id'];
        }
        $role->permissions()->detach(); // 解绑之前拥有的权限
        $role->permissions()->attach($ids); // 绑定新增的权限
        return response()->json(['status' => 0]);
    }
}
