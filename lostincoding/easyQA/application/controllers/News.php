<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 头条控制器
 */
class News extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
        $this->load->model('articleappend_model');
        $this->load->model('articletopic_model');
        $this->load->model('comment_model');
        $this->data['active'] = 'news';
    }

    public function index($type = 'latest', $page_index = 1, $page_size = 50)
    {
        if (!in_array($type, array('latest', 'fine'))) {
            redirect();
        }

        if ($type == 'latest') {
            $this->data['title'] = '最新头条';
            $this->data['article_lists'] = $this->article_model->gets_by_latest(3, $page_index, $page_size);
            $this->data['article_counts'] = $this->article_model->get_counts(3);
        } else if ($type == 'fine') {
            $this->data['title'] = '精帖头条';
            $this->data['article_lists'] = $this->article_model->gets_by_fine(3, $page_index, $page_size);
            $this->data['article_counts'] = $this->article_model->get_counts_by_fine(3);
        }

        //分页
        $config['base_url'] = base_url("news/{$type}/");
        $config['total_rows'] = $this->data['article_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->data['q_by_view_hot_lists'] = $this->article_model->gets_by_hot(1, 0, 7, 1, 10);
        $this->data['q_by_comment_hot_lists'] = $this->article_model->gets_by_hot(1, 1, 7, 1, 10);
        $this->data['comment_top_user_lists'] = $this->comment_model->get_top_users(1, 12);

        $this->data['active_nav'] = $type;
        $this->load->view("{$this->theme_id}/article/index", $this->data);
    }

    public function detail($id, $page_index = 1, $page_size = 50)
    {
        //提问阅读量增加1
        $this->article_model->add_view_counts($id);
        $article = $this->article_model->get($id, true, $this->user['id']);

        if (!is_array($article)) {
            redirect('errorpage/404');
        }

        //获取追加内容
        $this->data['article_append_lists'] = $this->articleappend_model->gets_by_articleId($article['id']);

        $user_id = isset($this->user['id']) ? $this->user['id'] : null;
        $this->data['comment_lists'] = $this->comment_model->gets_by_articleId($id, $page_index, $page_size, $user_id);
        $this->data['comment_counts'] = $this->comment_model->get_counts_by_articleId($id);
        $this->data['q_by_view_hot_lists'] = $this->article_model->gets_by_hot(1, 0, 7, 1, 10);
        $this->data['q_by_comment_hot_lists'] = $this->article_model->gets_by_hot(1, 1, 7, 1, 10);

        //获取keywords与description
        $article_topics = $this->articletopic_model->gets_by_articleId($id);
        if (is_array($article_topics)) {
            $article_topics_arr = array();
            foreach ($article_topics as $_v) {
                $article_topics_arr[] = $_v['topic'];
            }
            $article_topics_str = implode(' ', $article_topics_arr);
            $this->data['keywords'] = $article_topics_str;
        }
        $this->data['description'] = fetch_description($article['article_content']);

        $this->data['title'] = "#{$id} {$article['article_title']}";
        $this->data['article'] = $article;
        $this->load->view("{$this->theme_id}/article/detail", $this->data);
    }
}
