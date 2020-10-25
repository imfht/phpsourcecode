<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-6
 * Time: 上午9:24
 */
class Chapter extends CI_Controller
{
    public $title;
    public $style;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('story_model', 'story');
        $this->load->model('chapter_model', 'chapter');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
    }

    public function index($id)
    {
        if (!$id) {
            show_error('请输入章节号');
            return;
        }

        $data['chapter']    = $this->chapter->get($id);
        $data['chapters']   = $this->chapter->get(null, $data['chapter']['story_id']);
        $data['title']      = $data['chapter']['title'];
        $data['prev_next']  = $this->chapter->get_pn($id);
        $data['story']      = $this->story->get($data['chapter']['story_id']);
        $data['user']       = $this->session->DMN_USER;
        $data['chapter_id'] = $id;

        $this->load->model('category_model', 'category');
        $data['category'] = $this->category->get($data['story']['category']);

        $chapter = json_encode(array('id' => $id, 'title' => $data['chapter']['title']));

        $this->input->set_cookie($data['story']['id'], $chapter, 360000, '', SITEPATH);
        $data['style'] = $this->style;
        $this->output->cache(24 * 60);
        $this->load->view('chapter', $data);
    }

    public function lists($id)
    {
        $chapter  = $this->chapter->get($id, 'story_id');
        $chapters = $this->chapter->get(null, $chapter['story_id']);
        show_json($chapters);
    }
}
