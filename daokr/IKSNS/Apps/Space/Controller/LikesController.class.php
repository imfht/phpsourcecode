<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 个人空间 日记
 */
namespace Space\Controller;

class LikesController extends SpaceBaseController {
	public function _initialize() {
		parent::_initialize ();
		if (is_login()) {
			$this->userid = $this->visitor['userid'];
		}
	}
	public function index($id){
		$this->error("喜欢收藏的还在改版中；请等待");
	}
}