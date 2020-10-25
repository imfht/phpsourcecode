<?php
namespace Admin\Controller;
class IndexController extends CommonController {

    // 框架首页
    public function index() {
    	
    	$views=M('article')->where(array('status'=>1))->sum('view');
    	
    	$this->assign ( 'views', $views);
    	$artcount=M('article')->where(array('status'=>1))->count();
    	 
    	$this->assign ( 'artcount', $artcount);
    	$usercount=M('member')->where(array('status'=>1))->count();
    	$this->assign ( 'usercount', $usercount);
    	
    	
    	
       $syslog=D('syslogs')->order('id desc')->limit(10)->select();
        
        $this->assign ( 'syslog', $syslog);
       
     
        $this->display();
    }

}

?>