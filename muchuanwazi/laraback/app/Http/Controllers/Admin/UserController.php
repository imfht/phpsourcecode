<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserChangeProfileRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserRoleRequest;
use App\Models\User;
use Auth;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MessageAlert;
use App\Models\Role;
use UserLog;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user_manage')->except(['userRole', 'userRoleStore','changeMy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(15);
        $data = array('page_title' => '用户管理', 'page_description' => '新建、修改、管理系统用户数据',
            'users' => $users);
        return view('admin.user.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array('page_title' => '新建用户', 'page_description' => '新建一个系统用户', 'function' => __FUNCTION__);
        return view('admin.user.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {

        $request->merge(['password' => Hash::make($request->password)]);
        User::create($request->all());
        app('messageAlert')->store('保存成功', MessageAlert::SUCCESS, '新用户保存成功。');
        UserLog::info('store a user.[' . $request->name . ']');
        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $data = array('page_title' => '用户信息', 'page_description' => '显示系统用户信息',
            'user' => $user);
        return view('admin.user.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $data = array('page_title' => '更新用户', 'page_description' => '更新系统用户数据', 'function' => __FUNCTION__,
            'user' => $user);
        return view('admin.user.create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        if ($request->has('password') && $request->password != '') {
            $request->merge(['password' => Hash::make($request->password)]);
        } else {
            $request->offsetUnset('password');
        }
        $user->update($request->all());
        app('messageAlert')->store('更新成功', MessageAlert::SUCCESS, '用户信息更新成功。');
        UserLog::info('update a user.[' . $user->name . ']');
        return redirect()->route('user.show', ['id' => $user->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 1 ? 0 : 1;
        $user->update();
        app('messageAlert')->store('更新成功', MessageAlert::SUCCESS, '用户信息更新成功。');
        UserLog::info('destroy a user.[' . $user->name . ']');
        return redirect()->route('user.index');
    }


    public function userRole(User $user)
    {
        $roles = Role::all();
        $userRole = \DB::table('role_user')->where('user_id', $user->id)->pluck('role_id')->toArray();
        $items = [];
        $userRoleCount = 0;
        foreach ($roles as $role) {
            $checked = false;
            if (in_array($role->id, $userRole)) {
                $checked = true;
                $userRoleCount++;
            }
            $items[] = ['label' => $role->display_name, 'value' => $role->id, 'name' => 'userRole[]', 'isCheck' => $checked];
        }


        $data = array('page_title' => '用户赋权', 'page_description' => '给系统用户添加系统角色信息。',
            'user' => $user, 'userRoleCount' => $userRoleCount, 'userRole' => $items);
        return view('admin.user.user_role', $data);
    }

    public function userRoleStore(UserRoleRequest $request)
    {
        $assignRoles = $request->input('userRole');
        $user = User::find($request->userid);
        $user->roles()->sync([]);
        if ($assignRoles) {
            foreach ($assignRoles as $assignRole) {
                $user->attachRole($assignRole);
            }
        }
        app('messageAlert')->store('保存成功', MessageAlert::SUCCESS, '用户角色信息保存成功。');
        UserLog::info('Assign roles [' . json_encode($assignRoles) . '] to user.[' . $user->name . ']');
        return redirect()->route('user.role', ['user' => $user->id]);
    }

    public function avatar(User $user)
    {
        $data = array('page_title' => '添加用户头像', 'page_description' => '给系统用户添加头像。',
            'user' => $user);
        return view('admin.user.avatar', $data);
    }

    public function avatarUpload(Request $request,User $user)
    {
        $this->wrongTokenAjax();
        $file = $request->file('image');
        $input = array('image' => $file);
        $rules = array(
            'image' => 'image'
        );
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return \Response::json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ]);

        }

        $destinationPath = 'uploads/avatar/'.date('Ymd').'/';
        $filename = 'user'.$user->id.'_'.md5(time()).'.'.$file->extension();

        $file->move($destinationPath, $filename);

        $user->avatar=$destinationPath . $filename;
        $user->update();
        return \Response::json(
            [
                'success' => true,
                'avatar' => asset($destinationPath . $filename),
            ]
        );
    }

    private function wrongTokenAjax()
    {
        if ( \Session::token() !== \Request::get('_token') ) {
            $response = [
                'status' => false,
                'errors' => 'Wrong Token',
            ];

            return \Response::json($response);
        }

    }

    /*change my profile*/
    public function changeMy()
    {
        $user=Auth::user();
        $data = array('page_title' => '更新个人信息', 'page_description' => '修改个人信息', 'function' => __FUNCTION__,'user'=>$user);
        return view('admin.user.change_my', $data);
    }

    public function storeMy(UserChangeProfileRequest $request)
    {
        $user=Auth::user();
        if($request->has('password'))
        {
            $user->password=Hash::make($request->password);
        }
        $user->email=$request->email;
        $user->update();
        app('messageAlert')->store('保存成功', MessageAlert::SUCCESS, '个人信息修改成功。');
        UserLog::info('User [' . $user->name . '] changed his profile.');
        return redirect()->route('user.change_my');
    }

}
