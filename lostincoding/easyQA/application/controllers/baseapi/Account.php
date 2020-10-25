<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 用户账号接口控制器
 */
class Account extends BaseAPI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('geetest/geetest');
        $this->load->model('skinsetting_model');
        $this->load->model('user_model');
        $this->load->model('githubuser_model');
        $this->load->model('weixinuser_model');
        $this->load->model('qcuser_model');
        $this->load->model('weibouser_model');
        $this->load->model('oschinauser_model');
    }

    /**
     * 登录
     */
    public function signin()
    {
        $user['email'] = $this->input->post('email');
        $user['pwd'] = $this->input->post('pwd');

        //表单验证
        if (!$this->simplevalidate->required($user['email'])) {
            $this->result['error_code'] = -200192;
            return;
        }
        if (!$this->simplevalidate->range($user['email'], 1, 30)) {
            $this->result['error_code'] = -200193;
            return;
        }
        if (!$this->simplevalidate->email($user['email'])) {
            $this->result['error_code'] = -200194;
            return;
        }

        if (!$this->simplevalidate->required($user['pwd'])) {
            $this->result['error_code'] = -200195;
            return;
        }
        if (!$this->simplevalidate->range($user['pwd'], 6, 16)) {
            $this->result['error_code'] = -200196;
            return;
        }

        //验证码验证
        if (!$this->geetest->verify('pc')) {
            $this->result['error_code'] = -200148;
            return;
        }

        $user['pwd'] = md5($user['pwd'] . $this->config->config['salt']);
        $remember_me = $this->input->post('remember_me');
        //记住我，下次自动登录
        $remember_me = 1;

        $user = $this->user_model->get_by_email_and_pwd($user['email'], $user['pwd']);
        if (is_array($user)) {
            $success = $this->signin_success_callback($user, $remember_me);
            if ($success <= 0) {
                $this->result['error_code'] = -200152;
                $this->result['errcode'] = $success;
            }
        } else {
            $this->result['error_code'] = -200151;
        }
    }

    /**
     * 登录成功后执行的的更新用户信息、SESSION、COOKIE等内容的处理
     * @param  array $user 用户信息
     * @return int         成功返回1,其它表示错误代码
     */
    private function signin_success_callback($user, $remember_me = null)
    {
        if (!is_array($user)) {
            return -1;
        }

        $user['signin_time'] = $this->now;
        $user['signin_count'] += 1;
        //更新用户信息
        $user = $this->user_model->update($user);
        if (!is_array($user)) {
            return -2;
        }

        //获取设置的皮肤
        $_SESSION['skin'] = $this->skinsetting_model->get_by_userId($user['id']);

        //设置session
        $_SESSION['user'] = $user;
        //下次自动登录,cookie需要加密
        $user_id_encrypt = $this->simpleencrypt->encode($user['id'], $this->config->config['encrypt_key']);
        if (!empty($remember_me)) {
            setcookie('user_id', $user_id_encrypt, time() + $this->config->config['remember_me_time'], '/', '', false, true);
        }
        //禁止下次自动登录
        else {
            setcookie('user_id', '', time() - 3600, '/', '', false, true);
        }
        return 1;
    }

    /**
     * 注册
     */
    public function signup()
    {
        $user['email'] = $this->input->post('email');
        $user['pwd'] = $this->input->post('pwd');
        $user['nickname'] = $this->input->post('nickname');

        //表单验证
        if (!$this->simplevalidate->required($user['email'])) {
            $this->result['error_code'] = -200197;
            return;
        }
        if (!$this->simplevalidate->range($user['email'], 1, 30)) {
            $this->result['error_code'] = -200198;
            return;
        }
        if (!$this->simplevalidate->email($user['email'])) {
            $this->result['error_code'] = -200199;
            return;
        }

        if (!$this->simplevalidate->required($user['pwd'])) {
            $this->result['error_code'] = -200200;
            return;
        }
        if (!$this->simplevalidate->range($user['pwd'], 6, 16)) {
            $this->result['error_code'] = -200201;
            return;
        }

        if (!$this->simplevalidate->required($user['nickname'])) {
            $this->result['error_code'] = -200202;
            return;
        }
        if (!$this->simplevalidate->mix_range($user['nickname'], 1, 16)) {
            $this->result['error_code'] = -200203;
            return;
        }
        if (!$this->simplevalidate->nickname($user['nickname'])) {
            $this->result['error_code'] = -200204;
            return;
        }

        //验证码验证
        if (!$this->geetest->verify('pc')) {
            $this->result['error_code'] = -200148;
            return;
        }

        $user['pwd'] = md5($user['pwd'] . $this->config->config['salt']);

        //查询邮箱是否已被注册
        $_user = $this->user_model->get_by_email($user['email']);
        if (is_array($_user)) {
            $this->result['error_code'] = -200154;
            return;
        }

        //检查昵称是否已被占用
        $_user = $this->user_model->get_by_nickname($user['nickname']);
        if (is_array($_user)) {
            $this->result['error_code'] = -200004;
            return;
        }

        $user['signin_time'] = $this->now;
        $user = $this->user_model->add($user);
        if (is_array($user)) {
            //初始化用户注册数据
            $this->signup_success_callback($user);
            //设置session
            $_SESSION['user'] = $user;
        } else {
            $this->result['error_code'] = -200155;
        }
    }

    /**
     * 注册成功后的回调,初始化新用户相关信息
     * @param  array $user 新注册的用户信息
     * @return bool        成功返回true
     */
    private function signup_success_callback($user)
    {
        //获取设置的皮肤
        $_SESSION['skin'] = $this->skinsetting_model->get_by_userId($user['id']);
        return true;
    }

    /**
     * 完善资料
     */
    public function perfect()
    {
        if (!isset($_SESSION['open_user'])) {
            $this->result['error_code'] = -200001;
            return;
        }
        $open_user = $_SESSION['open_user'];

        $nickname = $this->input->post('nickname');

        //昵称为空
        if (!$this->simplevalidate->required($nickname)) {
            $this->result['error_code'] = -200002;
            return;
        }

        //昵称长度不在1-16位之间
        if (!$this->simplevalidate->mix_range($nickname, 1, 16)) {
            $this->result['error_code'] = -200003;
            return;
        }

        //昵称格式
        if (!$this->simplevalidate->nickname($nickname)) {
            $this->result['error_code'] = -200204;
            return;
        }

        //检查昵称是否已被占用
        $user = $this->user_model->get_by_nickname($nickname);
        if (is_array($user)) {
            $this->result['error_code'] = -200004;
            return;
        }
        unset($user);
        //先添加一个新用户
        $user['nickname'] = $nickname;
        $user['signin_time'] = $this->now;
        $user = $this->user_model->add($user);
        //初始化用户注册数据
        $this->signup_success_callback($user);
        //将新添加的用户与open_user绑定
        $open_user_to_db = array(
            'openid' => $open_user['openid'],
            'user_id' => $user['id'],
            'nickname' => $open_user['nickname'],
        );
        switch ($open_user['ref']) {
            case 'github':
                $this->githubuser_model->update($open_user_to_db);
                break;
            case 'weixin':
                $this->weixinuser_model->update($open_user_to_db);
                break;
            case 'qc':
                $this->qcuser_model->update($open_user_to_db);
                break;
            case 'weibo':
                $this->weibouser_model->update($open_user_to_db);
                break;
            case 'oschina':
                $this->oschinauser_model->update($open_user_to_db);
                break;
        }

        //获取设置的皮肤
        $_SESSION['skin'] = $this->skinsetting_model->get_by_userId($user['id']);

        //设置session
        $_SESSION['user'] = $user;
        //清除session open_user
        unset($_SESSION['open_user']);
    }

    /**
     * 第三方账号登录时绑定已有账号
     */
    public function bind()
    {
        if (!isset($_SESSION['open_user'])) {
            $this->result['error_code'] = -200205;
            return;
        }
        $open_user = $_SESSION['open_user'];

        $email = $this->input->post('email');
        $pwd = $this->input->post('pwd');

        //表单验证
        if (!$this->simplevalidate->required($email)) {
            $this->result['error_code'] = -200206;
            return;
        }
        if (!$this->simplevalidate->range($email, 1, 30)) {
            $this->result['error_code'] = -200207;
            return;
        }
        if (!$this->simplevalidate->email($email)) {
            $this->result['error_code'] = -200208;
            return;
        }

        if (!$this->simplevalidate->required($pwd)) {
            $this->result['error_code'] = -200209;
            return;
        }
        if (!$this->simplevalidate->range($pwd, 6, 16)) {
            $this->result['error_code'] = -200210;
            return;
        }

        //验证码验证
        if (!$this->geetest->verify('pc')) {
            $this->result['error_code'] = -200148;
            return;
        }

        $pwd = md5($pwd . $this->config->config['salt']);
        //检查用户名密码是否正确
        $user = $this->user_model->get_by_email_and_pwd($email, $pwd);
        if (!is_array($user)) {
            $this->result['error_code'] = -200211;
            return;
        }
        //0=未绑定,1=此社交账号已被绑定过,2=此账号已绑定过此类社交账号
        $bind_status = 0;
        switch ($open_user['ref']) {
            case 'github':
                //查询此社交账号是否已绑定过账号了
                $open_user_from_db = $this->githubuser_model->get_by_openid($open_user['openid']);
                if (!is_array($open_user_from_db)) {
                    //查询此账号是否已绑定过此类社交账号了
                    $open_user_from_db = $this->githubuser_model->get_by_userId($user['id']);
                    if (is_array($open_user_from_db)) {
                        $bind_status = 2;
                    }
                } else {
                    if (empty($open_user_from_db['user_id'])) {
                        $open_user_to_db = array(
                            'openid' => $open_user_from_db['openid'],
                            'user_id' => $user['id'],
                        );
                        $this->githubuser_model->update($open_user_to_db);
                    } else {
                        $bind_status = 1;
                    }
                }
                break;
            case 'weixin':
                //查询此社交账号是否已绑定过账号了
                $open_user_from_db = $this->weixinuser_model->get_by_openid($open_user['openid']);
                if (!is_array($open_user_from_db)) {
                    //查询此账号是否已绑定过此类社交账号了
                    $open_user_from_db = $this->weixinuser_model->get_by_userId($user['id']);
                    if (is_array($open_user_from_db)) {
                        $bind_status = 2;
                    }
                } else {
                    if (empty($open_user_from_db['user_id'])) {
                        $open_user_to_db = array(
                            'openid' => $open_user_from_db['openid'],
                            'user_id' => $user['id'],
                        );
                        $this->weixinuser_model->update($open_user_to_db);
                    } else {
                        $bind_status = 1;
                    }
                }
                break;
            case 'qc':
                $open_user_from_db = $this->qcuser_model->get_by_openid($open_user['openid']);
                if (!is_array($open_user_from_db)) {
                    //查询此账号是否已绑定过此类社交账号了
                    $open_user_from_db = $this->qcuser_model->get_by_userId($user['id']);
                    if (is_array($open_user_from_db)) {
                        $bind_status = 2;
                    }
                } else {
                    if (empty($open_user_from_db['user_id'])) {
                        $open_user_to_db = array(
                            'openid' => $open_user_from_db['openid'],
                            'user_id' => $user['id'],
                        );
                        $this->qcuser_model->update($open_user_to_db);
                    } else {
                        $bind_status = 1;
                    }
                }
                break;
            case 'weibo':
                $open_user_from_db = $this->weibouser_model->get_by_openid($open_user['openid']);
                if (!is_array($open_user_from_db)) {
                    //查询此账号是否已绑定过此类社交账号了
                    $open_user_from_db = $this->weibouser_model->get_by_userId($user['id']);
                    if (is_array($open_user_from_db)) {
                        $bind_status = 2;
                    }
                } else {
                    if (empty($open_user_from_db['user_id'])) {
                        $open_user_to_db = array(
                            'openid' => $open_user_from_db['openid'],
                            'user_id' => $user['id'],
                        );
                        $this->weibouser_model->update($open_user_to_db);
                    } else {
                        $bind_status = 1;
                    }
                }
                break;
            case 'oschina':
                $open_user_from_db = $this->oschinauser_model->get_by_openid($open_user['openid']);
                if (!is_array($open_user_from_db)) {
                    //查询此账号是否已绑定过此类社交账号了
                    $open_user_from_db = $this->oschinauser_model->get_by_userId($user['id']);
                    if (is_array($open_user_from_db)) {
                        $bind_status = 2;
                    }
                } else {
                    if (empty($open_user_from_db['user_id'])) {
                        $open_user_to_db = array(
                            'openid' => $open_user_from_db['openid'],
                            'user_id' => $user['id'],
                        );
                        $this->oschinauser_model->update($open_user_to_db);
                    } else {
                        $bind_status = 1;
                    }
                }
                break;
        }
        if ($bind_status == 0) {
            //获取设置的皮肤
            $_SESSION['skin'] = $this->skinsetting_model->get_by_userId($user['id']);

            //设置session
            $_SESSION['user'] = $user;
            //清除session open_user
            unset($_SESSION['open_user']);
        } else if ($bind_status == 1) {
            $this->result['error_code'] = -200212;
        } else if ($bind_status == 2) {
            $this->result['error_code'] = -200213;
        }
    }
}
