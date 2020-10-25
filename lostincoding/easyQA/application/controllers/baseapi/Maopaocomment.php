<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 冒泡评论接口控制器
 */
class Maopaocomment extends BaseAPI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('maopaocomment_model');
    }

    /**
     * 获取对话列表
     */
    public function dialog_lists()
    {
        $dialog_id = $this->input->get('dialog_id');
        $page_index = $this->input->get('page_index');
        $page_size = $this->input->get('page_size');

        $user_id = isset($this->user['id']) ? $this->user['id'] : null;

        $dialog_lists = $this->maopaocomment_model->get_dialogs($dialog_id, $user_id, $page_index, $page_size);
        $this->result['dialog_lists'] = $dialog_lists;
    }
}
