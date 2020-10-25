<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class WeixinConfigController extends HomeController {
	protected $config = array('app_type' => 'master');
	function _search_filter(&$map) {
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$map['rank_no|name'] = array('like', "%" . $keyword . "%");
		}
	}

	function get_agent_list() {
		import("Weixin.ORG.Util.Weixin");
		$weixin = new \Weixin();
		$weixin -> get_agent_list;
	}

	function del() {
		$id = $_POST['id'];
		$this -> _destory($id);
	}

	function set_app() {
		import("Weixin.ORG.Util.Weixin");
		$weixin = new \Weixin();

		$data['agentid'] = '23';
		$data['report_location_flag'] = '0';
		//$data['logo_mediaid'] = '1eG34RbvpnRdYLTPjh2SfGDE-597CaoMud_LmuJST3VMWsHkvz7O9I3u8pjFWF6YtC8ZzTQ8FVtR3mpjC7xLbrw';
		$data['name'] = 'test';
		$data['description'] = 'desc';
		$data['redirect_domain'] = 'xiaowei.smeoa.com';
		$data['isreportuser'] = '0';
		$data['isreportenter'] = '0';
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		echo($weixin -> set_app($data));

	}

	public function test() {
		if (IS_POST) {

			import("Weixin.ORG.Util.Weixin");
			$weixin = new \Weixin();

			//$data['media'] = 'Public/test.jpg;type=image;filename=test.jpg;filelength=1024;content-type=image/jpeg';

			$path = $_FILES['file']['tmp_name'];
			$file_name = $_FILES['file']['name'];
			rename($path, $path . $file_name);
			$file_size = $_FILES['file']['size'];
			$content_type = $_FILES['file']['type'];

			$data['media'] = "@{$path}{$file_name};filename={$file_name};size={$file_size};content-type={$content_type}";
			echo($weixin -> add_file($data, 23, 'file'));

			$data['mdeia'] = '@' . $path . $file_name;
			dump($data);
			echo($weixin -> add_media($data, 'file'));

		}
		$this -> display();
	}

}
?>