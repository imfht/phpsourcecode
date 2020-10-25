<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Mobile\Controller;
use Common\Lib\Category;
class SpecialController extends CommonController{
	/**
	 * 专题首页控制器方法
	 */
	public function index(){
		$cid = I('cid', 0,'intval');
		$this->assign('title', '专题首页');
		$this->display();

	}

	/**
	 * 测试－用户模型
	 */
	public function lists(){
		$cid = I('cid', 0,'intval');

		$cate = get_category(1);
		$self = Category::getSelf($cate, $cid);//当前栏目信息

		$patterns = array('/'.C('TMPL_TEMPLATE_SUFFIX').'$/');
		$replacements = array('');
		$template_list = preg_replace($patterns, $replacements, $self['template_list']);
		
		if (empty($template_list)) {
			$this->error('模板不存在');
		}
	
		$this->assign('title', '专题首页');
		$this->display($template_list);

	}

	/**
	 * 专题展示页控制器方法
	 */
	public function show($id = 0){
		$id = I('id', 0, 'intval');
// 		dump($id);
// 		exit;
		if ($id == 0) {
			$this->error('参数错误');
		}

		$content = D('special')->find($id);
// 				dump($content);
// 				exit;

		if (!$content) {
			$this->error('专题不存在');
		}
		$cid = $content['cid'];
		$patterns = array('/'.C('TMPL_TEMPLATE_SUFFIX').'$/');
		$replacements = array('');
		$template_show = preg_replace($patterns, $replacements, $content['special_template']);
// 		dump($template_show);
// 		exit;
		if (empty($template_show)) {
			$this->error('模板不存在');
		}

		$this->assign('title', $content['special_title']);
		$this->assign('keywords', $content['special_keywords']);
		$this->assign('description', $content['special_description']);
		$this->display($template_show);
	}

}

?>