<?php

/**
 * 附件管理
 * Class FileControl
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class FileController extends AuthController
{
    private $db;

    public function __init()
    {
        parent::__init();
        $this->db = K("Upload");
    }

    /**
     * [index 附件视图列表]
     * @return [type] [description]
     */
    public function index()
    {
        $count = $this->db->count();
        $page = new Page($count);
        $this->page = $page->show();
        $upload = $this->db->order("id desc")->limit($page->limit())->all();
        $this->assign('upload', $upload);
        $this->display();
    }

    /**
     * 删除附件
     */
    public function del()
    {
        $id = Q("id", null, "intval");
        if ($this->db->delFile($id))
        {
            $this->success("删除成功!");
        }
        $this->error($this->db->error);
    }

    /**
     * 批量删除
     */
    public function batchDel()
    {
        $ids = Q('ids');
        if ($ids && is_array($ids)) {
            foreach ($ids as $id) {
                $this->db->delFile($id);
            }
            $this->success("删除成功!");
        } else {
            $this->error('参数错误');
        }
    }

}
