<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
use Think\Controller;
class IndexController extends Controller {	
	protected function _initialize(){
		/* 读取数据库中的配置 */
    	$config = api('Config/lists');
    	C($config); //添加配置
      	define('UID',is_login()); 
      	$this->meat_title = C('WEB_SITE_TITLE');
       	$this->meat_keywords = C('WEB_SITE_KEYWORD');
       	$this->meat_description = C('WEB_SITE_DESCRIPTION');      
	}
    public function index($order='hot'){
    	if ($order== 'new'){
    		$orders = 'reg_time desc';	
    	}elseif($order== 'hot'){
    		$orders = 'fans desc';
    	}
    	
    	$model = D('Member'); 	
    	$where['status'] = 1;
    	$total        =   $model->where($where)->count();//获取总数
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
        $limit = $page->firstRow.','.$page->listRows;
        $list =  $model->where($where)->field('cdkey,last_login_time,status,last_login_ip,reg_time,reg_ip',true)->relation(true)->limit($limit)->order($orders)->select();
    	$this->assign('list',$list);
    	$this->assign('order',$order);
    	$title = '会员广场';    	
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
		$this->display(); 	
    }           
}