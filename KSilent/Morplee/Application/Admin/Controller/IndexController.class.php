<?php
namespace Admin\Controller;
use User\Api\UserApi as UserApi;

class IndexController extends AdminController {
    
    public function index(){
    	$this->meta_title = '管理首页';
        $this->display();
    }
}