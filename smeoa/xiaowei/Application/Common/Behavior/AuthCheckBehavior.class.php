<?php

namespace Common\Behavior;
use Think\Behavior;

defined('THINK_PATH') or exit();

class AuthCheckBehavior extends Behavior {
	protected $config;
	public function run(&$params) {
		//个人数据
		$app_type = $params['app_type'];	
		switch($app_type) {
			case 'weixin':
				if(!is_weixin()){
					$this->error('只能在微信里访问');
				}
				return true;
			case 'public' :
				$auth = array('admin' => false, 'write' => false, 'read' => true);
				$params['auth'] = $auth;
				return true;
				break;

			case 'asst' :
				$auth = array('admin' => true, 'write' => true, 'read' => true);
				$params['auth'] = $auth;
				return true;
				break;

			case 'personal' :
				$auth = array('admin' => true, 'write' => true, 'read' => true);
				$params['auth'] = $auth;
				return true;
				break;

			case 'common' :
				$auth = $this -> get_auth();
				break;
			case 'master' :
				$auth = $this -> get_auth();
				//dump($auth);
				if ($auth['admin']) {
					return true;
				}
				break;

			case 'folder' :				
				if (in_array(ACTION_NAME, array('folder_manage','field_manage'))) {
					$auth = $this -> get_auth();
					break;
				}
				if (isset($_REQUEST['fid'])) {
					$fid = $_REQUEST['fid'];
					$auth = D("SystemFolder") -> get_folder_auth($fid);
					break;
				}

				if (isset($_REQUEST['id'])) {
					$id = $_REQUEST['id'];
					if (!empty($id)) {
						if (is_array($id)) {
							$where["id"] = array("in", array_filter($id));
						} else {
							$where["id"] = array('in', array_filter(explode(',', $id)));
						}
						$model = D(CONTROLLER_NAME);
						$folder_id = $model -> where($where) -> getField('folder');
						$auth = D("SystemFolder") -> get_folder_auth($folder_id);
						break;
					}
				}
				$auth = $this -> get_auth();
				break;
			default :
				$auth = $this -> get_auth();
				break;
		}

		$is_match = false;
		$params['auth'] = $auth;
		$action = strtolower(ACTION_NAME);		
		if (isset($params['admin'])) {
			$controller_auth_admin = explode(',', $params['admin']);
			if (!$is_match and in_array($action, $controller_auth_admin)) {
				$is_match = true;
				$result = $auth['admin'];
			}
		}		
		if (isset($params['write'])) {
			$controller_auth_write = explode(',', $params['write']);
			if (!$is_match and in_array($action, $controller_auth_write)) {
				$is_match = true;
				$result = $auth['write'];
			}
		}

		if (isset($params['read'])) {
			$controller_auth_read = explode(',', $params['read']);
			if (!$is_match and in_array($action, $controller_auth_read)) {
				$is_match = true;
				$result = $auth['read'];
			}
		}

		if (isset($params['public'])) {
			$controller_auth_public = explode(',', $params['public']);
			if (!$is_match and in_array($action, $controller_auth_public)) {
				$is_match = true;
				$result = true;
			}
		}

		$default_auth_admin = explode(',', C('AUTH.admin'));
		$default_auth_write = explode(',', C('AUTH.write'));
		$default_auth_read = explode(',', C('AUTH.read'));

		if (!$is_match and in_array($action, $default_auth_admin)) {
			$is_match = true;
			$result = $auth['admin'];
		}
		if (!$is_match and in_array($action, $default_auth_write)) {
			$is_match = true;
			$result = $auth['write'];
		}
		if (!$is_match and in_array($action, $default_auth_read)) {
			$is_match = true;
			$result = $auth['read'];
		}

		if (!$result) {
			$auth_id = session(C('USER_AUTH_KEY'));
			if (!isset($auth_id)) {
				//跳转到认证网关
				redirect(U(C('USER_AUTH_GATEWAY')));
			}
			$e['message'] = "没有权限";
			include                         C('TMPL_NO_HAVE_AUTH');
			die ;
		};
		return true;
	}

	function get_auth() {

		$access_list = D("Node") -> access_list();

		$access_list = array_filter($access_list, array($this, 'filter_module'));
		$access_list = rotate($access_list);

		$module_list = $access_list['url'];
		$module_list = array_map(array($this, "get_module"), $module_list);
		$module_list = str_replace("_", "", $module_list);

		$access_list_admin = array_filter(array_combine($module_list, $access_list['admin']));
		$access_list_write = array_filter(array_combine($module_list, $access_list['write']));
		$access_list_read = array_filter(array_combine($module_list, $access_list['read']));

		$module_name = strtolower(CONTROLLER_NAME);

		$auth['admin'] = array_key_exists($module_name, $access_list_admin) || array_key_exists("##" . $module_name, $access_list_admin);

		$auth['write'] = array_key_exists($module_name, $access_list_write) || array_key_exists("##" . $module_name, $access_list_write);

		$auth['read'] = array_key_exists($module_name, $access_list_read) || array_key_exists("##" . $module_name, $access_list_read);

		if ($auth['admin'] == true) {
			$auth['write'] = true;
		}
		if ($auth['write'] == true) {
			$auth['read'] = true;
		}
		return $auth;
	}

	function get_module($str) {
		$arr_str = explode("/", $str);
		return strtolower($arr_str[0]);
	}

	function filter_module($str) {
		if (strpos($str['url'], '##') !== false) {
			return true;
		}
		if (empty($str['admin']) && empty($str['write']) && empty($str['read'])) {
			return false;
		}
		if (strpos($str['url'], 'index')) {
			return true;
		}
		return false;
	}

}
?>