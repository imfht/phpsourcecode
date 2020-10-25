<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class DynamicController extends UserController {
    /**
	* 用户动态
	*/    
    public function index($uid=0){
    	$id = $uid? intval($uid):UID;
    	if($id ){
    		$list = get_user_dynamic($id);
		    $total        =  count($list);//获取总数
		    $listRows = 20;
	        $page = new \Think\Page($total, $listRows);
	        $page->rollPage = 3;
	        if($total>$listRows){
	            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
	            $page->setConfig('prev', '<');
	        	$page->setConfig('next', '>');
	        }
	        $p =$page->show();
	        $this->assign('_page', $p? $p: '');
	        $this->assign('_total',$total);
	        $this->assign('list',array_slice($list,$page->firstRow,$page->listRows));   		
    		$this->display();   	
    	
    	}else{
    		$this->error('非法访问！');
    	}

    }    
}