<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 用户认证接口控制器
 */
class Verify extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('weibouser_model');
    }

    /**
     * 站长认证，通过验证网站根目录指定文件
     */
    public function website()
    {
        $domain = $this->input->post('domain');

        if (!$this->simplevalidate->required($domain)) {
            $this->result['error_code'] = -200025;
            return;
        }

        if (!$this->simplevalidate->domain($domain)) {
            $this->result['error_code'] = -200026;
            return;
        }

        $domain = strtolower($domain);
        $domain_md5 = md5($domain);
        //查询此域名下是否存在验证文件
        $content = $this->simplecurl->get($domain . '/' . $domain_md5 . '.txt');
        //验证成功，进行认证
        if ($content == $domain_md5) {
            $user = array(
                'id' => $this->user['id'],
                'verify_type' => 2,
                'verify_details' => $domain,
                'verify_time' => $this->now,
            );
            $user = $this->user_model->update($user);
            if (is_array($user)) {
                //设置session
                $_SESSION['user'] = $user;
                $this->data['user'] = $user;
            }
        }
        //验证失败
        else {
            $this->result['error_code'] = -200027;
        }
    }

    /**
     * 微博认证，通过绑定账号的微博认证信息
     */
    public function weibo()
    {
        //验证是否已绑定微博
        $weibo_user = $this->weibouser_model->get_by_userId($this->user['id']);
        if (!is_array($weibo_user)) {
            $this->result['error_code'] = -200028;
            return;
        }

        //获取微博信息
        $this->load->library(
            'weibo/SaeTClientV2',
            array(
                'akey' => $this->config->config['weibo']['WB_AKEY'],
                'skey' => $this->config->config['weibo']['WB_SKEY'],
                'access_token' => $weibo_user['access_token'],
            ),
            'c'
        );
        $weibo_user = $this->c->show_user_by_id($weibo_user['openid']);
        //没有成功获取到微博信息
        if (isset($weibo_user['error_code'])) {
            $this->result['error_code'] = -200029;
            $this->result['errcode'] = $weibo_user['error_code'];
            return;
        }

        //微博未通过微博认证则不能进行认证

        //进行认证
        $user = array(
            'id' => $this->user['id'],
            'verify_details' => $weibo_user['verified_reason'],
            'verify_time' => $this->now,
        );
        //微博黄V认证
        if ($weibo_user['verified_type'] == 0) {
            $user['verify_type'] = 3;
        }
        //微博蓝V认证
        else if ($weibo_user['verified_type'] >= 1 && $weibo_user['verified_type'] <= 7) {
            $user['verify_type'] = 4;
        }
        //未认证
        else {
            $this->result['error_code'] = -200030;
            return;
        }

        //绑定微博账号已通过微博认证，进行认证
        $user = $this->user_model->update($user);
        if (is_array($user)) {
            //设置session
            $_SESSION['user'] = $user;
            $this->data['user'] = $user;
        }
    }
}
