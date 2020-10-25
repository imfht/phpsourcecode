<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 皮肤接口控制器
 */
class Skin extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('skinsetting_model');
    }

    /**
     * 锁定皮肤背景(背景图不随页面滚动)
     */
    public function lock_background()
    {
        //用户没有使用皮肤
        if (empty($_SESSION['skin'])) {
            $this->result['error_code'] = -200064;
            return;
        }

        //1滚动,2不滚动
        $lock_status = $this->input->post('lock_status');
        $this->skinsetting_model->lock_background($this->user['id'], $lock_status);
        $_SESSION['skin']['lock_background'] = $lock_status;
    }
}
