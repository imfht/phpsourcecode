<?php
/**
 * 一开始写的代码 有些混乱 暂不改造 2014-05-08  
 * 
 * 
 */
class CateadminController extends AdminController {
	/**
	 * json格式
	 * 默认1为展开状态
	 */
	public function ztree() {
		echo self::getCateService ()->ztree ( $_POST ['id'] );
	
	}

	/**
	 * 获取分类信息
	 * 
	 */
	public function category() {
		$rs = self::getCateService ()->getCateById ( $_POST ['cid']);
		$this->sendNotice ( $rs );
	}

	
	public function edit() {
		
		$this->view ()->display ( 'file:cate/edit.html' );
	}
	
	public function move() {
		$rs = self::getCateService ()->move ( $_POST ['id'], $_POST ['fid'] );
		$this->sendNotice ( $rs );
	}
	
	public function rename() {
		
		$rs = self::getCateService ()->saveCategoryNameById ( $_POST ['name'], $_POST ['category'] );
		$this->sendNotice ( "SUCCESS",null,true );
	}
	
	//删除分类时，会把分类下的所有文章删除掉
	public function remove() {
        self::getCateService()->deleteCatetgoryById($_POST['id']);
        $this->sendNotice ( "SUCCESS",null,true );
	}
	
	public function add() {
		$rs = self::getCateService ()->addCate($_POST);
		$this->sendNotice ( "SUCCESS",null, $rs );
	}
	

	public static function getCateService() {
		return new CateService ();
	}
}