<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
class PageController extends HomeBaseController {
    //文章内页
    public function index() {
        $cid=I('get.cid',0);
        $info=M('Page')->where("cid={$cid}")->find();
        $this->assign('info',$info);
  		$this->display();

    }
    	
}
