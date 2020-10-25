<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        $this->display();
    }
}