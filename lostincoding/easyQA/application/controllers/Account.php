<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 账号控制器
 */
class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('geetest/geetest');
        $this->load->model('skinsetting_model');
        $this->load->library('mailer');
        $this->load->model('user_model');
        $this->load->model('githubuser_model');
        $this->load->model('weixinuser_model');
        $this->load->model('qcuser_model');
        $this->load->model('weibouser_model');
        $this->load->model('oschinauser_model');
    }

    /**
     * 默认跳转到登录页面
     */
    public function index()
    {
        redirect('account/signin');
    }

    /**
     * 登录页面
     */
    public function signin()
    {
        if (isset($this->user)) {
            redirect();
        }

        $this->data['title'] = '登录';
        $this->data['account_nav_active'] = 'signin';
        $this->load->view("{$this->theme_id}/account/signin", $this->data);
    }

    /**
     * 注册页面
     */
    public function signup()
    {
        if (isset($this->user)) {
            redirect();
        }

        $this->data['title'] = '注册';
        $this->data['account_nav_active'] = 'signup';
        $this->load->view("{$this->theme_id}/account/signup", $this->data);
    }

    /**
     * 登录成功后执行的的更新用户信息、SESSION、COOKIE等内容的处理
     * @param  array $user 用户信息
     * @return int         成功返回1,其它表示错误代码
     */
    private function signin_success_callback($user, $remember_me = 1)
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

        //如果账号已被冻结
        if ($user['freeze_status'] == 2) {
            session_destroy();
            setcookie('user_id', '', time() - 3600, '/', '', false, true);
            echo '账号已被冻结。';
            exit;
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
     * 退出登录
     */
    public function signout()
    {
        session_destroy();
        setcookie('user_id', '', time() - 3600, '/', '', false, true);
        redirect();
    }

    /**
     * 验证邮箱
     */
    public function email_activate()
    {
        $this->data['title'] = '验证邮箱';

        $this->data['error_code'] = 'ok';

        $user = $this->user_model->get($this->user['id']);
        //邮箱已激活
        if ($user['email_status'] == 2) {
            $_SESSION['user']['email_status'] = 2;
            $this->data['email'] = $this->user['email'];
            $this->load->view("{$this->theme_id}/account/email_activate", $this->data);
            return;
        }

        //加密的字符串
        $encrypt_code = urldecode($this->input->get('encrypt_code'));
        $text = $this->simpleencrypt->decode($encrypt_code, $this->config->config['encrypt_key']);
        $arr = explode('|', $text);
        //参数错误
        if (count($arr) != 2) {
            $this->data['error_code'] = -1;
            $this->load->view("{$this->theme_id}/account/email_activate", $this->data);
            return;
        }
        $email = $arr[0];

        //不是当前邮箱
        if ($user['email'] != $email) {
            $this->data['error_code'] = -3;
            $this->load->view("{$this->theme_id}/account/email_activate", $this->data);
            return;
        }

        $this->data['email'] = $email;
        $user = $this->user_model->get_by_email($email);
        $time = $arr[1];
        //链接过期
        if (time($this->now, '-' . $this->config->config['account']['email_link_timeout'] . ' hour') < $time) {
            $this->data['error_code'] = -2;
            $this->load->view("{$this->theme_id}/account/email_activate", $this->data);
            return;
        }

        //验证邮箱
        if ($user['email_status'] == 1) {
            $user['email_status'] = 2;
            $user = $this->user_model->update($user);
            $_SESSION['user'] = $user;
            $this->data['user'] = $user;
        }
        $this->load->view("{$this->theme_id}/account/email_activate", $this->data);
    }

    /**
     * 忘记密码
     */
    public function forgot_pwd()
    {
        $this->data['title'] = '找回密码';

        //找回密码的邮箱账号
        $email = $this->input->post('email');
        $this->data['email'] = $email;

        //发送验证邮件
        if (!empty($email)) {
            //表单验证
            if (!$this->simplevalidate->email($email)) {
                redirect('account/forgot_pwd');
            }

            //验证码验证
            if (!$this->geetest->verify('pc')) {
                $this->data['tips'] = '验证码错误。';
                $this->load->view("{$this->theme_id}/account/forgot_pwd", $this->data);
                return;
            }

            //查询用户是否存在
            $user = $this->user_model->get_by_email($email);
            if (!is_array($user)) {
                redirect('account/forgot_pwd');
            }

            $this->data['title'] = '邮件已发送，请查收';
            //发送找回密码的邮件
            $this->send_reset_pwd_email($email);
            $arr = explode('@', $email);
            $this->data['email_homepage'] = 'http://mail.' . $arr[1];
        }
        $this->load->view("{$this->theme_id}/account/forgot_pwd", $this->data);
    }

    /**
     * 重设密码
     */
    public function reset_pwd()
    {
        $this->data['title'] = '找回密码';

        //加密的字符串
        if ($_POST) {
            $encrypt_code = $this->input->post('encrypt_code');
        } else {
            $encrypt_code = urldecode($this->input->get('encrypt_code'));
        }
        //重新设置的新密码
        $pwd = $this->input->post('pwd');

        $text = $this->simpleencrypt->decode($encrypt_code, $this->config->config['encrypt_key']);
        $arr = explode('|', $text);
        //参数错误
        if (count($arr) != 2) {
            $this->load->view("{$this->theme_id}/account/reset_pwd", $this->data);
            echo '参数错误';
            return;
        }
        $email = $arr[0];
        $user = $this->user_model->get_by_email($email);
        $time = $arr[1];
        //链接过期
        if (time($this->now, '-' . $this->config->config['account']['email_link_timeout'] . ' hour') < $time) {
            $this->load->view("{$this->theme_id}/account/reset_pwd", $this->data);
            echo '链接过期';
            return;
        }

        $this->data['email'] = $user['email'];

        //显示重置密码表单
        if (empty($pwd)) {
            $this->data['title'] = '重置密码';
            $this->data['encrypt_code'] = $encrypt_code;
            $this->load->view("{$this->theme_id}/account/reset_pwd", $this->data);
            return;
        }
        //重置密码
        else {
            if (!$this->simplevalidate->range($pwd, 6, 16)) {
                redirect('account/reset_pwd?encrypt_code=' . $encrypt_code);
            }

            //验证码验证
            if (!$this->geetest->verify('pc')) {
                $this->data['tips'] = '验证码错误。';
                $this->load->view("{$this->theme_id}/account/reset_pwd", $this->data);
                return;
            }

            $this->data['title'] = '密码修改成功';
            //设置新密码
            $user['pwd'] = md5($pwd . $this->config->config['salt']);
            $this->user_model->update($user);
            $this->load->view("{$this->theme_id}/account/reset_pwd", $this->data);
        }
    }

    /**
     * 发送注册邮件
     * @param  do   string send/resend发送邮件,null为不发送邮件
     * @return bool        成功返回true,失败返回false
     */
    public function signup_email_send($do = null)
    {
        if (empty($this->user['email'])) {
            redirect();
        }

        //如果已激活
        if ($this->user['email_status'] == 2) {
            //redirect('account/email_activate');
        }

        $email = $this->user['email'];
        $this->data['title'] = '邮箱验证';
        $this->data['email'] = $this->user['email'];
        $arr = explode('@', $email);
        $this->data['email_homepage'] = 'http://mail.' . $arr[1];

        if (empty($do)) {
            $this->load->view("{$this->theme_id}/account/signup_email_send", $this->data);
            return;
        }

        if (in_array($do, array('send', 'resend'))) {
            $site_name = $this->config->config['site_info']['name'];
            $text = $email . '|' . time();
            //加密串
            $encrypt_code = urlencode(urlencode($this->simpleencrypt->encode($text, $this->config->config['encrypt_key'])));
            $url = base_url() . 'account/email_activate?encrypt_code=' . $encrypt_code;
            $email_content_html = '<div style="padding:25px;text-align:center;color:#513e31;background:white;border-bottom:1px solid #c3b29e;box-shadow:0 2px 4px rgba(0,0,0,.1);">
            <h3 style="margin:0;padding:0;font-size:large;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;">【' . $site_name . '】帐号成功创建</h3>
            <p>用户名：' . $email . '，请点击以下按钮激活账号。</p>
            <a href="' . $url . '" style="display:block;width:200px;height:44px;border:1px solid #a37951;line-height:44px;margin:0 auto 15px;border-radius:4px;color:#513e31;text-decoration:none;text-shadow:0 1px 0 rgba(255,255,255,.5);box-shadow:0 1px 0 rgba(255,255,255,.5) inset;background:#e8d7b8;background:-webkit-gradient(linear,left top,left bottom,from(#e8d8b9),to(#d4bd95));background:-webkit-linear-gradient(top,#e8d8b9,#d4bd95);background:-moz-linear-gradient(top,#e8d8b9,#d4bd95);background:-o-linear-gradient(top,#e8d8b9,#d4bd95);background:linear-gradient(to bottom,#e8d8b9,#d4bd95);" target="_blank">激活帐号</a>
            <div style="font-size:11px;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;">
                <p>也可以将地址复制到浏览器打开：</p>
                <a href="' . $url . '" style="color:#448ccb;" target="_blank">' . $url . '</a></div>
        </div>
        <div style="padding:0 10px 20px 10px;color:#a49382;text-shadow:0 1px 0 rgba(255,255,255,.5);font-size:11px;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;"><p style="margin-bottom:6px;">如果您错误的收到此邮件，可以忽略这个提示，无需回复。</p><span>- ' . $site_name . ' 团队</span></div>';
            //邮箱相关设置
            $email_config = $this->config->config['email'];
            $email_config['Address'] = array($email, '');
            $email_config['Subject'] = '【' . $site_name . '】请求确认您的电子邮件';
            $email_config['msgHTML'] = $email_content_html;
            $this->mailer->post($email_config);
        }
        redirect('account/signup_email_send');
    }

    /**
     * 发送找回密码的邮件
     * @param  text $email
     * @return bool 成功返回true,失败返回false
     */
    private function send_reset_pwd_email($email)
    {
        $site_name = $this->config->config['site_info']['name'];
        $text = $email . '|' . time();
        //加密串
        $encrypt_code = urlencode(urlencode($this->simpleencrypt->encode($text, $this->config->config['encrypt_key'])));
        $url = base_url() . 'account/reset_pwd?encrypt_code=' . $encrypt_code;

        $email_content_html = '<div style="padding:25px;text-align:center;color:#513e31;background:white;border-bottom:1px solid #c3b29e;box-shadow:0 2px 4px rgba(0,0,0,.1);">
            <h3 style="margin:0;padding:0;font-size:large;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;">找回密码</h3>
            <p>用户名：' . $email . '，请点击以下按钮。</p>
            <a href="' . $url . '" style="display:block;width:200px;height:44px;border:1px solid #a37951;line-height:44px;margin:0 auto 15px;border-radius:4px;color:#513e31;text-decoration:none;text-shadow:0 1px 0 rgba(255,255,255,.5);box-shadow:0 1px 0 rgba(255,255,255,.5) inset;background:#e8d7b8;background:-webkit-gradient(linear,left top,left bottom,from(#e8d8b9),to(#d4bd95));background:-webkit-linear-gradient(top,#e8d8b9,#d4bd95);background:-moz-linear-gradient(top,#e8d8b9,#d4bd95);background:-o-linear-gradient(top,#e8d8b9,#d4bd95);background:linear-gradient(to bottom,#e8d8b9,#d4bd95);" target="_blank">找回密码</a>
            <div style="font-size:11px;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;">
                <p>也可以将地址复制到浏览器打开：</p>
                <a href="' . $url . '" style="color:#448ccb;" target="_blank">' . $url . '</a></div>
        </div>
        <div style="padding:0 10px 20px 10px;color:#a49382;text-shadow:0 1px 0 rgba(255,255,255,.5);font-size:11px;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;"><p style="margin-bottom:6px;">如果您错误的收到此邮件，可以忽略这个提示，无需回复。</p><span>- ' . $site_name . ' 团队</span></div>';
        //邮箱相关设置
        $email_config = $this->config->config['email'];
        $email_config['Address'] = array($email, '');
        $email_config['Subject'] = '【' . $site_name . '】找回密码';
        $email_config['msgHTML'] = $email_content_html;
        return $this->mailer->post($email_config);
    }

    /**
     * github登录
     */
    public function github()
    {
        $client_id = $this->config->config['github']['client_id'];
        $redirect_uri = $this->config->config['github']['redirect_uri'];
        $state = $this->time;
        $_SESSION['state'] = $state;
        $url = 'https://github.com/login/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=user&state=' . $state . '&allow_signup=1';
        redirect($url);
    }

    /**
     * github登录回调
     */
    public function github_callback()
    {
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        if (!isset($code)) {
            echo 'need code';
            return;
        }
        //验证state
        if (!isset($_SESSION['state']) || $state != $_SESSION['state']) {
            echo 'CSRF';
            return;
        }
        unset($_SESSION['state']);

        $url = 'https://github.com/login/oauth/access_token';
        $headers = array('Accept: application/json');
        //获取token
        $token = $this->simplecurl->post(
            $url,
            array(
                'client_id' => $this->config->config['github']['client_id'],
                'client_secret' => $this->config->config['github']['client_secret'],
                'code' => $code,
                'redirect_uri' => $this->config->config['github']['redirect_uri'],
                'state' => $state,
            ),
            true,
            $headers
        );
        if ($token) {
            $url = 'https://api.github.com/user';
            $headers = array('User-Agent: Awesome-Octocat-App');
            //获取用户信息
            $open_user = $this->simplecurl->get(
                $url,
                array(
                    'access_token' => $token['access_token'],
                ),
                true,
                $headers
            );
            $open_user['ref'] = 'github';
            $open_user['token'] = $token;
            $open_user['openid'] = $open_user['id'];
            $open_user['nickname'] = $open_user['login'];

            //保存Github用户信息
            $open_signin_status = $this->github_signin_success_callback($open_user);
            $this->open_signin_callback($open_user, $open_signin_status);
        } else {
            echo 'Github登录失败 <a href="/account/signin">返回重新登录&raquo;</a>';
        }
    }

    /**
     * Github登录用户信息更新
     * @param  array $open_user Github登录用户信息
     * @return bool             首次登录或者账号未完善返回1,非首次登录返回2,其它为错误代码
     */
    private function github_signin_success_callback($open_user)
    {
        //先清空session中的open_user信息
        unset($_SESSION['open_user']);

        $open_user_from_db = $this->githubuser_model->get_by_openid($open_user['openid']);
        //非首次登录
        if (is_array($open_user_from_db)) {
            //账号未完善
            if ($open_user_from_db['user_id'] == null) {
                //如果用户已是登录状态,直接绑定账号
                if (isset($this->user)) {
                    $open_user_to_db = array(
                        'openid' => $open_user['openid'],
                        'user_id' => $this->user['id'],
                        'nickname' => $open_user['nickname'],
                    );
                    $this->githubuser_model->update($open_user_to_db);
                    redirect('account/bind');
                }
                //将社交账号信息放入session,供完善资料或绑定账号时使用
                $_SESSION['open_user']['ref'] = 'github';
                $_SESSION['open_user']['ref_name'] = 'Github';
                $_SESSION['open_user']['openid'] = $open_user['openid'];
                $_SESSION['open_user']['nickname'] = $open_user['login'];
                return 1;
            }

            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signin_time' => $this->now,
            );
            //更新用户信息
            $open_user_from_db = $this->githubuser_model->update($open_user_to_db);
            //记录Github用户信息失败
            if (!is_array($open_user_from_db)) {
                return -2;
            }
            //查询用户信息
            $user = $this->user_model->get($open_user_from_db['user_id']);

            //登录成功
            $this->signin_success_callback($user);
            return 2;
        }
        //首次登录
        else {
            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'user_id' => null,
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signup_time' => $this->now,
                'signin_time' => $this->now,
            );
            //如果用户已是登录状态,直接绑定账号
            if (isset($this->user)) {
                $open_user_to_db['user_id'] = $this->user['id'];
            }
            $open_user_from_db = $this->githubuser_model->add($open_user_to_db);
            //记录用户信息失败
            if (!is_array($open_user_from_db)) {
                return -1;
            }
            //如果用户已是登录状态,直接跳转
            if (isset($this->user)) {
                redirect('account/bind');
            }
            //将社交账号信息放入session,供完善资料或绑定账号时使用
            $_SESSION['open_user']['ref'] = 'github';
            $_SESSION['open_user']['ref_name'] = 'Github';
            $_SESSION['open_user']['openid'] = $open_user['openid'];
            $_SESSION['open_user']['nickname'] = $open_user['login'];
            return 1;
        }
    }

    /**
     * 微信登录
     */
    public function weixin()
    {
        $appid = $this->config->config['weixin']['appid'];
        $redirect_uri = $this->config->config['weixin']['redirect_uri'];
        $state = $this->time;
        $_SESSION['state'] = $state;
        $url = 'https://open.weixin.qq.com/connect/qrconnect?appid=' . $appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_login&state=' . $state . '#wechat_redirect';
        redirect($url);
    }

    /**
     * 微信登录回调
     */
    public function weixin_callback()
    {
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        if (!isset($code)) {
            redirect('account/signin');
        }
        //验证state
        if (!isset($_SESSION['state']) || $state != $_SESSION['state']) {
            echo 'CSRF';
            return;
        }
        unset($_SESSION['state']);

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        //获取token
        $token = $this->simplecurl->post(
            $url,
            array(
                'appid' => $this->config->config['weixin']['appid'],
                'secret' => $this->config->config['weixin']['secret'],
                'code' => $code,
                'grant_type' => 'authorization_code',
            ),
            true
        );
        if ($token) {
            $url = 'https://api.weixin.qq.com/sns/userinfo';
            //获取用户信息
            $open_user = $this->simplecurl->get(
                $url,
                array(
                    'access_token' => $token['access_token'],
                    'openid' => $token['openid'],
                ),
                true
            );
            $open_user['ref'] = 'weixin';
            $open_user['token'] = $token;
            $open_user['unionid'] = $open_user['unionid'];
            $open_user['openid'] = $open_user['openid'];
            $open_user['nickname'] = $open_user['nickname'];

            //微信用户信息
            $open_signin_status = $this->weixin_signin_success_callback($open_user);
            $this->open_signin_callback($open_user, $open_signin_status);
        } else {
            echo '微信登录失败 <a href="/account/signin">返回重新登录&raquo;</a>';
        }
    }

    /**
     * 微信登录用户信息更新
     * @param  array $open_user 微信登录用户信息
     * @return bool             首次登录或者账号未完善返回1,非首次登录返回2,其它为错误代码
     */
    private function weixin_signin_success_callback($open_user)
    {
        //先清空session中的open_user信息
        unset($_SESSION['open_user']);

        $open_user_from_db = $this->weixinuser_model->get_by_openid($open_user['openid']);
        //非首次登录
        if (is_array($open_user_from_db)) {
            //账号未完善
            if ($open_user_from_db['user_id'] == null) {
                //如果用户已是登录状态,直接绑定账号
                if (isset($this->user)) {
                    $open_user_to_db = array(
                        'unionid' => $open_user['unionid'],
                        'openid' => $open_user['openid'],
                        'user_id' => $this->user['id'],
                        'nickname' => $open_user['nickname'],
                    );
                    $this->weixinuser_model->update($open_user_to_db);
                    redirect('u/bind');
                }
                //将社交账号信息放入session,供完善资料或绑定账号时使用
                $_SESSION['open_user']['ref'] = 'weixin';
                $_SESSION['open_user']['ref_name'] = '微信';
                $_SESSION['open_user']['unionid'] = $open_user['unionid'];
                $_SESSION['open_user']['openid'] = $open_user['openid'];
                $_SESSION['open_user']['nickname'] = $open_user['nickname'];
                return 1;
            }

            $open_user_to_db = array(
                'unionid' => $open_user['unionid'],
                'openid' => $open_user['openid'],
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signin_time' => $this->now,
            );
            //更新用户信息
            $open_user_from_db = $this->weixinuser_model->update($open_user_to_db);
            //weixin用户信息失败
            if (!is_array($open_user_from_db)) {
                return -2;
            }
            //查询用户信息
            $user = $this->user_model->get($open_user_from_db['user_id']);

            //登录成功
            $this->signin_success_callback($user);
            return 2;
        }
        //首次登录
        else {
            $open_user_to_db = array(
                'unionid' => $open_user['unionid'],
                'openid' => $open_user['openid'],
                'user_id' => null,
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signup_time' => $this->now,
                'signin_time' => $this->now,
            );
            //如果用户已是登录状态,直接绑定账号
            if (isset($this->user)) {
                $open_user_to_db['user_id'] = $this->user['id'];
            }
            $open_user_from_db = $this->weixinuser_model->add($open_user_to_db);
            //记录用户信息失败
            if (!is_array($open_user_from_db)) {
                return -1;
            }
            //如果用户已是登录状态,直接跳转
            if (isset($this->user)) {
                redirect('u/bind');
            }
            //将社交账号信息放入session,供完善资料或绑定账号时使用
            $_SESSION['open_user']['ref'] = 'weixin';
            $_SESSION['open_user']['ref_name'] = '微信';
            $_SESSION['open_user']['unionid'] = $open_user['unionid'];
            $_SESSION['open_user']['openid'] = $open_user['openid'];
            $_SESSION['open_user']['nickname'] = $open_user['nickname'];
            return 1;
        }
    }

    /**
     * QQ登录
     */
    public function qq_connect()
    {
        $this->load->library('qc/class/qc');
        //跳转至QQ登录页面
        $this->qc->qq_login();
    }

    /**
     * QQ登录回调
     */
    public function qc_callback()
    {
        $this->load->library('qc/class/qc');
        $token = $this->qc->qq_callback();

        //登录成功
        if (is_array($token) && isset($token['access_token'])) {
            $openid = $this->qc->get_openid();
            $this->qc->__construct();
            $open_user = $this->qc->get_user_info();
            $open_user['ref'] = 'qc';
            $open_user['token'] = $token;
            $open_user['openid'] = $openid;
            $open_user['nickname'] = $open_user['nickname'];

            //保存QQ用户信息
            $open_signin_status = $this->qc_signin_success_callback($open_user);
            $this->open_signin_callback($open_user, $open_signin_status);
        } else {
            echo 'QQ登录失败 <a href="/account/signin">返回重新登录&raquo;</a>';
        }
    }

    /**
     * QQ登录用户信息更新
     * @param  array $open_user QQ登录用户信息
     * @return bool             首次登录或者账号未完善返回1,非首次登录返回2,其它为错误代码
     */
    private function qc_signin_success_callback($open_user)
    {
        //先清空session中的open_user信息
        unset($_SESSION['open_user']);

        $open_user_from_db = $this->qcuser_model->get_by_openid($open_user['openid']);
        //非首次登录
        if (is_array($open_user_from_db)) {
            //账号未完善
            if ($open_user_from_db['user_id'] == null) {
                //如果用户已是登录状态,直接绑定账号
                if (isset($this->user)) {
                    $open_user_to_db = array(
                        'openid' => $open_user['openid'],
                        'user_id' => $this->user['id'],
                        'nickname' => $open_user['nickname'],
                    );
                    $this->qcuser_model->update($open_user_to_db);
                    redirect('u/bind');
                }
                //将社交账号信息放入session,供完善资料或绑定账号时使用
                $_SESSION['open_user']['ref'] = 'qc';
                $_SESSION['open_user']['ref_name'] = 'QQ';
                $_SESSION['open_user']['openid'] = $open_user['openid'];
                $_SESSION['open_user']['nickname'] = $open_user['nickname'];
                return 1;
            }

            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signin_time' => $this->now,
            );
            //更新用户信息
            $open_user_from_db = $this->qcuser_model->update($open_user_to_db);
            //记录QQ用户信息失败
            if (!is_array($open_user_from_db)) {
                return -2;
            }
            //查询用户信息
            $user = $this->user_model->get($open_user_from_db['user_id']);

            //登录成功
            $this->signin_success_callback($user);
            return 2;
        }
        //首次登录
        else {
            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'user_id' => null,
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signup_time' => $this->now,
                'signin_time' => $this->now,
            );
            //如果用户已是登录状态,直接绑定账号
            if (isset($this->user)) {
                $open_user_to_db['user_id'] = $this->user['id'];
            }
            $open_user_from_db = $this->qcuser_model->add($open_user_to_db);
            //记录用户信息失败
            if (!is_array($open_user_from_db)) {
                return -1;
            }
            //如果用户已是登录状态,直接跳转
            if (isset($this->user)) {
                redirect('u/bind');
            }
            //将社交账号信息放入session,供完善资料或绑定账号时使用
            $_SESSION['open_user']['ref'] = 'qc';
            $_SESSION['open_user']['ref_name'] = 'QQ';
            $_SESSION['open_user']['openid'] = $open_user['openid'];
            $_SESSION['open_user']['nickname'] = $open_user['nickname'];
            return 1;
        }
    }

    /**
     * 微博登录
     */
    public function weibo()
    {
        $this->load->library(
            'weibo/SaeTOAuthV2',
            array(
                'akey' => $this->config->config['weibo']['WB_AKEY'],
                'skey' => $this->config->config['weibo']['WB_SKEY'],
            ),
            'o'
        );
        //跳转至微博登录页面
        $code_url = $this->o->getAuthorizeURL($this->config->config['weibo']['WB_CALLBACK_URL']);
        $state = $this->time;
        $_SESSION['state'] = $state;
        $code_url .= '&state=' . $state;
        redirect($code_url);
    }

    /**
     * 微博登录回调
     */
    public function weibo_callback()
    {
        $this->load->library(
            'weibo/SaeTOAuthV2',
            array(
                'akey' => $this->config->config['weibo']['WB_AKEY'],
                'skey' => $this->config->config['weibo']['WB_SKEY'],
            ),
            'o'
        );
        $code = $this->input->get('code');
        if (isset($code)) {
            $state = $this->input->get('state');
            //验证state
            if (!isset($_SESSION['state']) || $state != $_SESSION['state']) {
                echo 'CSRF';
                return;
            }
            unset($_SESSION['state']);
            $keys = array();
            $keys['code'] = $code;
            $keys['redirect_uri'] = $this->config->config['weibo']['WB_CALLBACK_URL'];
            $token = $this->o->getAccessToken('code', $keys);
        }

        //登录成功
        if ($token) {
            $this->load->library(
                'weibo/SaeTClientV2',
                array(
                    'akey' => $this->config->config['weibo']['WB_AKEY'],
                    'skey' => $this->config->config['weibo']['WB_SKEY'],
                    'access_token' => $token['access_token'],
                ),
                'c'
            );
            $uid_arr = $this->c->get_uid();
            $openid = $uid_arr['uid'];
            $open_user = $this->c->show_user_by_id($openid);
            $open_user['ref'] = 'weibo';
            $open_user['token'] = $token;
            $open_user['openid'] = $openid;
            $open_user['nickname'] = $open_user['screen_name'];

            //保存微博用户信息
            $open_signin_status = $this->weibo_signin_success_callback($open_user);
            $this->open_signin_callback($open_user, $open_signin_status);
        } else {
            echo '微博登录失败 <a href="/account/signin">返回重新登录&raquo;</a>';
        }
    }

    /**
     * 微博登录用户信息更新
     * @param  array $open_user 微博登录用户信息
     * @return bool             首次登录或者账号未完善返回1,非首次登录返回2,其它为错误代码
     */
    private function weibo_signin_success_callback($open_user)
    {
        //先清空session中的open_user信息
        unset($_SESSION['open_user']);

        $open_user_from_db = $this->weibouser_model->get_by_openid($open_user['openid']);
        //非首次登录
        if (is_array($open_user_from_db)) {
            //账号未完善
            if ($open_user_from_db['user_id'] == null) {
                //如果用户已是登录状态,直接绑定账号
                if (isset($this->user)) {
                    $open_user_to_db = array(
                        'openid' => $open_user['openid'],
                        'user_id' => $this->user['id'],
                        'nickname' => $open_user['nickname'],
                    );
                    $this->weibouser_model->update($open_user_to_db);
                    redirect('u/bind');
                }
                //将社交账号信息放入session,供完善资料或绑定账号时使用
                $_SESSION['open_user']['ref'] = 'weibo';
                $_SESSION['open_user']['ref_name'] = '微博';
                $_SESSION['open_user']['openid'] = $open_user['openid'];
                $_SESSION['open_user']['nickname'] = $open_user['screen_name'];
                return 1;
            }

            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signin_time' => $this->now,
            );
            //更新用户信息
            $open_user_from_db = $this->weibouser_model->update($open_user_to_db);
            //记录微博用户信息失败
            if (!is_array($open_user_from_db)) {
                return -2;
            }
            //查询用户信息
            $user = $this->user_model->get($open_user_from_db['user_id']);

            //登录成功
            $this->signin_success_callback($user);
            return 2;
        }
        //首次登录
        else {
            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'user_id' => null,
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signup_time' => $this->now,
                'signin_time' => $this->now,
            );
            //如果用户已是登录状态,直接绑定账号
            if (isset($this->user)) {
                $open_user_to_db['user_id'] = $this->user['id'];
            }
            $open_user_from_db = $this->weibouser_model->add($open_user_to_db);
            //记录用户信息失败
            if (!is_array($open_user_from_db)) {
                return -1;
            }
            //如果用户已是登录状态,直接跳转
            if (isset($this->user)) {
                redirect('u/bind');
            }
            //将社交账号信息放入session,供完善资料或绑定账号时使用
            $_SESSION['open_user']['ref'] = 'weibo';
            $_SESSION['open_user']['ref_name'] = '微博';
            $_SESSION['open_user']['openid'] = $open_user['openid'];
            $_SESSION['open_user']['nickname'] = $open_user['screen_name'];
            return 1;
        }
    }

    /**
     * oschina登录
     */
    public function oschina()
    {
        $client_id = $this->config->config['oschina']['client_id'];
        $redirect_uri = $this->config->config['oschina']['redirect_uri'];
        $state = $this->time;
        $_SESSION['state'] = $state;
        $url = 'https://www.oschina.net/action/oauth2/authorize?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $redirect_uri . '&state=' . $state;
        redirect($url);
    }

    /**
     * oschina登录回调
     */
    public function oschina_callback()
    {
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        if (!isset($code)) {
            echo 'need code';
            return;
        }
        //验证state
        if (!isset($_SESSION['state']) || $state != $_SESSION['state']) {
            echo 'CSRF';
            return;
        }
        unset($_SESSION['state']);

        $url = 'https://www.oschina.net/action/openapi/token';
        //获取token
        $token = $this->simplecurl->get(
            $url,
            array(
                'client_id' => $this->config->config['oschina']['client_id'],
                'client_secret' => $this->config->config['oschina']['client_secret'],
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->config->config['oschina']['redirect_uri'],
                'code' => $code,
                'dataType' => 'json',
            ),
            true
        );
        if ($token) {
            $url = 'https://www.oschina.net/action/openapi/user';
            //获取用户信息
            $open_user = $this->simplecurl->get(
                $url,
                array(
                    'access_token' => $token['access_token'],
                ),
                true
            );
            $open_user['ref'] = 'oschina';
            $open_user['token'] = $token;
            $open_user['openid'] = $open_user['id'];
            $open_user['nickname'] = $open_user['name'];

            //保存oschina用户信息
            $open_signin_status = $this->oschina_signin_success_callback($open_user);
            $this->open_signin_callback($open_user, $open_signin_status);
        } else {
            echo '开源中国登录失败 <a href="/account/signin">返回重新登录&raquo;</a>';
        }
    }

    /**
     * oschina登录用户信息更新
     * @param  array $open_user oschina登录用户信息
     * @return bool             首次登录或者账号未完善返回1,非首次登录返回2,其它为错误代码
     */
    private function oschina_signin_success_callback($open_user)
    {
        //先清空session中的open_user信息
        unset($_SESSION['open_user']);

        $open_user_from_db = $this->oschinauser_model->get_by_openid($open_user['openid']);
        //非首次登录
        if (is_array($open_user_from_db)) {
            //账号未完善
            if ($open_user_from_db['user_id'] == null) {
                //如果用户已是登录状态,直接绑定账号
                if (isset($this->user)) {
                    $open_user_to_db = array(
                        'openid' => $open_user['openid'],
                        'user_id' => $this->user['id'],
                        'nickname' => $open_user['nickname'],
                    );
                    $this->oschinauser_model->update($open_user_to_db);
                    redirect('account/bind');
                }
                //将社交账号信息放入session,供完善资料或绑定账号时使用
                $_SESSION['open_user']['ref'] = 'oschina';
                $_SESSION['open_user']['ref_name'] = '开源中国';
                $_SESSION['open_user']['openid'] = $open_user['openid'];
                $_SESSION['open_user']['nickname'] = $open_user['login'];
                return 1;
            }

            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signin_time' => $this->now,
            );
            //更新用户信息
            $open_user_from_db = $this->oschinauser_model->update($open_user_to_db);
            //记录oschina用户信息失败
            if (!is_array($open_user_from_db)) {
                return -2;
            }
            //查询用户信息
            $user = $this->user_model->get($open_user_from_db['user_id']);

            //登录成功
            $this->signin_success_callback($user);
            return 2;
        }
        //首次登录
        else {
            $open_user_to_db = array(
                'openid' => $open_user['openid'],
                'user_id' => null,
                'nickname' => $open_user['nickname'],
                'access_token' => $open_user['token']['access_token'],
                'signup_time' => $this->now,
                'signin_time' => $this->now,
            );
            //如果用户已是登录状态,直接绑定账号
            if (isset($this->user)) {
                $open_user_to_db['user_id'] = $this->user['id'];
            }
            $open_user_from_db = $this->oschinauser_model->add($open_user_to_db);
            //记录用户信息失败
            if (!is_array($open_user_from_db)) {
                return -1;
            }
            //如果用户已是登录状态,直接跳转
            if (isset($this->user)) {
                redirect('account/bind');
            }
            //将社交账号信息放入session,供完善资料或绑定账号时使用
            $_SESSION['open_user']['ref'] = 'oschina';
            $_SESSION['open_user']['ref_name'] = '开源中国';
            $_SESSION['open_user']['openid'] = $open_user['openid'];
            $_SESSION['open_user']['nickname'] = $open_user['nickname'];
            return 1;
        }
    }

    /**
     * 社交账号登录后回调
     * @param  array $open_user 社交账号用户信息
     * @param  int   $open_signin_status 社交账号登录状态
     */
    private function open_signin_callback($open_user, $open_signin_status)
    {
        //社交账号登录资料更新成功
        if ($open_signin_status > 0) {
            //首次登录,前往完善资料
            if ($open_signin_status == 1) {
                //如果已登录状态,则表明此为绑定操作,如里未登录状态,则表明此为登录操作
                if (isset($this->user)) {
                    redirect('account/bind');
                } else {
                    redirect('account/perfect');
                }
            }
            //非首次登录
            else if ($open_signin_status == 2) {
                switch ($open_user['ref']) {
                    case 'github':
                        $open_user_from_db = $this->githubuser_model->get_by_openid($open_user['openid']);
                        break;
                    case 'weixin':
                        $open_user_from_db = $this->weixinuser_model->get_by_openid($open_user['openid']);
                        break;
                    case 'qc':
                        $open_user_from_db = $this->qcuser_model->get_by_openid($open_user['openid']);
                        break;
                    case 'weibo':
                        $open_user_from_db = $this->weibouser_model->get_by_openid($open_user['openid']);
                        break;
                    case 'oschina':
                        $open_user_from_db = $this->oschinauser_model->get_by_openid($open_user['openid']);
                        break;
                }
                $user = $this->user_model->get($open_user_from_db['user_id']);
                $this->signin_success_callback($user);
                redirect();
            }
        }
        //信息更新失败
        else {
            echo '登录失败，错误代码：' . $open_signin_status . ' <a href="/account/signin">返回重新登录&raquo;</a>';
        }
    }

    /**
     * 绑定已有账号
     */
    public function bind()
    {
        if (!isset($_SESSION['open_user'])) {
            echo 'need open_user';
            return;
        }
        $open_user = $_SESSION['open_user'];

        $this->data['title'] = '绑定已有账号';
        $this->data['account_nav_active'] = 'bind';
        $this->data['open_user'] = $open_user;

        $this->load->view("{$this->theme_id}/account/bind", $this->data);
    }

    /**
     * 完善资料
     */
    public function perfect()
    {
        if (!isset($_SESSION['open_user'])) {
            echo 'need open_user';
            return;
        }
        $open_user = $_SESSION['open_user'];

        $this->data['title'] = '完善资料';
        $this->data['account_nav_active'] = 'perfect';
        $this->data['open_user'] = $open_user;

        $this->load->view("{$this->theme_id}/account/perfect", $this->data);
    }
}
