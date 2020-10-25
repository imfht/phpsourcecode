<?php
namespace Home\Controller;
use OT\DataDictionary;

/**
 * 
 * 主要获聚合数据
 */
class NewsController extends HomeController {

	//系统首页
    public function index(){
    	$title= '信息资讯';
        $category = D('Category')->getTree();
        $lists    = D('Document')->lists(null);
        $this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE'); 
    	$this->assign('cate',$category);//栏目
        $this->assign('lists',$lists);//列表
        $this->assign('page',D('Document')->page);//分页
    	//dump($category);              
        $this->display();
    }

}