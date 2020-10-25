<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 文章APP基础控制器
 */
namespace Article\Controller;
use Common\Controller\FrontendController;

class ArticleBaseController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		//生成导航
		$this->assign('arrNav',$this->_pagenav());
	}
	/*
	 * 配置成后台可以更新的导航 暂时先这样
	 * */
	protected  function _pagenav(){
		// 文章导航
	    $arrChannel = D('ArticleChannel')->getAllChannel(array('isnav'=>'0'));
	    $arrNav = array();
	    if($arrChannel){
	    	foreach($arrChannel as $item){
	    		$arrNav[$item['nameid']] = array('name'=>$item['name'], 'url'=>U('article/channel/index',array('nameid'=>$item['nameid'])));
	    	}
	    }
	    //下版本开发
/*	    if($this->visitor['userid']){
	    	$arrNav['my_article'] = array('name'=>'我的文章', 'url'=>U('article/index/my_article'));
	    }*/
		return $arrNav;
	}
			
}