<?php
//版权所有(C) 2014 www.ilinei.com

namespace tpl\_cms\control;

use cms\model\_article;
use cms\model\_category;
use wx\model\_wx_setting;
use ilinei\browser;

//入口
class index{
	//默认
	public function index(){
		global $_var, $db, $setting;
		
		$ac = empty($_var['gp__c']) || !is_ansi($_var['gp__c']) ? $setting['SiteIndex'] : $_var['gp__c'];
		
		!is_file(ROOTPATH."/tpl/_cms/control/pc_{$ac}.php") && exit_html5('Access Denied');
		
		$db->connect();
		
		$browser = new browser($_SERVER['HTTP_USER_AGENT']);
		$ismobile = $browser->isMobile();
		
		if($_SESSION['_PREVIEW'] || $_var['gp__PREVIEW']){
			$ismobile = true;
			!$_SESSION['_PREVIEW'] && $_SESSION['_PREVIEW'] = $_var['gp__PREVIEW'];
		}
		
		$_article = new _article();
		$_category = new _category();
		$_wx_setting = new _wx_setting();
		
		//加载微信参数
		$wx_setting = cache_read('wx_setting');
		if(!$wx_setting){
			$wx_setting = $_wx_setting->get();
			$wx_setting = $_wx_setting->format($wx_setting);
			
			cache_write('wx_setting', $wx_setting);
		}
		
		//文章分类缓存
		$categories = cache_read('category');
		if(!is_array($categories)){
			$categories = $_category->get_all(0, 'article');
			cache_write('category', $categories);
		}
		//导航缓存
		$navs = cache_read('nav');
		if(!is_array($navs)){
			$navs = $_category->get_tree(0, 'nav');
			cache_write('nav', $navs);
		}

        include_once ROOTPATH."/tpl/_cms/control/pc_{$ac}.php";
	}

	public function show_message($message, $url_forward = ''){
        tshow_message($message, $url_forward, 'view/pc_show_message');
    }
    
}
?>