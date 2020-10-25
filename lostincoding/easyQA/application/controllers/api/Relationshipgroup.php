<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 用户关系分组接口控制器
 */
class Relationshipgroup extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('relationshipgroup_model');
    }

}
