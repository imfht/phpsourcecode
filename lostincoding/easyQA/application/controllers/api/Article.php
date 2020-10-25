<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 文章接口控制器
 */
class Article extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
        $this->load->model('articleappend_model');
        $this->load->model('topic_model');
        $this->load->model('articletopic_model');
        $this->load->model('usertopic_model');
        $this->load->model('articlefavorite_model');
    }

    /**
     * 添加文章
     */
    public function add()
    {
        $article_type = $this->input->post('article_type');
        $article_title = $this->input->post('article_title');
        $article_content = $this->input->post('article_content');
        $article_topics = $this->input->post('article_topics');

        //必须有标题
        if (!$this->simplevalidate->required($article_title)) {
            $this->result['error_code'] = -200010;
            return;
        }

        //标题长度
        if (!$this->simplevalidate->mix_range($article_title, 10, 100)) {
            $this->result['error_code'] = -200011;
            return;
        }

        //必须有内容
        if (!$this->simplevalidate->required($article_content)) {
            $this->result['error_code'] = -200012;
            return;
        }

        //内容长度
        if (!$this->simplevalidate->mix_range($article_content, 10, 10000)) {
            $this->result['error_code'] = -200013;
            return;
        }

        //标签(话题)
        $topics_str = $this->get_topics_str($article_topics);
        $article_content .= "\n" . $topics_str;

        $article = array(
            'article_type' => $article_type,
            'article_title' => $article_title,
            'article_content' => $article_content,
            'user_id' => $this->user['id'],
        );
        if ($article_type == 1) {
            $article['article_status'] = 1;
        }

        $article = $this->article_model->add($article);
        if (is_array($article)) {
            $this->result['article'] = $article;

            //添加话题
            $topic_lists = fetch_topic_lists($article_content);
            if (is_array($topic_lists)) {
                foreach ($topic_lists as $_topic) {
                    $topic = array(
                        'topic' => $_topic,
                    );
                    $topic = $this->topic_model->update($topic);
                    if (is_array($topic)) {
                        $article_topic = array(
                            'article_id' => $article['id'],
                            'topic_id' => $topic['id'],
                        );
                        $this->articletopic_model->add($article_topic);
                        $user_topic = array(
                            'user_id' => $this->user['id'],
                            'topic_id' => $topic['id'],
                        );
                        $this->usertopic_model->update($user_topic);
                    }
                }
            }
        } else {
            $this->result['error_code'] = -200006;
        }
    }

    /**
     * 追加文章
     */
    public function append()
    {
        $article_id = $this->input->post('article_id');
        $append_content = $this->input->post('append_content');

        //必须有内容
        if (!$this->simplevalidate->required($append_content)) {
            $this->result['error_code'] = -200012;
            return;
        }

        //内容长度
        if (!$this->simplevalidate->mix_range($append_content, 10, 10000)) {
            $this->result['error_code'] = -200013;
            return;
        }

        //验证是否发帖人本人操作
        $article = $this->article_model->get($article_id);
        if ($article['user_id'] != $this->user['id']) {
            $this->result['error_code'] = -200031;
            return;
        }

        $article_append = array(
            'article_id' => $article_id,
            'append_content' => $append_content,
        );

        $this->articleappend_model->add($article_append);
    }

    /**
     * 收藏文章
     */
    public function favorite()
    {
        $article_id = $this->input->post('article_id');

        $favorite = $this->articlefavorite_model->get_by_userId_and_articleId($this->user['id'], $article_id);
        //已收藏则做取消收藏操作
        if (is_array($favorite)) {
            $this->articlefavorite_model->del($favorite['id']);
        }
        //未收藏则做收藏操作
        else {
            $favorite = array(
                'user_id' => $this->user['id'],
                'article_id' => $article_id,
            );
            $favorite = $this->articlefavorite_model->add($favorite);
        }
    }

    /**
     * 将标签转化为#话题#
     */
    private function get_topics_str($article_topics)
    {
        $topics_str = '';
        if (!empty($article_topics)) {
            $article_topics = trim($article_topics);
        } else {
            $article_topics = '';
        }
        if (!empty($article_topics)) {
            $topics_arr = array();
            $article_topics_arr = preg_split('/[\s,]+/', $article_topics);
            $article_topics_arr = array_slice($article_topics_arr, 0, 5);
            foreach ($article_topics_arr as $_v) {
                //标签太长了,直接跳过
                if (mix_strlen($_v) > 20) {
                    continue;
                }
                $topics_arr[] = '#' . $_v . '#';
            }
            if (!empty($topics_arr)) {
                $topics_str = implode(' ', $topics_arr);
            }
        }
        return $topics_str;
    }
}
