<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Home\Controller;
use Think\Controller;
use Common\Lib\String; //引入类函数
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
class IndexController extends CommonController {
	/**
	 * 网站首页控制器
	 */
    public function index(){
    	//**引入字符截取函数
    	import('Class.String',APP_PATH);//文件在当前项目目录下的class目录
    	//**引入栏目关系函数
    	import('Class.Category', APP_PATH);
    	
    	//****SEO信息
    	$m=M('Config');
    	$data=$m->field('config_webtitle,config_webkw,config_cp,config_company')->find();
//     	dump($data);
//     	exit;
    	$title=$data['config_webtitle'];
    	$keywords=$data['config_webkw'];
    	$description=$data['config_cp'];
    	$config_company=$data['config_company'];
    	 
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	$this->assign('config_company',$config_company);

		$this->display();
    }
}