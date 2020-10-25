<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 消息接口控制器
 */
class Msg extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('msg_model');
    }

    /**
     * 删除消息
     * 现在的设计就是发送人不存储消息（因为都是系统消息），只存储接收人消息，所以只有接收人可以删除消息
     */
    public function del()
    {
        $id = $this->input->post('id');

        $msg = $this->msg_model->get($id);
        //消息不存在
        if (!is_array($msg)) {
            $this->result['error_code'] = -200022;
            return;
        }

        //消息不属于此人，无权删除
        if ($msg['receiver_user_id'] != $this->user['id']) {
            $this->result['error_code'] = -200023;
            return;
        }

        $this->msg_model->del($id);
    }
}
