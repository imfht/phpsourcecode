<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-11
 * Time: 上午8:58
 */
class Story extends CI_Controller {

    public $style, $user;

    function __construct() {
        parent::__construct();
        $this->load->model('story_model', 'story');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
        $this->user  = $this->session->DMN_USER;
        if ($this->user['level'] < 7) {
            show_error('您没有权限查看此内容。');
        }
    }

    function index($category_id = null) {
        $this->load->model('category_model', 'category');

        $data['categorys']   = $this->category->get();
        $data['category_id'] = $category_id;
        $data['style']       = $this->style;

        $this->load->view('admin/story', $data);
    }

    function datatable($category_id = null) {
        $search = $this->input->get_post('search');
        $this->load->library('Datatables');
        if ($category_id) {
            $this->datatables->where('story.category', $category_id);
        }
        if ($this->user['level'] != 9) {
            $this->datatables->where('user_id', $this->user['id']);
        }
        if ($search['value']) {
            $this->db->like('story.title', $search['value'])->or_like('story.author', $search['value']);
        }
        $action = <<<ETO
<div class="btn-group btn-group-sm">

                                <a href="#"  class="btn btn-default listChapter" title="章节列表">
                                    <i class="icon-list-alt"></i>
                                </a>

                                <a href="#"  class="btn btn-default addChapter" title="增加章节">
                                    <i class="icon-plus"></i>
                                </a>
                                <a href="#"  class="btn btn-default editStory" title="编辑小说">
                                    <i class="icon-edit"></i>
                                </a>
                                <a href="#" class="btn btn-default deleteStory" title="删除小说">
                                    <i class="icon-trash"></i>
                                </a>
ETO;
        if ($this->user['level'] == 9) {
            $action .= <<<ETO
  <a href="#" class="btn btn-default updateStory" title="更新小说">
                                    <i class="icon-cloud-download"></i>

                                </a>
    <a href="#" class="btn btn-default dropdown-toggle"  data-toggle="dropdown" title="审核小说">
        <i class="icon-check"></i>
    </a>
    <ul class="dropdown-menu" role="menu">
        <li><a href="#" data-approve="1" class="approveStory"><i class="icon-ok"></i> 审核通过</a></li>
        <li><a href="#" data-approve="2" class="unApproveStory"><i class="icon-remove"></i> 审核未通过</a></li>
    </ul>
ETO;
        } else {
            $action .= <<<ETO
<a href="#" class="btn btn-default approveStory" data-approve="0" title="提交审核">
    <i class="icon-check"></i>
</a>
ETO;

        }

        $action .= '</div>';

        $this->datatables->select("story.id,category.title as category_title,story.title,author,time,story.approve,last_update", false)
            ->from('story')
            ->join('category', 'story.category=category.id', 'left')
            ->add_column('DT_RowId', '$1', 'id')
            ->add_column('action', $action);
        echo $this->datatables->generate();
    }

    function edit($id = null) {
        $this->load->model('category_model', 'category');
        $data['categorys'] = $this->category->get();
        if ($id) {
            $data['story'] = $this->story->get($id);
        }

        $this->load->view('admin/story_edit', $data);
    }

    function add() {
        $approve = ($this->user['level'] == 9) ? 1 : 0;
        $story   = array(
            'id'          => $this->input->post('id'),
            'title'       => $this->input->post('title'),
            'author'      => $this->input->post('author') ? $this->input->post('author') : $this->user['name'],
            'category'    => $this->input->post('category'),
            'image'       => $this->input->post('image'),
            'last_update' => date('Y-m-d H:i:s'),
            'desc'        => $this->input->post('desc'),
            'user_id'     => $this->user['id'],
            'approve'     => $approve
        );

        if (!$story['title']) {
            show_json(['error' => '小说标题没有输入，请返回重新填写。']);
            return;
        }

        if (!$story['id']) {
            $check = $this->story->get(null, null, null, ['title' => $story['title']]);
            if ($check) {
                show_json(['error' => '小说《<b>' . $story['title'] . '</b>》已经存在，请重新填写。']);
                return;
            }
            $story['time'] = date('Y-m-d H:i:s');
            $this->db->insert('story', $story);
            show_json(['success' => '增加小说《' . $story['title'] . '》成功']);
        } else {
            $this->db->where('id', $story['id'])->update('story', $story);
            show_json(['success' => '编辑小说《' . $story['title'] . '》成功']);
        }
        //redirect('/admin/story');
    }

    function delete($id) {
        if (!$id) show_error('没有选择书号.');

        $this->db->delete('story', array('id' => $id));
        $this->db->delete('chapter', array('story_id' => $id)); //删除章节
        $this->db->delete('collect_cache', array('story_id' => $id)); //删除更新
        //redirect('/admin/story');
    }

    function image() {
        $config['upload_path']   = 'books/' . date('Y', time());
        $config['allowed_types'] = 'jpg|png|bmp|gif|jpeg';
        $config['max_size']      = 500;
        $config['encrypt_name']  = true;
        //创建目录
        mkdirs($config['upload_path']);

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('imageUpload')) {
            show_error($this->upload->display_errors());
        } else {
            $data = array('upload_data' => $this->upload->data());

            $message = array(
                'path'    => $config['upload_path'],
                'profile' => $data['upload_data']
            );
            show_json($message);
        }
    }

    function approve($id, $approve = 1) {
        if (!$id) {
            show_error('没有选择审核的小说');
        }
        $this->db->where('id', $id)->set('approve', $approve)->update('story');
        switch ($approve) {
            case 1:
                echo '审核通过';
                break;
            case 2:
                echo '审核未通过';
                break;
            default:
                echo '已提交审核';
                break;
        }

    }

    function upload() {
        $this->load->model('chapter_model', 'chapter');
        $config['upload_path']   = './books/uploads/';
        $config['allowed_types'] = 'txt';
        $config['max_size']      = 10240;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('story')) {
            echo ($this->upload->display_errors());
            exit();
        } else {
            $data              = array('upload_data' => $this->upload->data());
            $story             = $this->story->parse_file($data["upload_data"]);
            $story['category'] = $this->input->post('category') ? $this->input->post('category') : 1;

            if ($this->story->get(null, 1, null, array('title' => $story['title']))) {
                unlink($data["upload_data"]['full_path']);
                show_json(['error'=>'《' . $story['title'] . '》小说已经存在，请不要重复上传。<br />如果是同名小说，请改名后重新上传。']);
                exit();
            }

            $chapters = $this->story->parse_chapter($data["upload_data"]['full_path']);

            $story['desc']        = $chapters['desc'];
            $story['time']        = date('Y-m-d H:i:s');
            $story['last_update'] = $story['time'];

            $this->db->insert('story', $story);
            $story_id = $this->db->insert_id();

            unset($chapters['desc']);
            $i = 0;
            foreach ($chapters as $chapter) {
                $chapter['order']    = $i;
                $chapter['story_id'] = $story_id;
                $this->chapter->insert($chapter);
                $i++;
            }

            unlink($data["upload_data"]['full_path']);
            show_json(['success'=>'上传成功']);
            //redirect('/admin/story');
        }
    }

}