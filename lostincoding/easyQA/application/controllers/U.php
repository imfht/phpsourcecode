<?php
defined('BASEPATH') or exit('No direct script access allowed');

class U extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('article_model');
        $this->load->model('comment_model');
        $this->load->model('articlefavorite_model');
        $this->load->model('msg_model');
        $this->load->model('skinsetting_model');
        $this->load->model('githubuser_model');
        $this->load->model('weixinuser_model');
        $this->load->model('qcuser_model');
        $this->load->model('weibouser_model');
        $this->load->model('oschinauser_model');
        $this->data['active'] = 'u';
    }

    /**
     * 我的后台首页
     */
    public function index($page_index = 1, $page_size = 50)
    {
        $this->is_signin();

        $this->data['article_lists'] = $this->article_model->gets_by_userId(0, $this->user['id'], $page_index, $page_size);
        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        //分页
        $config['base_url'] = base_url("u/");
        $config['total_rows'] = $this->data['article_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 2;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->data['active_nav'] = 'q';
        $this->data['title'] = '用户中心';
        $this->load->view("{$this->theme_id}/u/index", $this->data);
    }

    /**
     * 个人主页
     */
    public function home($id = null, $page_index = 1, $page_size = 50)
    {
        if (empty($id)) {
            $nickname = $this->input->get('nickname');
            $user = $this->user_model->get_by_nickname($nickname);
            $id = $user['id'];
        }

        //获取设置的皮肤
        $this->data['skin'] = $this->skinsetting_model->get_by_userId($id);

        $this->data['huser'] = $this->user_model->get($id);
        $this->data['article_lists'] = $this->article_model->gets_by_userId(0, $id, $page_index, $page_size);
        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $id);
        $this->data['comment_lists'] = $this->comment_model->gets_by_userId($id, $page_index, $page_size);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($id);
        $this->data['active_nav'] = 'home';
        $this->data['title'] = '个人首页';
        $this->load->view("{$this->theme_id}/u/home", $this->data);
    }

    /**
     * 我的评论
     */
    public function comment($page_index = 1, $page_size = 50)
    {
        $this->is_signin();

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_lists'] = $this->comment_model->gets_by_userId($this->user['id'], $page_index, $page_size);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        //分页
        $config['base_url'] = base_url("u/comment/");
        $config['total_rows'] = $this->data['comment_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->data['active_nav'] = 'comment';
        $this->data['title'] = '我的评论';
        $this->load->view("{$this->theme_id}/u/comment", $this->data);
    }

    /**
     * 我的收藏
     */
    public function favorite($page_index = 1, $page_size = 50)
    {
        $this->is_signin();

        $this->data['articlefavorite_lists'] = $this->articlefavorite_model->gets_by_userId($this->user['id'], $page_index, $page_size);
        $this->data['articlefavorite_counts'] = $this->articlefavorite_model->get_counts_by_userId($this->user['id']);

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        //分页
        $config['base_url'] = base_url("u/");
        $config['total_rows'] = $this->data['articlefavorite_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 2;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->data['active_nav'] = 'favorite';
        $this->data['title'] = '用户中心';
        $this->load->view("{$this->theme_id}/u/favorite", $this->data);
    }

    /**
     * 头像设置
     */
    public function avatar()
    {
        $this->is_signin();

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        $this->data['active_nav'] = 'avatar';
        $this->data['title'] = '头像设置';
        $this->load->view("{$this->theme_id}/u/avatar", $this->data);
    }

    /**
     * 个人资料
     */
    public function profile()
    {
        $this->is_signin();

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        $this->data['active_nav'] = 'profile';
        $this->data['title'] = '个人资料';
        $this->load->view("{$this->theme_id}/u/profile", $this->data);
    }

    /**
     * 我的消息
     */
    public function msg($page_index = 1, $page_size = 20)
    {
        $this->is_signin();

        $this->data['title'] = '我的消息';
        $msg_lists = $this->msg_model->gets_to_me($this->user['id'], $page_index, $page_size);
        $msg_counts = $this->msg_model->gets_to_me_count($this->user['id']);
        $this->data['msg_lists'] = $msg_lists;
        $this->data['msg_counts'] = $msg_counts;

        if (is_array($msg_lists) && $this->msg_to_me_counts > 0) {
            //将本页消息设置已读
            $this->msg_model->view_msg_by_range($this->user['id'], $msg_lists[count($msg_lists) - 1]['id'], $msg_lists[0]['id']);
        }

        //分页
        $config['base_url'] = base_url("u/msg/");
        $config['total_rows'] = $this->data['msg_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        $this->data['active_nav'] = 'msg';
        $this->load->view("{$this->theme_id}/u/msg", $this->data);
    }

    /**
     * 修改密码
     */
    public function reset_pwd()
    {
        $this->data['title'] = '修改密码';

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        $this->data['active_nav'] = 'reset_pwd';
        $this->load->view("{$this->theme_id}/u/reset_pwd", $this->data);
    }

    /**
     * 绑定/解除绑定社交账号
     */
    public function bind()
    {
        $this->data['title'] = '账号关联';

        //oschina
        $this->data['oschina_user'] = $this->oschinauser_model->get_by_userId($this->user['id']);
        //github
        $this->data['github_user'] = $this->githubuser_model->get_by_userId($this->user['id']);
        //weixin
        $this->data['weixin_user'] = $this->weixinuser_model->get_by_userId($this->user['id']);
        //qq
        $this->data['qc_user'] = $this->qcuser_model->get_by_userId($this->user['id']);
        //weibo
        $this->data['weibo_user'] = $this->weibouser_model->get_by_userId($this->user['id']);

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        $this->data['active_nav'] = 'bind';
        $this->load->view("{$this->theme_id}/u/bind", $this->data);
    }

    /**
     * 认证
     */
    public function verify()
    {
        $this->data['title'] = '用户认证';

        $this->data['article_counts'] = $this->article_model->get_counts_by_userId(0, $this->user['id']);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_userId($this->user['id']);

        $this->data['active_nav'] = 'verify';
        $this->load->view("{$this->theme_id}/u/verify", $this->data);
    }

    /**
     * 下载站长认证的验证文件
     */
    public function download_website_verify_file()
    {
        $domain = $this->input->get('domain');

        if (empty($domain) || !$this->simplevalidate->domain($domain)) {
            echo 'give me a right domain, please.';
            return;
        }

        $domain_md5 = md5($domain);
        $filename = $domain_md5 . '.txt';
        header("Content-Type: application/octet-stream");
        if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition:  attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: attachment; filename*="utf8' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        echo $domain_md5;
    }
}
