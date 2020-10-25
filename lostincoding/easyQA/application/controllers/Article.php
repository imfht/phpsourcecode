<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 文章控制器
 */
class Article extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
    }

    public function add()
    {
        if (!isset($this->user)) {
            redirect('account/signin');
        }

        $this->data['title'] = '发布';
        $this->load->view("{$this->theme_id}/article/add", $this->data);
    }

    public function edit($article_id)
    {
        if (!isset($this->user)) {
            redirect('account/signin');
        }

        $article = $this->article_model->get($article_id, true);

        //验证是否发帖人本人操作
        if ($article['user_id'] != $this->user['id']) {
            redirect();
        }

        $this->data['article'] = $article;

        $this->data['title'] = '编辑';
        $this->load->view("{$this->theme_id}/article/add", $this->data);
    }
}
