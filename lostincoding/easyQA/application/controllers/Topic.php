<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Topic extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('topic_model');
        $this->load->model('articletopic_model');
        $this->load->model('article_model');
        $this->load->model('comment_model');
        $this->data['active'] = 'topic';
    }

    /**
     * 话题列表
     */
    public function index($page_index = 1, $page_size = 150)
    {
        $this->data['title'] = '话题';

        $topic_lists = $this->topic_model->gets($page_index, $page_size, 0);

        $this->data['topic_lists'] = $topic_lists;
        $this->load->view("{$this->theme_id}/topic", $this->data);
    }

    /**
     * 指定话题文章列表
     */
    public function articles($topic_id = null, $page_index = 1, $page_size = 50)
    {
        $topic_name = $this->input->get('topic');
        if (!empty($topic_id)) {
            //统一转化为小写方便去重
            $topic_name = strtolower($topic_name);
            $topic = $this->topic_model->get($topic_id);
        } else if (!empty($topic_name)) {
            $topic = $this->topic_model->get_by_topic($topic_name);
        }

        $type = $this->input->get('type');
        if (empty($type)) {
            $type = 'all';
        }
        if ($type == 'all') {
            $article_type = 0;
        } else if ($type == 'q') {
            $article_type = 1;
        } else if ($type == 'discuss') {
            $article_type = 2;
        } else if ($type == 'news') {
            $article_type = 3;
        }

        $this->data['title'] = $topic_name . '话题';

        $this->data['article_lists'] = $this->articletopic_model->gets_by_topicId($topic['id'], $article_type, $page_index, $page_size, 0);
        $this->data['article_counts'] = $this->articletopic_model->get_counts_by_topicId($topic['id'], $article_type);

        $this->data['q_by_view_hot_lists'] = $this->article_model->gets_by_hot(1, 0, 7, 1, 10);
        $this->data['q_by_comment_hot_lists'] = $this->article_model->gets_by_hot(1, 1, 7, 1, 10);
        $this->data['comment_top_user_lists'] = $this->comment_model->get_top_users(1, 12);

        $this->data['active'] = $type;
        $this->data['topic'] = $topic_name;

        //分页
        $config['base_url'] = base_url("topic/{$topic['id']}/");
        $config['total_rows'] = $this->data['article_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->load->view("{$this->theme_id}/article/index", $this->data);
    }
}
