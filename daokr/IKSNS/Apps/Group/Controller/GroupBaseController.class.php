<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @小组应用 基础类
 */
namespace Group\Controller;
use Common\Controller\FrontendController;

class GroupBaseController extends FrontendController {
	
	public function _initialize() {
		parent::_initialize ();
		// 读取配置
		$this->fcache('group_setting');
				
		//生成导航
		$this->assign('arrNav',$this->_pagenav());
	}	
	
	// 覆盖父类导航
	protected  function _pagenav(){
		// 小组导航
		if($this->visitor['userid'] > 0){
			$arrNav['index'] = array('name'=>'我的小组', 'url'=>U('group/index/index'));
		}
		$arrNav['groups']       = array('name'=>'发现小组', 'url'=>U('group/explore/groups'));
		$arrNav['topics'] = array('name'=>'发现话题', 'url'=>U('group/explore/topics'));		
		return $arrNav;
	}
}