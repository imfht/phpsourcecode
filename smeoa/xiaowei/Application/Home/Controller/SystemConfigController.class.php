<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;
class SystemConfigController extends HomeController {

	protected $config = array('app_type' => 'master');

	public function index() {

		//显示左侧微信菜单树
		$system_license = get_system_config('system_license');
		$this -> assign('system_license', $system_license);

		if (!empty($_GET['oid'])) {
			$where['option_id'] = $_GET['oid'];
			$_SESSION['oid'] = $_GET['oid'];
			$oid = $_SESSION['oid'];
		} else {
			$where['option_id'] = 1;
			$_SESSION['oid'] = 1;
			$oid = $_SESSION['oid'];
		}

		$where_system['data_type'] = array('eq', 'system');
		$system_data = M('SystemConfig') -> where($where_system) -> getField('code,val');
		$this -> assign('system_data', $system_data);

		if (!empty($system_license)) {

			$where_weixin['data_type'] = array('eq', 'weixin');
			$weixin_data = M('SystemConfig') -> where($where_weixin) -> getField('code,val');
			$this -> assign('weixin_data', $weixin_data);

			$where_push['data_type'] = array('eq', 'push');
			$push_data = M('SystemConfig') -> where($where_push) -> getField('code,val');
			$this -> assign('push_data', $push_data);

			//微信菜单设置
			$weixin_option = M('WeixinOption') -> getField('id,name');
			$this -> assign('weixin_option', $weixin_option);

			$weixin_option_id = I('weixin_option');
			if (empty($weixin_option_id)) {
				$weixin_option_id = $_SESSION['oid'];
			} else {
				$_SESSION['oid'] = $weixin_option_id;
			}
			$this -> assign('weixin_option_id', $weixin_option_id);
			$where_weixin_menu['option_id'] = $weixin_option_id;

			$weixin_menu = M("WeixinMenu") -> where($where_weixin_menu) -> field('id,pid,name,key,sort,type') -> order('sort ASC') -> select();
			$this -> assign('weixin_menu_list', $weixin_menu);
			$tree = list_to_tree($weixin_menu);

			$this -> assign('weixin_menu', popup_tree_menu($tree));

		}
		//其他部分
		if (!empty($_POST['eq_pid'])) {
			$eq_pid = $_POST['eq_pid'];
		} else {
			$eq_pid = "#";
		}
		$this -> assign('eq_pid', $eq_pid);

		$node = M("SystemConfig");
		$where_system_group['data_type'] = array('eq', 'common');
		$where_system_group['pid'] = array('eq', 0);

		$list = $node -> where($where_system_group) -> order('sort asc') -> getField('id,name');
		$this -> assign('group_list', $list);

		$menu = array();
		$where_common['data_type'] = array('eq', 'common');
		$menu = M("SystemConfig") -> where($where_common) -> field('id,pid,name,is_del') -> order('sort ASC') -> select();

		if ($eq_pid != "#") {
			$tree = list_to_tree($menu, $eq_pid);
		} else {
			$tree = list_to_tree($menu);
		}

		$this -> assign('menu', popup_tree_menu($tree));

		$model = M("SystemConfig");
		$where_system_config['data_type'] = array('eq', 'common');
		$list = $model -> where($where_system_config) -> order('sort asc') -> getField('id,name');
		$this -> assign('system_config_list', $list);

		$this -> display();
	}

	function save() {
		//data_type 划分：
		$data_type = I('data_type');
		if ($data_type == 'system') {

			$this -> set_val('system_name', 'system');
			$this -> set_val('system_license', 'system');
			$this -> set_val('upload_file_ext', 'system');
			$this -> set_val('system_name', 'system');
			$this -> set_val('login_verify_code', 'system');
			$this -> success('保存成功');
			die ;
		}
		if ($data_type == 'weixin') {
			$this -> set_val('weixin_corp_id', 'weixin');
			$this -> set_val('weixin_secret', 'weixin');
			$this -> set_val('weixin_token', 'weixin');
			$this -> set_val('weixin_encoding_aes_key', 'weixin');
			$this -> set_val('weixin_site_url', 'weixin');
			$this -> success('保存成功');
			die ;
		}

		if ($data_type == 'system_push') {
			$this -> set_val('ws_push_config', 'push');
			$this -> set_val('weixin_push_config', 'push');
			$this -> set_val('msg_push_config', 'push');
			$this -> success('保存成功');
			die ;

		}

		$this -> _save();
	}

	//保存微信菜单
	public function add_weixin_menu() {
		$model = M('weixin_menu');
		$data['option_id'] = $_SESSION['oid'];
		$data['name'] = $_POST['name'];
		$data['url'] = $_POST['url'];
		$data['key'] = $_POST['key'];
		$data['type'] = $_POST['type'];
		$data['pid'] = $_POST['pid'];
		$data['sort'] = $_POST['sort'];
		$model -> add($data);
		$this -> success('新增成功！');
	}

	public function save_weixin_menu() {
		$this -> _update('weixin_menu');
	}

	//保存微信应用
	public function add_weixin_option() {
		$model = M('weixin_option');
		$data['name'] = $_POST['name'];
		$data['sort'] = $_POST['sort'];
		$model -> add($data);
		$this -> success('新增成功！');
	}

	//获取微信菜单
	public function release() {
		import("Weixin.ORG.Util.Weixin");
		$weixin = new \Weixin();

		$agent_list = $weixin -> get_agent_list();

		if ($agent_list) {
			foreach ($agent_list as $key => $val) {
				$new[$val['agentid']] = $val['name'];
			}
			unset($new[0]);
		}

		$this -> assign('agent_list', $new);
		$this -> display();
	}

	//发布
	public function weixin_update() {
		$node = M("weixin_menu");
		$menu = array();
		$where['option_id'] = $_SESSION['oid'];
		$menu = $node -> field('id,pid,name,url,key,type') -> where($where) -> order('sort ASC') -> select();

		foreach ($menu as $key => &$val) {
			if ($val['type'] == 'view') {
				$val['url'] = $this -> _get_weixin_auth_url($val['url']);
			}
		}
		//生成微信菜单所需格式。
		$menu_tree = $this -> create_tree($menu);
		$data['button'] = $menu_tree;
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		$agent_id = $_POST['eq_pid'];
		import("Weixin.ORG.Util.Weixin");
		$weixin = new \Weixin();
		$rs = $weixin -> set_menu($data, $agent_id);
		if ($rs) {
			$this -> success('发布成功');
		} else {
			$this -> error($rs);;
		}
	}

	// 创建Tree
	public function create_tree($list, $root = 0, $pk = 'id', $pid = 'pid', $child = 'sub_button') {
		$tree = array();
		if (is_array($list)) {
			// 创建基于主键的数组引用

			$refer = array();
			foreach ($list as $key => $data) {
				$refer[$data[$pk]] = &$list[$key];
			}

			foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId = 0;
				if (isset($data[$pid])) {
					$parentId = $data[$pid];
				}
				if ((string)$root == $parentId) {
					$tree[] = &$list[$key];
				} else {
					if (isset($refer[$parentId])) {
						$parent = &$refer[$parentId];
						$parent[$child][] = &$list[$key];
					}
				}
			}
		}
		return $tree;
	}

	function set_val($key, $type) {
		$data['val'] = I($key);
		$data['data_type'] = $type;

		$where_system['code'] = $key;
		$vo = M('SystemConfig') -> where($where_system) -> find();
		if (!empty($vo)) {
			$data['id'] = $vo['id'];
			$list = M('SystemConfig') -> save($data);
		} else {
			$data['code'] = $key;
			$list = M('SystemConfig') -> add($data);
		}

		if ($list !== false) {
			return true;
		} else {
			return false;
		}
	}

	public function del($id) {
		$this -> _destory($id);
	}

	public function del_menu($id) {
		$model = M("WeixinMenu");
		$where['id'] = $id;
		$result = $model -> where($where) -> delete();
		if ($result) {
			$this -> success('删除成功！');
		}
	}

	//读取
	function edit($id) {
		$model = M("weixin_menu");
		$vo = $model -> find($id);
		if (IS_AJAX) {
			if ($vo !== false) {// 读取成功
				$return['data'] = $vo;
				$return['status'] = 1;
				$return['info'] = "读取成功";
				$this -> ajaxReturn($return);
			} else {
				$return['status'] = 0;
				$return['info'] = "读取错误";
				$this -> ajaxReturn($return);
			}
		}
		$this -> assign('vo', $vo);
		$this -> display();
		return $vo;
	}

	public function winpop() {
		$node = M("SystemConfig");
		$menu = array();
		$where['data_type'] = array('eq', 'common');
		$where['is_del'] = array('eq', 0);
		$menu = $node -> where($where) -> field('id,pid,name') -> order('sort asc') -> select();

		$tree = list_to_tree($menu);
		$this -> assign('menu', popup_tree_menu($tree));

		$this -> display();
	}

	public function winpop2() {
		$this -> winpop();
	}

	public function winpop_weixin() {
		$node = M("weixin_menu");
		$weixin_menu = array();
		$where['option_id'] = array('eq', $_SESSION['oid']);
		$weixin_menu = $node -> where($where) -> field('id,pid,name') -> order('sort asc') -> select();

		$tree = list_to_tree($weixin_menu);
		$this -> assign('weixin_menu', popup_tree_menu($tree));

		$this -> display();
	}

	public function winpop4() {
		$this -> winpop3();
	}

	private function _get_weixin_auth_url($url) {
		$site_url = get_system_config("weixin_site_url");
		$corpid = get_system_config("weixin_corp_id");
		$redirect_uri = urlencode($site_url . '/index.php?m=Weixin');
		$url = base64_encode($site_url.$url);
		$oauth_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$corpid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$url}#wechat_redirect";
		return $oauth_url;
	}

}
?>