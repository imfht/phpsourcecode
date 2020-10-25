<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @爱客网特有标签类
 */
namespace Home\Controller;
use Common\Controller\FrontendController;

class TagController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		$this->tag_mod = D ( 'Common/Tag' );
	}
	public function add_ajax() {
		$objname = $this->_post ( 'objname' );
		$idname = $this->_post ( 'idname' );
		$objid = $this->_post ( 'objid' );
		$tags = $this->_post ( 'tags', 'strip_tags,trim' );
		$tagid = $this->tag_mod->addTag ( $objname, $idname, $objid, $tags );
	}
}