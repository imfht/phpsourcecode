<?php

class UserController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 用户中心
     * @param int $id
     * @return string
     */
    public function index($id) {
        $user = User::find($id);

        //设置标题、描述等SEO信息
        View::share('title', $user['username']);
        View::share('description', '用户' . $user['username'] . '的个人中心');

        //获取用户发布的所有文章
        $nodes = Node::where('uid', '=', $user['id'])->orderBy('id', 'desc')->paginate(15);
        $paginate = $nodes->links();

        $data = array(
            'user' => $user,
            'nodes' => $nodes,
            'paginate' => $paginate,
            'user_content_top' => '',
            'user_content_bottom' => ''
        );
        /**
         * hook_user_page_load
         */
        $data = Hook_user::user_page_load($data);

        $template = Theme::user($user);
        return View::make($template, $data);
    }

    /**
     * 登录
     */
    public function login() {
        //设置标题、描述等SEO信息
        View::share('title', '登陆');
        View::share('description', '在' . $this->siteName . '进行登陆');

        //判断是否开启登录,1为允许登录，0为禁止登录
        $user_is_allow_login = Setting::find('user_is_allow_login');
        if (!$user_is_allow_login->value) {
            $template = Theme::message();
            return View::make($template, array('message' => '该站点已关闭登录功能，请联系管理员！', 'type' => 'info', 'url' => '/'));
        }
        if (Auth::check()) {
            $template = Theme::message();
            return View::make($template, array('message' => '你已经登录，即将为你跳转到首页', 'type' => 'info', 'url' => '/'));
        }

        if (Request::method() == 'POST') {
            $input = Input::all();
            $username_or_email = $input['username_or_email'];
            $password = $input['password'];
            if (isset($input['remember'])) {
                $remember = true;
            } else {
                $remember = FALSE;
            }

            /**
             * hook_user_login_before
             */
            list($input, $username_or_email, $password, $remember) = Hook_user::user_login_before($input, $username_or_email, $password, $remember);

            //密码验证
            if (Auth::attempt(array('email' => $username_or_email, 'password' => $password), $remember) || Auth::attempt(array('username' => $username_or_email, 'password' => $password), $remember)) {
                /**
                 * hook_user_login_after
                 */
                $user = Auth::user();
                Hook_user::user_login_after($user);

                //return Redirect::intended('index');
                $back_url = Input::get('back_url');
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'login', 'message' => '登陆'));
                return Redirect::intended('/');
            } else {
                return Redirect::to(Request::url())->with('message', '用户名 / 邮箱或者密码错误');
            }
        }

        $message = Session::get('message');
        $template = Theme::template('user.login');
        return View::make($template, array('message' => $message));
    }

    /**
     * 注册
     */
    public function register() {
        //设置标题、描述等SEO信息
        View::share('title', '注册');
        View::share('description', '在' . $this->siteName . '上注册一个账户,成为其会员');

        //判断是否开启注册,1为允许注册，0为禁止注册
        $user_is_allow_register = Setting::find('user_is_allow_register');
        if (!$user_is_allow_register->value) {
            $template = Theme::message();
            return View::make($template, array('message' => '该站点已关闭注册功能，请联系管理员！', 'type' => 'info', 'url' => '/'));
        }
        if (Auth::check()) {
            $template = Theme::message();
            return View::make($template, array('message' => '你已经登录，即将为你跳转到首页', 'type' => 'info', 'url' => '/'));
        }
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'username' => 'required|min:4|max:10|unique:users,username',
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
                return Redirect::to(Request::url())->withErrors($validator);
            }

            /**
             * hook_user_register_before
             */
            $input = Hook_user::user_register_before($input);

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
                //添加角色
                $data_role = array('uid' => $uid, 'rid' => 2);
                UserRoles::create($data_role);

                /**
                 * hook_user_register_after
                 */
                Hook_user::user_register_after();

                Logs::create(array('uid' => $uid, 'type' => 'add', 'message' => '用户注册,ID为' . $uid . ',用户名：' . $input['username']));
            } catch (Exception $e) {
                //事务回滚
                DB::rollback();
                return View::make('Theme::templates.message', array('message' => '注册失败，请联系管理员！', 'type' => 'error', 'url' => Request::url()));
            }
            //提交事务
            DB::commit();

            //发送邮件
            $content = array(
                'siteName' => $this->siteName,
                'siteUrl' => $this->siteUrl,
                'username' => $input['username'],
                'password' => $input['password'],
                'email' => $input['email']
            );
            $data = array(
                'siteName' => $this->siteName,
                'email' => $input['email']
            );
            //注册成功发送邮件
            try {
                Mail::queue('email', $content, function($message) use ($data) {
                    $message->to($data['email'], $data['siteName'])->subject('恭喜你，注册成功 --' . $data['siteName']);
                });
            } catch (Exception $e) {
                //die('over');
            }

            //展示消息
            $template = Theme::message();
            return View::make($template, array('message' => '恭喜你，注册成功', 'type' => 'success', 'url' => 'login'));
        }
        $message = Session::get('message');
        $filename = Theme::template('user.register');
        return View::make($filename)->with('message', $message);
    }

    /**
     * 
     * 退出登录
     */
    public function logout() {
        Auth::logout();
        return Redirect::to('/');
    }

    /**
     * 编辑用户信息
     */
    public function edit($id) {
        //设置标题、描述等SEO信息
        View::share('title', '编辑用户个人信息');

        if ($id != Auth::user()->id) {
            $template = Theme::message();
            return View::make($template, array('message' => '对不起，你没有改权限！', 'type' => 'error', 'url' => '/user/' . $id));
        }
        $user = User::find($id);

        if (Request::method() == 'POST') {

            //判断是否上传了图片
            if ($_FILES) {
                $input = Input::all();
                $rules = array(
                    'picture' => 'mimes:jpeg,png,gif,pjpeg'
                );
                $messages = array(
                    'picture.mimes' => '只允许上传大小不超过2M的JPG,PNG,GIF格式的图片！'
                );
                //进行字段验证
                $validator = Validator::make($input, $rules, $messages);
                if ($validator->fails()) {
                    return Redirect::to(Request::url())->withErrors($validator);
                }
                //Image::upload($file,$path,$weight,$height);
                $template = Image::upload($_FILES['picture'], 'upload/author/', $id, 100, 100);
                if (!$template) {
                    return Redirect::to(Request::url());
                }
                $user->picture = $template;
                $result = $user->save();
                if (!$result) {
                    $template = Theme::message();
                    return View::make($template, array('message' => '修改头像失败', 'type' => 'error', 'url' => '/user/' . $id));
                }
            }
            $input = Input::all();
            if ($input['password']) {
                $rules = array(
                    'password' => 'required|min:6|max:20|confirmed|alpha_dash',
                    'password_confirmation' => 'required'
                );
                $messages = array(
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
                    return Redirect::to(Request::url())->withErrors($validator);
                }

                //修改密码
                $user->password = Hash::make($input['password']);
                $result = $user->save();
                if (!$result) {
                    $template = Theme::message();
                    return View::make($template, array('message' => '修改密码失败', 'type' => 'error', 'url' => '/user/' . $id));
                }
            }
            $template = Theme::message();
            return View::make($template, array('message' => '修改成功', 'type' => 'success', 'url' => '/user/' . $id));
        }
        $template = Theme::template('user.edit');
        return View::make($template);
    }

}
