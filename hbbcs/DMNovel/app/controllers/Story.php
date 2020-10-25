<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-6
 * Time: 上午9:24
 */
class Story extends CI_Controller {

    public $title, $style, $user;

    function __construct() {
        parent::__construct();
        $this->load->model('story_model', 'story');
        $this->load->model('chapter_model', 'chapter');
        $this->load->model('bookmark_model', 'bookmark');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
        $this->user  = $this->session->DMN_USER;
    }

    public function index($id) {
        if (!$id) {
            show_error('请输入书号');
        }

        $this->load->model('category_model', 'category');


        $data['categories'] = $this->category->get();


        $data['story']    = $this->story->get($id);
        $data['title']    = $data['story']['title'];
        $data['chapters'] = $this->chapter->get(null, $id);

        $data['category_id'] = $data['story']['category'];
        //$data['bookmark']    = $this->db->where('story_id', $id)->from('bookmark')->count_all_results();

        $data['last_read'] = $this->input->cookie($id) ? json_decode($this->input->cookie($id), true) : '';
        $data['style']     = $this->style;
        $data['user']      = $this->session->DMN_USER;
        $this->output->cache(2*60);
        $this->load->view('story', $data);
    }

    public function mark($id) {
        if (!$id) {
            echo '没有选择要收藏的小说';
            return;
        }

        if (!$this->user) {
            echo "您还未登录，请登录后重试";
            return;
        }

        $bookmark = $this->bookmark->get(null, ['user_id' => $this->user['id'], 'story_id' => $id]);

        if ($bookmark) {
            echo "您已经收藏过此小说。";
            return;
        }

        $mark = [
            'id'       => 0,
            'user_id'  => $this->user['id'],
            'story_id' => $id
        ];
        $this->db->insert('bookmark', $mark);
        $mark = $this->db->where('story_id', $id)->from('bookmark')->count_all_results();
        $this->db->set('mark', $mark)->where('id', $id)->update('story');
    }

    function vote($id, $score = 1) {
        if (!$id) {
            echo '没有选择要收藏的小说';
        } else if (!$score) {
            echo '没有输入评分数。';
        } else if (!$this->user) {
            echo "您还未登录，请登录后重试";
        } else {
            $vote = json_decode($this->user['vote'], true);

            if ($vote && array_key_exists($id, $vote)) {
                echo '您已经对此小说投过票。';
            } else {
                $story = $this->db->select('vote,score')->where('id', $id)->get('story')->row_array();

                $vote[$id]  =  $score;
                $user_vote = json_encode($vote, JSON_UNESCAPED_UNICODE);
                $this->db->where('id', $this->user['id'])->set('vote', $user_vote)->update('users');
                $this->user['vote']      = $user_vote;
                $this->session->DMN_USER = $this->user;

                $story['vote']++;
                $story['score'] += $score;
                $story['average'] = $story['score'] / $story['vote'];

                $this->db->where('id', $id)->update('story', $story);

                echo sprintf("%.1f", $story['average']);
            }
        }
    }

}