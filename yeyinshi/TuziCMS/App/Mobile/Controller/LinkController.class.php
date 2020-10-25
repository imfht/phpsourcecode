<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Mobile\Controller;
use Think\Controller;
use Common\Lib\String; //引入类函数
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
class LinkController extends Controller {
	/**
	 * 友情链接首页控制器方法
	 */
    public function index(){
    	$m=D('Link');
    	$arr=$m->where("link_show=0")->order('link_sort')->select();
    	//只显示未被删除news_dell=0的数据
    	dump($arr);
    	//exit;
    	 
    	$this->assign('vlist',$arr);
    	$this->display();
    }
    
    


}
