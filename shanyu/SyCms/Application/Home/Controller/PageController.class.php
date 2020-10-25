<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;

//单页
class PageController extends HomeBaseController {
    public function index($cname='') {
        if( empty($cname) || strlen($cname) >15 ) E('非法操作');
        
        $cate=D('Category')->getCategory($cname);
        if($cate['is_menu']){
            $cate=M('Category')->where("pid={$cate['id']}")->order('sort asc')->find();
            $cate['url']=U('/'.$cate['name'],'','html',true);
            if(!empty($cate['setting'])){
                $cate['setting']=unserialize($cate['setting']);
            }
        }

        $this->assign('CATEGORY',$cate);

        $this->assign('CID',$cate['id']);
        $this->assign('PID',$cate['pid']);

        //获取单页内容
        $show=M('Page')->where("cid={$cate['id']}")->find();
        $this->assign('show',$show);

        $tpl=isset($cate['template_page']) ? $cate['template_page'] : 'Page_index';
        $this->display($tpl);
    }
    	
}
