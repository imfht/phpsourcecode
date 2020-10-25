<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 话题管理接口控制器
 */
class Topic extends AdminAPI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('topic_model');
    }

    /**
     * 删除
     */
    public function del()
    {
        $topic_id = $this->input->post('topic_id');

        //删除话题(不会删除此话题的文章，需要删除文章直接到文章管理里删除)
        $this->topic_model->del($topic_id);
    }
}
