<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PermissionRequest;
use App\Logic\PermissionLogic;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use MessageAlert;
use UserLog;

class PermissionController extends Controller
{

    private $permissionLogic;

    public function __construct(PermissionLogic $permissionLogic)
    {
        $this->permissionLogic=$permissionLogic;
        $this->middleware('permission:permission_manage');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions=$this->permissionLogic->getPermissionsWithPage();
        $data=array('page_title'=>'模块管理','page_description'=>'新建、修改、管理系统模块数据',
            'permissions'=>$permissions);
        return view('admin.permission.permissions',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data=array('page_title'=>'模块管理','page_description'=>'新建、修改、管理系统模块数据','function'=>__FUNCTION__);
        return view('admin.permission.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        Permission::create($request->all());
        app('messageAlert')->store('保存成功',MessageAlert::SUCCESS,'新模块信息保存成功。');
        UserLog::info('Store a permission ['.$request->name.']');
        return redirect()->route('permission.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        $data=array('page_title'=>'模块信息','page_description'=>'显示系统模块信息',
            'permission'=>$permission);
        return view('admin.permission.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        $data=array('page_title'=>'更新模块','page_description'=>'更新系统模块数据','function'=>__FUNCTION__,
                    'permission'=>$permission);
        return view('admin.permission.create',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, $id)
    {
        $permission=Permission::find($id);
        $permission->update($request->all());
        app('messageAlert')->store('更新成功',MessageAlert::SUCCESS,'模块信息更新成功。');
        UserLog::info('Update a permission ['.$permission->name.']');
        return redirect()->route('permission.show',['id' => $permission->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
