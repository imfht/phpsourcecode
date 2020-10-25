<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;

//栏目列表页
class ListController extends HomeBaseController {
    //文章内页
    public function index($cname='') {
        if( empty($cname) || strlen($cname) >15 ) E('非法操作');

        $cate=D('Home/Category')->getCategory($cname);
        $this->assign($cate['setting']);
        unset($cate['setting']);

        $this->assign('CATEGORY',$cate);
        $this->assign('CID',$cate['id']);
        $this->assign('PID',$cate['pid']);

        $tpl=isset($cate['template_list']) ? $cate['template_list'] : 'List_index';
        $this->display($tpl);
    }


    	
}
