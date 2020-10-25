<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;

class FrontController extends BaseController {

	protected function _initialize(){
		parent::_initialize();

		if (C('WEB_SITE_CLOSE')) {
			$this->error('站点已经关闭，请稍后访问~');
		}
		if (is_mobile() && C('OPEN_MOBILE_SITE')) {
			C('DEFAULT_THEME',C('DEFAULT_THEME')."_m");
		}

		if (!cookie('think_language') && C('LANG_SWITCH_ON')) {
			cookie('think_language',C('DEFAULT_LANG'));
		}elseif(cookie('think_language') && !in_array(cookie('think_language'), explode(',', C('LANG_LIST')))){
			cookie('think_language',C('DEFAULT_LANG'));
		}
	}
	
	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}
}