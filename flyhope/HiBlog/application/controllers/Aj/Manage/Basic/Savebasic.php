<?php
/**
 * 保存基本信息
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Basic_SavebasicController extends Aj_AbsController {

	public function indexAction() {
		$name = Comm\Arg::post('name');
		$desc = Comm\Arg::post('desc');
		$page_count = Comm\Arg::post('page_count', FILTER_VALIDATE_INT);
		$comment_youyan = Comm\Arg::post('comment_youyan');
		$timezone = Comm\Arg::post('timezone');
		
		Model\Blog::save(array(
			'name' => $name,
			'desc' => $desc,
			'page_count' => $page_count,
			'comment_youyan' => $comment_youyan,
			'timezone' => $timezone,
		));
		
		Comm\Response::json(100000, '操作成功', null, false);
	}
	
}
