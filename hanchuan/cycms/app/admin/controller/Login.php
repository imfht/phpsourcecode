<?php
namespace app\admin\controller;

use think\facade\Cookie;
use app\admin\model\User;
use think\captcha\facade\Captcha;

class Login extends Common
{
    public function index()
    {
        $auth = Cookie::get('auth');
        if ($auth) {
            list($identifier, $token) = str_split($auth,32);
            if (ctype_alnum($identifier) && ctype_alnum($token)) {
                $user = User::where(['identifier'=>$identifier,'token'=>$token,'status'=>1])->find();
                if ($user) {
                    if ($user->group->status == 0) {
                        return $this -> error('所属用户组已被禁用！', url('admin/login/index'));
                    }
                    if ($token == $user->token && $user->identifier == password($user->uid . md5($user->username . $user->salt))) {
                        return $this -> success('您已登录，正在跳转！', url('admin/index/index'));
                    }
                }
            }
        }
        return $this->view->fetch();
    }

    public function login()
    {
        $data = $this->request->post();
        
        #数据验证
        $validate = new \app\admin\validate\Login;
        $result = $validate->check($data);
        if ($result !== true) {
            return $this -> error($validate->getError(), url('login/index'));
        }
        #验证码验证
        if(!Captcha::check($data['verify'])){
            return $this->error('验证码错误');
        };
        #密码比对
        $user = User::where(['status'=>1,'username'=>$data['username'],'password'=>password($data['password'])])->find();
        if ($user) {
            if ($user->group->status == 0) {
                return $this -> error('所属用户组已被禁用！', url('admin/login/index'));
            }

            $token = password(uniqid(rand(), true));
            $salt = random(10);
            $identifier = password($user->uid . md5($user->username . $salt));
            $auth = $identifier.$token;

            $user->identifier = $identifier;
            $user->token = $token;
            $user->salt = $salt;
            $user->save();

            if (isset($data['remember'])) {
                Cookie::set('auth', $auth, 3600*24*365);
            } else {
                Cookie::set('auth', $auth);
            }
            addlog('登录成功。', $user->username);
            return $this -> success('恭喜，登录成功！', url('admin/index/index'));
        } else {
            addlog('用户或密码错误。', $data['username']);
            return $this -> error('用户名或密码错误，请稍后再试！', url('admin/login/index'));
        }
        
    }
    
    public function verify($id = '')
    {
        return Captcha::create($id);
    }
}
