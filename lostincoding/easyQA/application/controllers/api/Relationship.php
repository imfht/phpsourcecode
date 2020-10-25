<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 用户关系接口控制器
 */
class Relationship extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('relationship_model');
    }

    /**
     * 关注
     */
    public function follow()
    {
        $ruser_id = $this->input->post('ruser_id');

        $relationship = $this->relationship_model->follow($this->user['id'], $ruser_id);
        $this->result['relationship'] = $relationship;
    }

    /**
     * 取消关注
     */
    public function unfollow()
    {
        $ruser_id = $this->input->post('ruser_id');

        $relationship = $this->relationship_model->unfollow($this->user['id'], $ruser_id);
    }

}
