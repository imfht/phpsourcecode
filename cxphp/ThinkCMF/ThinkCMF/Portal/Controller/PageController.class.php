<?php

namespace Portal\Controller;

use Common\Controller\HomeBaseController;

class PageController extends HomeBaseController {

	public function index() {
		$id = I('get.id');
		$content = sp_sql_page($id);
		$this->assign($content);
		$smeta = json_decode($content['smeta'], true);
		$tplname = empty($smeta['template']) ? 'page' : $smeta['template'];
		$this->display("Portal:$tplname");
	}

	public function nav_index() {
		$navcatname = "页面";
		$datas = sp_sql_pages("field:ID,post_title;");
		$navrule = array(
			"action" => "Page/index",
			"param"	 => array(
				"id" => "ID"
			),
			"label"	 => "post_title");
		echo sp_get_nav4admin($navcatname, $datas, $navrule);
	}

}
