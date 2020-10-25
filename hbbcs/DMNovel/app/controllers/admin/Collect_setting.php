<?php

/**
 * Description of Capture
 *
 * @author joe
 */
class Collect_setting extends CI_Controller {

    public $style,$user;

    function __construct() {
        parent::__construct();
        $this->load->model('category_model', 'category');
        $this->load->model('collect_model', 'collect');
        $this->load->model('story_model', 'story');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
        $this->user = $this->session->DMN_USER;
        if ($this->user['level'] <7 ) {
            show_error('您没有权限查看此内容。');
        }
    }

    function index() {
        $data['collects'] = $this->collect->get(null, 'id,site_title,site_url');
        $data['style']    = $this->style;
        $this->load->view('admin/collect/list', $data);
    }

    function get($story_id = null) {
        if ($story_id) {
            $data['collect_book'] = $this->db->get_where('collect_book', ['story_id' => $story_id])->row_array();
        }
        $data['categories'] = $this->category->get();
        $data['collects']   = $this->collect->get(null, 'id,site_title,site_url');
        $this->load->view('admin/collect/get', $data);
    }

    function test($id = null) {
        $book_id = null;
        $ajax    = 0;
        if (!$id) {
            $id          = $this->input->post('collect_id');
            $book_id     = $this->input->post('book_id');
            $category_id = $this->input->post('category_id');
        } else {
            $ajax = 1;
        }

        $book         = $this->collect->getBookInfo($id, $book_id);
        $chapter_list = $this->collect->getChapterList();
        $chapter      = $this->collect->getChapter($book['chapter_url'] . $chapter_list[0]['url']);

        $data['book']         = $book;
        $data['chapter_list'] = $chapter_list;
        $data['chapter']      = $chapter ? substr($chapter, 0, 500) : "没有抓取到内容......";
        $data['ajax']         = $ajax;
        $data['collect_id']   = $id;
        $data['book_id']      = $book_id;
        $data['category_id']  = $category_id;
        $data['style']        = $this->style;

        if ($ajax == 0) {
            $this->load->view('admin/iframe_header');
        }
        $this->load->view('admin/collect/test', $data);
    }


    function edit($id = null) {
        $data = [];
        if ($id) {
            $data['collect'] = $this->collect->get($id);
        }
        $this->load->view('admin/collect/add', $data);
    }

    function delete($id) {
        if (!$id) show_error('没有要删除的ID');

        $this->db->delete('collect', ['id' => $id]);
    }

    function add() {
        $collect = [
            'id'              => $this->input->post('id'),
            'site_title'      => $this->input->post('site_title'),
            'site_url'        => $this->input->post('site_url'),
            'book_url'        => $this->input->post('book_url'),
            'book_title'      => $this->input->post('book_title'),
            'book_author'     => $this->input->post('book_author'),
            'book_desc'       => $this->input->post('book_desc'),
            'book_img'        => $this->input->post('book_img'),
            'book_list'       => $this->input->post('book_list'),
            'chapter_list'    => $this->input->post('chapter_list'),
            'chapter_url'     => $this->input->post('chapter_url'),
            'chapter_content' => $this->input->post('chapter_content'),
            'test_id'         => $this->input->post('test_id')
        ];

        $this->db->replace('collect', $collect);
        redirect('admin/collect_setting');
    }

}
