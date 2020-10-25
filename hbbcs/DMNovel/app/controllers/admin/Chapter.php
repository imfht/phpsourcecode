<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-14
 * Time: 上午11:01
 */
class Chapter extends CI_Controller {

    public $style,$user;

    function __construct() {
        parent::__construct();
        $this->load->model('story_model', 'story');
        $this->load->model('chapter_model', 'chapter');
        $this->style = get_cookie('style')?'bootstrap/'.get_cookie('style'):'bootstrap.min';
        $this->user = $this->session->DMN_USER;
        if ($this->user['level'] <7 ) {
            show_error('您没有权限查看此内容。');
        }
    }

    function index($story_id = null, $chapter_id = null) {
        $data['order']=0;
        if ($story_id) {
            $data['story'] = $this->story->get($story_id);
            if ($chapter_id) {
                $data['chapter'] = $this->chapter->get($chapter_id);
                $this->load->view('admin/chapter', $data);
                return;
            }
            $data['order']=$this->chapter->all($story_id);
        } else {
            if ($this->user['level']!=9) {
                $this->db->where('user_id',$this->user['id']);
            }
            $data['storys'] = $this->story->get();
        }

        $data['style'] = $this->style;

        $this->load->view('admin/chapter', $data);
    }

    function add() {
        $type    = $this->input->post('type');
        $chapter = array(
            'id'       => $this->input->post('id'),
            'title'    => $this->input->post('title'),
            'content'  => $this->chapter->filter($this->input->post('content')),
            'story_id' => $this->input->post('story_id'),
            'order'    => $this->input->post('order')
        );

        if (!$chapter['title']) show_error('章节标题未填写，请检查后重新提交。');

        $story = $this->story->get($chapter['story_id'],'id');

        if (!$story) show_error('您所提交的小说不存在，请检查后重新提交。');

        if (!$chapter['id']) {
            $this->chapter->insert($chapter);
        } else {
            $this->chapter->update($chapter);
        }
        $this->db->cache_delete('admin', 'chapter');
        $this->db->cache_delete('chapter', $chapter['id']);

        $this->db->set('last_update',date('Y-m-d H:i:s'))->where('id',$chapter['story_id'])->update('story');
        show_json(['url'=>'/admin/chapter/' . $type . '/' . $story['id']]);
    }

    function delete($id=null) {
        if (!$id) show_error('没有选择要删除的章节');

        $this->db->delete('chapter',array('id'=>$id));
    }

    function list_chapter($story_id=null) {
        if (!$story_id) show_error('没有选择小说，请从发布小说中查看章节列表');

        $data['style']     = $this->style;
        $data['story']    = $this->story->get($story_id);
        $data['style']     = $this->style;
        $this->load->view('admin/chapter_list', $data);
    }

    function datatable($story_id) {
        $search = $this->input->get_post('search');

        $where='story_id='.$story_id;

        if ($search['value']) {
            $where.=' AND (id='.$search['value'].' OR title like "%'.$search['value'].'%")';
        }

        $this->load->library('Datatables');
        $this->datatables->select("id,`title`,`order`", false)
            ->from('chapter')
            ->where($where)
            ->add_column('DT_RowId', '$1', 'id')
            ->add_column('action', <<<ETO
            <div class="btn-group btn-group-sm">

                                <a href="#"  class="btn btn-default editChapter" title="编辑章节">
                                    <i class="icon-edit"></i>
                                </a>
                                <a href="#" class="btn btn-default deleteChapter" title="删除章节">
                                    <i class="icon-trash"></i>
                                </a>
                    </div>

ETO
            );

        echo $this->datatables->generate();
    }

}