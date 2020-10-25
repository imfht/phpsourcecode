<?php
namespace app\index\controller;
\think\Loader::import('controller/Jump', TRAIT_PATH, EXT);

class Api
{
    use \traits\controller\Jump;

    // 验证码
    public function captcha()
    {
        $key = input('key');
        $config = \ebcms\Config::get('home.captcha');
        $captcha = new \think\captcha\Captcha($config);
        return $captcha->entry($key);
    }

    public function gourl(){
        $url = input('url', request() -> root());
        $response = new \think\response\Redirect($url);
        $response->code(302);
        throw new \think\exception\HttpResponseException($response);
    }

    // 重置后台账户
    public function readmin()
    {
        $password_file = CONF_PATH.'pwd.php';

        if (!is_file($password_file)) {
            return '重置密码文件不存在！';
        }
        if (!is_writable($password_file)) {
            return '重置密码文件不可写！请处理！';
        }
        if (!$password = trim(file_get_contents($password_file))) {
            return '重置密码文件已经过期！';
        }

        file_put_contents($password_file, '');

        if (strlen($password)<6 || strlen($password)>12) {
            return '新邮箱或新密码配置错误，请处理！';
        }
        $email = \think\Config::get('super_admin');
        
        $where = [
            'email' =>  $email
        ];
        if (!$manager = \app\ebcms\model\Manager::where($where) -> find()) {
            return '账户错误，请联系官方人员寻求帮助！';
        }

        $manager -> password = $password;
        if (false === $manager -> save()) {
            return '密码重置失败！请联系官方人员寻求帮助！';
        }

        return '密码重置成功！您的登陆账户是：' . $email;
    }

    public function store($str){
        \think\Config::set('app_trace',false);
        $uid = \ebcms\Config::get('system.store_uid');
        return md5($str.'_'.$uid);
    }

    public function token(){
        \think\Config::set('app_trace',false);
        if ($authstr = \think\Cache::get('authstr')) {
            \think\Cache::rm('authstr');
            return $authstr;
        }
    }

}