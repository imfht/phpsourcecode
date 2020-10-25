<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
class IndexController extends HomeBaseController {

    public function index(){
        $this->assign('CID',0);
    	$this->display();
    }
       


}