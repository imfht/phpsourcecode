<?php

class BackAdminController extends BackBaseController {

    /**
     * 后台首页
     */
    public function index() {
        //1、获取最近注册的用户数量信息
        $register_count = Report::get_register_count();
        //2、获取最近发布的文章数量信息
        $node_count = Report::get_node_count();
        return View::make('BackTheme::templates.index', array('register_count' => $register_count, 'node_count' => $node_count));
    }

    /**
     * 后台登录
     */
    public function login() {
        //检查是否已经登录
        if (Auth::check()) {
            return View::make('BackTheme::templates.message', array('message' => '你已经登录，即将为你跳转到首页', 'type' => 'info', 'url' => '/admin'));
        }
        if (Request::method() == 'POST') {
            //1、验证码验证
            $rules = array(
                'user-captcha' => 'required|captcha'
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->with('message', '验证码错误')->withInput();
            }
            //2、账户密码验证
            $input = Input::all();
            $username_or_email = $input['username_or_email'];
            $password = $input['password'];
            if (isset($input['remember'])) {
                $remember = true;
            } else {
                $remember = FALSE;
            }
            //密码验证
            if (Auth::attempt(array('email' => $username_or_email, 'password' => $password), $remember) || Auth::attempt(array('username' => $username_or_email, 'password' => $password), $remember)) {
                //判断是否是管理员登陆,如果不是管理员则退出登陆
                $rid = Auth::user()->rid;
                if ($rid != 1) {
                    //再判断是否具有后台登陆权限
                    $result = RolesPermission::where('rid', $rid)->where('name', 'admin_login')->get();
                    if (!$result) {
                        Auth::logout();
                        return Redirect::to('/');
                    }
                }
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'login', 'message' => '登陆'));
                return Redirect::intended('admin');
            } else {
                Input::flash();
                return Redirect::back()->with('message', '用户名 / 邮箱或者密码错误')->withInput();
            }
        }
        $message = Session::get('message');
        return View::make('BackTheme::templates.login')->with('message', $message);
    }

    /**
     * 403
     */
    public function message_403() {
        //设置标题、描述等SEO信息
        View::share('title', '403');
        View::share('description', '没有权限访问');

        return View::make('BackTheme::templates.403');
    }

    /**
     * 404
     */
    public function message_404() {
        //设置标题、描述等SEO信息
        View::share('title', '404');
        View::share('description', '没有找到该页面');

        return View::make('BackTheme::templates.404');
    }

}
