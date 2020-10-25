<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Think\Controller;
class EmptyController extends CommonController {
    public function index(){
        $this->error("404");
    }
}