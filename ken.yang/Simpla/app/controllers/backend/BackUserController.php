<?php

/*
 * 用户管理
 */

class BackUserController extends BackBaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * --------------------------------------------------------------------
     * 用户列表
     * @return type
     */
    public function index() {
        //初始化值
        $choose = array('uid' => null, 'username' => null, 'email' => null);
        if (Request::method() == 'POST') {
            $input = Input::all();
            $uid = $input['uid'];
            $username = $input['username'];
            $email = $input['email'];

            //进行筛选
            //逻辑为，只有一个输入是有效的
            $choose = array('uid' => $uid, 'username' => $username, 'email' => $email);
            if ($uid) {
                $users = User::where('id', '=', $uid)->orderBy('id', 'desc')->paginate(20);
            } elseif ($username) {
                $users = User::where('username', '=', $username)->orderBy('id', 'desc')->paginate(20);
            } elseif ($email) {
                $users = User::where('email', '=', $email)->orderBy('id', 'desc')->paginate(20);
            } else {
                $users = User::orderBy('id', 'desc')->paginate(20);
            }
        } else {
            $users = User::orderBy('id', 'desc')->paginate(20);
        }

        return View::make('BackTheme::templates.user/index', array('users' => $users, 'choose' => $choose));
    }

    /**
     * -------------------------------------------------------------------
     * 添加用户
     * @return type
     */
    public function add() {
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'username' => 'required|min:6|max:32|unique:users,username',
                'email' => 'required|unique:users,email|max:256',
                'password' => 'required|min:6|max:20|confirmed|alpha_dash',
                'password_confirmation' => 'required'
            );
            $messages = array(
                'username.required' => '必须填写用户名',
                'username.min' => '用户名最少为:min个字符',
                'username.max' => '用户名最少为:max个字符',
                'username.unique' => '用户名已经存在',
                'emial.required' => '必须填写邮箱',
                'emial.unique' => '邮箱已经注册',
                'emial.max' => '邮箱最大为:max个字符',
                'password.required' => '必须填写密码',
                'password.min' => '密码最少为:min个字符',
                'password.max' => '密码最少为:max个字符',
                'password.confirmed' => '两次输入的密码不一样',
                'password.alpha_dash' => '密码仅允许字母、数字、破折号（-）以及底线（_）',
                'password_confirmation.required' => '必须填写两次密码'
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //创建用户
            $data = array();
            $data['username'] = $input['username'];
            $data['password'] = Hash::make($input['password']);
            $data['email'] = $input['email'];
            $data['created_at'] = NOW_FORMAT_TIME;
            $data['updated_at'] = NOW_FORMAT_TIME;
            try {
                //开始事务
                DB::beginTransaction();
                $uid = DB::table('users')->insertGetId($data);
                if ($uid) {
                    //添加角色
                    $data_role = array('uid' => $uid, 'rid' => $input['roles']);
                    UserRoles::create($data_role);
                }
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加新用户' . $uid));
            } catch (Exception $e) {
                //事务回滚
                DB::rollback();
                return View::make('BackTheme::templates.message', array('message' => '添加新用户失败！', 'type' => 'error', 'url' => Request::url()));
            }
            //提交事务
            DB::commit();
            return View::make('BackTheme::templates.message', array('message' => '添加用户成功！', 'type' => 'success', 'url' => '/admin/user'));
        }
        //获取所有角色
        $roles = Roles::where('id', '!=', 1)->get()->toarray();
        return View::make('BackTheme::templates.user/add', array('roles' => $roles));
    }

    /**
     * -------------------------------------------------------------------
     * 编辑用户
     * @param int $uid
     * @return type
     */
    public function edit($uid) {
        $user = User::find($uid);
        $user_role = $user->roles['rid'];

        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'username' => 'required|min:6|max:32',
                'email' => 'required|max:256',
                'password' => 'confirmed|max:20',
            );
            $messages = array(
                'username.required' => '必须填写用户名',
                'username.min' => '用户名最小为:min个字符',
                'username.max' => '用户名最大为:max个字符',
                'email.required' => '必须填写邮箱',
                'email.max' => '邮箱最大为:max个字符',
                'password.max' => '密码最大为:max个字符',
                'password.confirmed' => '两次输入的密码不一样',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //保存用户
            $user->username = $input['username'];
            $user->email = $input['email'];
            $user->status = $input['status'];
            $user->updated_at = NOW_FORMAT_TIME;
            if ($input['password']) {
                $user->password = Hash::make($input['password']);
            }

            try {
                //开始事务
                DB::beginTransaction();
                $user->save();
                //保存角色
                $users_roles = UserRoles::find($uid);
                $users_roles->rid = $input['roles'];
                $users_roles->save();
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑用户,ID为' . $uid));
            } catch (Exception $e) {
                //事务回滚
                DB::rollback();
                return View::make('BackTheme::templates.message', array('message' => '编辑用户失败！', 'type' => 'error', 'url' => Request::url()));
            }
            //提交事务
            DB::commit();
            return View::make('BackTheme::templates.message', array('message' => '编辑用户成功！', 'type' => 'success', 'url' => '/admin/user'));
        }
        //获取所有角色
        $roles = Roles::where('id', '!=', 1)->get()->toarray();
        return View::make('BackTheme::templates.user/edit', array('user' => $user, 'user_role' => $user_role, 'roles' => $roles));
    }

    /**
     * ------------------------------------------------------------------
     * 删除用户
     * @param int $uid
     * @return type
     */
    public function delete($uid) {
        if ($uid == 1) {
            $result = array('status' => 'error', 'message' => '管理员账户不允许删除！');
            return json_encode($result);
        } else {
            try {
                //开始事务
                DB::beginTransaction();
                //删除用户
                $user = User::find($uid);
                $user->delete();
                //删除角色
                UserRoles::where('uid', '=', $uid)->delete();

                Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除用户,ID为' . $uid));
            } catch (Exception $e) {
                //事务回滚
                DB::rollback();
                $result = array('status' => 'error', 'message' => '删除失败');
                return json_encode($result);
            }
            //提交事务
            DB::commit();

            $result = array('status' => 'success', 'message' => '删除成功');
            return json_encode($result);
        }
    }

    /**
     * ------------------------------------------------------------------
     * 用户个人信息
     * @param int $uid
     * @return type
     */
    public function info() {
        $id = Auth::user()->id;
        $user = User::find($id);

        //获取用户发布的所有文章
        $nodes = Node::where('uid', '=', $user['id'])->orderBy('id', 'desc')->paginate(15);

        return View::make('BackTheme::templates.user/info', array('user' => $user, 'nodes' => $nodes));
    }

    /**
     * ------------------------------------------------------------------
     * IP锁定功能
     * @param int $uid
     * @return type
     */
    public function ip_lock() {
        return View::make('BackTheme::templates.user/ip_lock');
    }

    /**
     * ----------------------------------------------------------------------
     * 用户角色
     * 
     */

    /**
     * 角色列表
     */
    public function roles() {
        $roles = Roles::all();
        return View::make('BackTheme::templates.user.roles.index')->with('roles', $roles);
    }

    /**
     * 
     * 添加角色
     */
    public function roles_add() {
        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
            );
            $messages = array(
                'title.required' => '必须填写角色名',
                'title.max' => '角色名最多只能输入32个字符',
                'description.max' => '描述最多只能输入256个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //创建用户
            $data = array();
            $data['title'] = $input['title'];
            $data['description'] = $input['description'];
            $result = Roles::create($data);
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加角色,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加角色成功！', 'type' => 'success', 'url' => '/admin/user/roles'));
            }
        }
        return View::make('BackTheme::templates.user.roles.add');
    }

    /**
     * 编辑角色
     * @param type $rid   角色ID
     */
    public function roles_edit($rid) {
        $role = Roles::find($rid);
        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入32个字符',
                'description.max' => '描述最多只能输入256个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('role', $role)->withInput();
            }
            //保存用户
            $role['title'] = $input['title'];
            $role['description'] = $input['description'];
            $result = $role->save();
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑角色,ID为' . $rid));
                return View::make('BackTheme::templates.message', array('message' => '更新角色成功！', 'type' => 'success', 'url' => '/admin/user/roles'));
            }
        }
        return View::make('BackTheme::templates.user.roles.edit')->with('role', $role);
    }

    /**
     * 删除角色
     */
    public function roles_delete($rid) {
        if ($rid == 1 || $rid == 2 || $rid == 3) {
            return View::make('BackTheme::templates.message', array('message' => '删除失败，系统默认角色不允许删除！', 'type' => 'warning', 'url' => '/admin/user/roles'));
        }
        $role = Roles::find($rid);
        if (!$role) {
            return View::make('BackTheme::templates.message', array('message' => '删除失败，不存在该角色！', 'type' => 'error', 'url' => '/admin/user/roles'));
        }

        $role->delete();
        Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除角色,ID为' . $rid));
        return View::make('BackTheme::templates.message', array('message' => '删除角色成功！', 'type' => 'success', 'url' => '/admin/user/roles'));
    }

    /**
     * ----------------------------------------------------------------------
     * 用户角色权限管理
     * 
     */
    public function roles_permission($rid) {
        //管理员账户拥有所有权限，不能编辑
        if ($rid == '3') {
            return View::make('BackTheme::templates.message', array('message' => '超级管理员账户默认拥有所有权限！', 'type' => 'wrong', 'url' => '/admin/user/roles'));
        }

        //1、从数据库获取路由权限组
        $roles_access = Hook_access::access();
        //$roles_access = RolesPermission::getRoutes();
        //2、权限排除
        if ($roles_access) {
            foreach ($roles_access as $key => $access) {
                //当为匿名用户的时候，排除admin选项
                if ($rid == '1' && stristr($key, 'admin') != '0') {
                    unset($roles_access[$key]);
                    continue;
                }
                foreach ($access['list'] as $row_key => $row) {
                    //当有特殊路由时需要排除
                    if (in_array($row['as'], array('403', '404', 'login', 'logout', 'register', 'password_remind', 'password_getremind', 'password_reset', 'password_getreset'))) {
                        unset($roles_access[$key]['list'][$row_key]);
                        continue;
                    }
                    if (empty($row['as'])) {
                        unset($roles_access[$key]['list'][$row_key]);
                        continue;
                    } else {
                        //2、获取当前角色的所具有的权限
                        $is_role = RolesPermission::where('rid', '=', $rid)->where('name', '=', $row['as'])->first();
                        if ($is_role) {
                            $roles_access[$key]['list'][$row_key]['permisssion'] = '1';
                        } else {
                            $roles_access[$key]['list'][$row_key]['permisssion'] = '';
                        }
                    }
                }
            }
        }

        //获取当前编辑的角色信息
        $role = Roles::find($rid);

        if (Request::method() == 'POST') {
            $input = Input::all();
            //先清除所有权限
            RolesPermission::where('rid', '=', $rid)->delete();
            foreach ($input['permission'] as $row) {
                $data = array();
                $data['rid'] = $rid;
                $data['name'] = $row;
                RolesPermission::firstOrCreate($data);
            }
            //保存修改
            return View::make('BackTheme::templates.message', array('message' => '修改权限成功！', 'type' => 'success', 'url' => Request::url()));
        }
        return View::make('BackTheme::templates.user.permission.index', array('roles_access' => $roles_access, 'role' => $role));
    }

    /**
     * 刷新路由权限组,然后写入数据库
     */
    public function roles_access_refresh() {
        $setting_roles_access = Setting::find('roles_access');
        //获取所有系统和激活模块的权限
        $access_routes = Hook_access::access();
        //进行序列化，写入数据库
        $setting_roles_access->value = serialize($access_routes);
        $setting_roles_access->save();
        return View::make('BackTheme::templates.message', array('message' => '刷新角色权限成功！', 'type' => 'success', 'url' => '/admin/user/roles'));
    }

}
