<?php
namespace Home\Controller;

class IndexController extends HomeController {

    public function index() {
        $where['template'] = 1;
        $where['position'] = 1;
        $list = $this->lists(D('Ad'),$where);
        $this->assign('banner',$list);
        $this->setKeyWords(C('WEB_SITE_KEYWORD'));
        $this->assign('site_description',C('WEB_SITE_DESCRIPTION'));
        /* 获取当前分类列表 */
        $Document = D('Document');
        $where = array('status'=>1);
        $list= $this->lists($Document,$where,'create_time DESC');

        $this->assign('list',$list);
        $this->show();
    }
    public function ajaxNews(){
        $Document = D('Document');
        $where = array('status'=>1);        
        $list= $this->lists($Document,$where,'create_time DESC');    
        $this->assign('_list',$list);
        $result['p']=I('get.p')+1;
	    $result['content']=$this->fetch();
	    $result['errno']=0;
	    $this->ajaxReturn($result);        
    }
}