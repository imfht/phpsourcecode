<?php
namespace app\admin\controller;

use think\captcha\Captcha;
use think\Controller;
use app\common\service\LoginService;
use utils\JsonUtils;
use utils\JWTUtils;

class Login extends Controller
{
    protected $loginService;
    protected $jsonUtils;

    public function initialize()
    {
        $this->loginService = new LoginService();
        $this->jsonUtils = new JsonUtils();
    }

    public function index()
    {
        if(session('auth')){
            $this->redirect('admin/index/index');
        }
        return $this->fetch('/login');
    }

    public function login()
    {
        $username = input("post.username");
        $password = JWTUtils::encode(input("post.password"));
        $verify = input("post.verify");

        $captcha = new Captcha();
        if(!$captcha->check($verify) && config('verify') == true) {
            return $this->jsonUtils->msgError($data = null, "验证码不正确");
        }
        $auth = $this->loginService->auth($username, $password);
        if(!empty($auth)){
            return $this->jsonUtils->success();
        }
        return $this->jsonUtils->error();
    }

    public function logout()
    {
        $this->loginService->sessionOut();
        $this->redirect('login/index');
    }

    public function verify()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    45,
            // 验证码位数
            'length'      =>    4,
            // 开启验证码背景图片功能 随机使用扩展包内`think-captcha/assets/bgs`目录下面的图片
           'useImgBg'     =>    true,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
}
