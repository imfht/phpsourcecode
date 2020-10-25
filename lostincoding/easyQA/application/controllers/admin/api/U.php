<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 用户管理接口控制器
 */
class U extends AdminAPI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    /**
     * 冻结用户账号
     */
    public function freeze()
    {
        $user_id = $this->input->post('user_id');
        $freeze_status = $this->input->post('freeze_status');

        $freeze_status = $freeze_status == 1 ? 2 : 1;

        $user = array(
            'id' => $user_id,
            'freeze_status' => $freeze_status,
        );
        $this->user_model->update($user);
    }
}
