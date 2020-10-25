<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/8/17
// +----------------------------------------------------------------------
// | Title:  Login.php
// +----------------------------------------------------------------------
 namespace app\index\controller;

 use app\index\model\Users As UserModel;
 use think\Config;
 use think\Controller;
 use think\Db;
 use think\Loader;
 use think\Request;
 use think\Session;
 use think\Url;

 class Login extends Controller
 {
     public function index() {
         header("Content-Type: text/html; charset=utf-8");
         if (Session::has("user_name") && Session::has("user_id")) {
             $this->redirect("index/index");
         } else {
             return $this->fetch();
         }
     }

     /* 提交登录信息 */
     public function login() {
         // 设定数据返回格式
         Config::set("default_return_type","json");
         $request = Request::instance();
         if ($request->isPost()) {
             // 接受指定信息
             $username = $request->param("username");
             $password = $request->param("password");
             //$verify   = $request->param("verify");
             $token    = $request->param("token");

             // 验证数组
             $data = [
                 'username'  => $username,
                 'password'  => $password ,
                 //'verify'    => $verify,
                 '__token__' => $token
             ];

             $validate = Loader::validate("Users");
             if (!$validate->scene("login")->check($data)) {
                 // dump($validate->getError()); // 输出 验证的 错误信息
                 $this->error($validate->getError());
             }
             // 查询是否有此账户
             //$user = new UserModel();
             $userDb = Db::name('users')->where('user_name', $username)->find();
             //p($userDb);
             //exit();
             if ($userDb) {
                 // 验证 status = 1 才能登陆 $userDb->getData("status")
                 if ($userDb['status'] != '1') {
                     return $this->error("账户存在异常，请联系管理员。");
                 }
                 // 验证密码是否正确
                 $passwordBy = validate_password($password, $userDb["user_password"]);
                 if ($passwordBy === true) {
                     // 密码正确
                     UserModel::where("user_name", $username)->update(["user_count"=> ['exp', 'user_count+1']]);
                     // 将用户信息写入 Session
                     Session::set('user_id', $userDb['id']); // 用户ID
                     Session::set('user_name', $userDb['user_name']); // 登录名
                     Session::set('user_nick', $userDb['user_nick']); // 用户名
                     Session::set('user_auth', $userDb['user_auth']); // 1为超级管理员
                     return $this->success('登录成功', Url::build("index/index"));
                 } else {
                     return $this->error("账户或密码错误");
                 }
             } else {
                 return $this->error("账户或密码错误");
             }
         }
     }

     /* 退出登录 */
     public function logout() {
         Session::clear(); // 清除session值
         return $this->success("成功退出", Url::build("index/index"));
     }

     /* 验证码 */
     public function verify() {
         $config = array(
             'fontSize' => 20, // 验证码字体大小
             'length'   => 4, // 验证码位数
             'useNoise' => false, // 关闭验证码杂点 true-开启，false-关闭
             'imageH'   => 40, // 验证码图片高度
             'imageW'   => 150, // 验证码图片宽度
         );
         //ob_clean(); // 清除缓存
         $Verify = new \org\Verify($config);
         $Verify->expire = 300;
         $Verify->entry();
         exit();
     }
 }