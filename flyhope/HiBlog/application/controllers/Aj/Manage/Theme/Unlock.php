<?php
/**
 * 模板解锁
 * 
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_UnlockController extends Aj_AbsController {

	//控制器入口
	public function indexAction() {
		$id = Comm\Arg::post('id', FILTER_VALIDATE_INT);
		$result = Model\Theme\Resource::unlock($id);
		Comm\Response::json(100000, 'succ', null, false);
	}

}