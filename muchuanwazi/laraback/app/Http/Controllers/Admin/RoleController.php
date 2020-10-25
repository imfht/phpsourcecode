<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RolePermissionRequest;
use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use DB;
use MessageAlert;
use UserLog;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role_manage')->except(['rolePermission', 'rolePermissionStore']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(15);
        $data = array('page_title' => '角色管理', 'page_description' => '新建、修改、管理系统角色数据',
            'roles' => $roles);
        return view('admin.role.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array('page_title' => '新建角色', 'page_description' => '新建一个角色', 'function' => __FUNCTION__);
        return view('admin.role.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        Role::create($request->all());
        app('messageAlert')->store('保存成功', MessageAlert::SUCCESS, '新角色信息保存成功。');
        UserLog::info('Store a role.[' . $request->name . ']');
        return redirect()->route('role.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $data = array('page_title' => '角色信息', 'page_description' => '显示系统角色信息',
            'role' => $role);
        return view('admin.role.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $data = array('page_title' => '更新角色', 'page_description' => '更新系统角色数据', 'function' => __FUNCTION__,
            'role' => $role);
        return view('admin.role.create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $role = Role::find($id);
        $role->update($request->all());
        app('messageAlert')->store('更新成功', MessageAlert::SUCCESS, '角色信息更新成功。');
        UserLog::info('Update a role [' . $role->name . ']');
        return redirect()->route('role.show', ['id' => $role->id]);
    }


    /**
     * Show the form for destroy the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function remove(Role $role)
    {
        $rolePermissionCount = DB::table('permission_role')->where('role_id', '=', $role->id)->count();
        $roleUserCount = DB::table('role_user')->where('role_id', '=', $role->id)->count();
        $data = array('page_title' => '删除角色', 'page_description' => '删除系统角色，删除后，此角色与模块以及用户的关联信息也将同时删除。', 'function' => __FUNCTION__,
            'role' => $role, 'rolePermissionCount' => $rolePermissionCount, 'roleUserCount' => $roleUserCount);
        return view('admin.role.remove', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->users()->sync([]); // Delete relationship data
        $role->perms()->sync([]); // Delete relationship data

        $role->forceDelete();

        app('messageAlert')->store('删除成功', MessageAlert::SUCCESS, '角色信息删除成功。');
        UserLog::info('Destroy a role [' . json_encode($role) . ']');
        return redirect()->route('role.index');
    }


    public function rolePermission(Role $role)
    {
        $permissions = Permission::all();
        $rolePermission = \DB::table('permission_role')->where('role_id', $role->id)->pluck('permission_id')->toArray();
        $items = [];
        $rolePermissionCount = 0;
        foreach ($permissions as $permission) {
            $checked = false;
            if (in_array($permission->id, $rolePermission)) {
                $checked = true;
                $rolePermissionCount++;
            }
            $items[] = ['label' => $permission->display_name, 'value' => $permission->id, 'name' => 'rolePermission[]', 'isCheck' => $checked];
        }


        $data = array('page_title' => '角色赋权', 'page_description' => '给系统角色添加可管理的模块信息。',
            'role' => $role, 'rolePermissionCount' => $rolePermissionCount, 'rolePermission' => $items);
        return view('admin.role.role_permission', $data);
    }

    public function rolePermissionStore(RolePermissionRequest $request)
    {
        $assignPermissions = $request->input('rolePermission');
        $role = Role::find($request->roleid);
        $role->perms()->sync([]);
        if ($assignPermissions) {
            foreach ($assignPermissions as $assignPermission) {
                $role->attachPermission($assignPermission);
            }
        }
        app('messageAlert')->store('保存成功', MessageAlert::SUCCESS, '角色授权信息保存成功。');
        UserLog::info('Assign permissions [' . json_encode($assignPermissions) . '] to role [' . $role->name . ']');
        return redirect()->route('role.permission', ['role' => $role->id]);
    }
}
