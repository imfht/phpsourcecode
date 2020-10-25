<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 用户账号接口控制器
 */
class Account extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('geetest/geetest');
        $this->load->library('mailer');
        $this->load->model('user_model');
        $this->load->model('githubuser_model');
        $this->load->model('weixinuser_model');
        $this->load->model('qcuser_model');
        $this->load->model('weibouser_model');
    }

    /**
     * 签到
     */
    public function sign()
    {
        $error_code = sign();
        if ($error_code < 0) {
            $this->result['error_code'] = $error_code;
        }

        $this->result['sign_info'] = $_SESSION['sign_info'];
        $this->result['sign_points'] = $error_code;
    }

    /**
     * 更新头像
     */
    public function update_avatar()
    {
        $avatar_ext = $this->input->post('avatar_ext');
        //如果是上传的头像,加上一个参数,为了防止七牛的缓存
        if (in_array($avatar_ext, array('jpg', 'png', 'gif'))) {
            $avatar_ext .= '?' . $this->time;
        }
        $user = array(
            'id' => $this->user['id'],
            'avatar_ext' => $avatar_ext,
        );
        $user = $this->user_model->update($user);
        if (is_array($user)) {
            $_SESSION['user']['avatar_ext'] = $avatar_ext;
            $this->result['avatar'] = create_avatar_url($this->user['id'], $avatar_ext);
        } else {
            $this->result['error_code'] = -200070;
        }
    }

    /**
     * 更新个人资料
     */
    public function update_profile()
    {
        $gender = $this->input->post('gender');
        $brief = $this->input->post('brief');

        //个人简介长度
        if (!$this->simplevalidate->mix_range($brief, 0, 140)) {
            $this->result['error_code'] = -200009;
            return;
        }

        $user = array(
            'id' => $this->user['id'],
            'gender' => $gender,
            'brief' => $brief,
        );
        $user = $this->user_model->update($user);
        if (!is_array($user)) {
            $this->result['error_code'] = -200008;
        }
        $_SESSION['user'] = $user;
    }

    /**
     * 修改密码
     */
    public function reset_pwd()
    {
        $pwd = $this->input->post('pwd');
        $new_pwd = $this->input->post('new_pwd');

        //验证原密码长度
        if (strlen($pwd) < 6 || strlen($pwd) > 16) {
            $this->result['error_code'] = -200214;
            return;
        }

        //验证新密码长度
        if (strlen($new_pwd) < 6 || strlen($new_pwd) > 16) {
            $this->result['error_code'] = -200215;
            return;
        }

        //验证码验证
        if (!$this->geetest->verify('pc')) {
            $this->result['error_code'] = -200148;
            return;
        }

        $pwd = md5($pwd . $this->config->config['salt']);
        $new_pwd = md5($new_pwd . $this->config->config['salt']);

        //验证原密码
        $user = $this->user_model->get_by_email_and_pwd($this->user['email'], $pwd);
        if (!is_array($user)) {
            $this->result['error_code'] = -200216;
            return;
        }

        //设置新密码
        $user = array(
            'id' => $user['id'],
            'pwd' => $new_pwd,
        );
        $this->user_model->update($user);
    }

    /**
     * 解除绑定关联账号
     */
    public function unbind()
    {
        //如果用户未绑定邮箱则不能解除绑定
        $user = $this->user_model->get($this->user['id']);
        if (empty($user['email'])) {
            $this->result['error_code'] = -200183;
            return;
        }

        $ref = $this->input->post('ref');
        switch ($ref) {
            case 'github':{
                    $success = $this->githubuser_model->del_by_userId($this->user['id']);
                    break;
                }
            case 'weixin':{
                    $success = $this->weixinuser_model->del_by_userId($this->user['id']);
                    break;
                }
            case 'qc':{
                    $success = $this->qcuser_model->del_by_userId($this->user['id']);
                    break;
                }
            case 'weibo':{
                    $success = $this->weibouser_model->del_by_userId($this->user['id']);
                    break;
                }
        }
        if (!$success) {
            $this->result['error_code'] = -200071;
        }
    }

    /**
     * 绑定邮箱
     */
    public function bind_email()
    {
        $email = $this->input->post('email');
        $pwd = $this->input->post('pwd');

        //验证邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->result['error_code'] = -200072;
            return;
        }

        //验证密码长度
        if (strlen($pwd) < 6 || strlen($pwd) > 16) {
            $this->result['error_code'] = -200077;
            return;
        }

        $user = $this->user_model->get($this->user['id']);
        //此账号已绑定了邮箱
        if (!empty($user['email'])) {
            $this->result['error_code'] = -200073;
            return;
        }

        $user = $this->user_model->get_by_email($email);
        //此邮箱已被其它账号绑定
        if (!empty($user['email'])) {
            $this->result['error_code'] = -200074;
            return;
        }

        $user = array(
            'id' => $this->user['id'],
            'email' => $email,
            'pwd' => md5($pwd . $this->config->config['salt']),
        );
        $user = $this->user_model->update($user);
        if (is_array($user)) {
            $_SESSION['user']['email'] = $email;
        }

        //发送验证邮件
        $this->send_bind_email($email);
    }

    /**
     * 重新发送验证邮件
     */
    public function resend_bind_email()
    {
        $user = $this->user_model->get($this->user['id']);
        //此账号未绑定邮箱
        if (empty($user['email'])) {
            $this->result['error_code'] = -200075;
            return;
        }

        //此账号邮箱已激活
        if ($user['email_status'] == 2) {
            $_SESSION['user']['email_status'] = 2;
            $this->result['error_code'] = -200076;
            return;
        }

        $arr = explode('@', $user['email']);
        $this->result['email_homepage'] = 'http://mail.' . $arr[1];
        //发送验证邮件
        $this->send_bind_email($user['email']);
    }

    /**
     * 发送验证邮件
     */
    private function send_bind_email($email)
    {
        $arr = explode('@', $email);
        $this->result['email_homepage'] = 'http://mail.' . $arr[1];

        $site_name = $this->config->config['site_info']['name'];
        $text = $email . '|' . time();
        //加密串
        $encrypt_code = urlencode(urlencode($this->simpleencrypt->encode($text, $this->config->config['encrypt_key'])));
        $url = base_url() . 'account/email_activate?encrypt_code=' . $encrypt_code;
        $email_content_html = '<div style="padding:25px;text-align:center;color:#513e31;background:white;border-bottom:1px solid #c3b29e;box-shadow:0 2px 4px rgba(0,0,0,.1);">
            <h3 style="margin:0;padding:0;font-size:large;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;">【' . $site_name . '】邮箱绑定确认</h3>
            <p>用户名：' . $email . '，请点击以下按钮确认绑定邮箱。</p>
            <a href="' . $url . '" style="display:block;width:200px;height:44px;border:1px solid #a37951;line-height:44px;margin:0 auto 15px;border-radius:4px;color:#513e31;text-decoration:none;text-shadow:0 1px 0 rgba(255,255,255,.5);box-shadow:0 1px 0 rgba(255,255,255,.5) inset;background:#e8d7b8;background:-webkit-gradient(linear,left top,left bottom,from(#e8d8b9),to(#d4bd95));background:-webkit-linear-gradient(top,#e8d8b9,#d4bd95);background:-moz-linear-gradient(top,#e8d8b9,#d4bd95);background:-o-linear-gradient(top,#e8d8b9,#d4bd95);background:linear-gradient(to bottom,#e8d8b9,#d4bd95);" target="_blank">确认账号</a>
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
        return $this->mailer->post($email_config);
    }
}
