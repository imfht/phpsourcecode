<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PermissionsStoreRequest;
use Illuminate\Http\Request;
use Services\PermissionsService;

class PermissionsController extends Controller
{
    protected $permission;

    public function __construct(PermissionsService $permission)
    {
        $this->permission = $permission;
    }

    public function index()
    {
        return view('admin.permissions.index')->with('permissions', $this->permission->all());
    }

    public function create()
    {
        return view('admin.permissions.create')->with('permissions', $this->permission->all());
    }

    public function store(PermissionsStoreRequest $request)
    {
        $message = '添加失败';
        $redirectTo = '';
        if ($this->permission->store($request->all())) {
            $message = '添加成功';
            $redirectTo = route('permissions.index');
        }
        showMessage($message, $redirectTo);
        return redirect()->back();
    }

    public function edit($id)
    {
        return view('admin.permissions.edit')->with('permission', $this->permission->findById($id))->withPermissions($this->permission->all());
    }

    public function update(PermissionsStoreRequest $request, $id)
    {
        $message = '修改失败';
        if ($this->permission->update($request->all(), $id)) {
            $message = '修改成功';
        }
        showMessage($message);
        return back();

    }

    public function destroy($id)
    {
        $this->permission->destroy($id);
        showMessage('删除成功');
        return redirect()->back();
    }
}
