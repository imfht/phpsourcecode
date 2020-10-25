<?php
namespace app\controller;

use app\BaseController;
use think\facade\Session;

class Common extends BaseController 
{
    public function initialize()
    {
        parent::initialize();

        $admin_id = Session::get('admin_id');

        if(empty($admin_id)){
            return $this->error('请登录','Login/index');
        }
    }
}