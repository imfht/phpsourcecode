<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminsStoreRequest;
use Illuminate\Http\Request;
use Services\AdminsService;
use Services\RolesService;

class AdminsController extends Controller
{
    protected $admin;

    public function __construct(AdminsService $admin)
    {
        parent::__construct();
        $this->admin = $admin;
    }

    public function index()
    {
        return view('admin.admins.index')->withAdmins($this->admin->paginate(10));
    }

    public function create(RolesService $role)
    {
        return view('admin.admins.create')->withRoles($role->all());
    }

    public function store(AdminsStoreRequest $request)
    {

        $data = $request->all();
        $status = 0;
        if ($this->admin->store($data)) {
            $status = 1;
        }
        return response()->json(['status'=>$status]);
    }

    public function destroy($id)
    {
        $message = $this->admin->delete($id) ? '删除成功' : '删除失败';
        showMessage($message);
        return back();
    }

    public function active(Request $request, $id)
    {
        $active = '';
        if ($request->input('active') == 'disable') {
            $active = 0;
        } elseif ($request->input('active') == 'enable') {
            $active = 1;
        }
        $result = $this->admin->update(['active'=>$active], $id);
        $status = 0;
        if ($result) {
            $status = 1;
        }
        return response()->json(['status'=>$status]);
    }
}
