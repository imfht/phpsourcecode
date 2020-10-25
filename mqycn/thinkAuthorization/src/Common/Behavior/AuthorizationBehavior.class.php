<?php
/**
 * 名称：AuthorizationBehavior
 * 功能：权限行为类
 * 作者: Qiyuan<mqycn@126.com>
 * 更新：http://git.oschina.net/mqycn/thinkAuthorization/
 */

namespace Common\Behavior;

class AuthorizationBehavior {

	/**
	 * 当前控制器
	 */
	private $thisController;

	/**
	 * 选项
	 */
	protected $option = array(
		'AUTHORIZATION_OPEN' => false,
		'AUTHORIZATION_URL' => false,
	);

	/**
	 * 默认权限，如果不设置无法访问设置页面
	 */
	private $AuthDefault = array(
		'admin/index/index',
		'admin/authorization/index',
		'admin/authorization/add',
		'admin/authorization/edit',
		'admin/authorization/save',
		'admin/authorization/update',
	);

	/**
	 * 入口
	 */
	public function run(&$controller) {
		if (C('AUTHORIZATION_OPEN')) {
			$this->thisController = $controller;
			$arg = $this->Check();
		}
	}

	/**
	 * 错误页面，如果 当前控制亲存在 存在 _ShowError，调用_ShowError
	 */
	private function error($message) {
		if ('' == C('AUTHORIZATION_URL')) {
			$badURL = U('/Error');
		} else {
			$badURL = U(C('AUTHORIZATION_URL'));
		}
		if (in_array('_ShowError', get_class_methods($this->thisController))) {
			$this->thisController->_ShowError($message, $badURL);
		} else {
			die('<!DOCTYPE html><html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>' . $message . '</title></head><body style="background:#CCC;"><div style="background:#EEE;width:400px;position:absolute;left:50%;top:20%;margin-left:-200px;border:solid 5px #999;color:#666;padding:20px;border-radius:15px;"><h1>:( 鉴权出错了!</h1><hr /><p><a style="color:#333;text-decoration:none;" href="' . $badURL . '">' . $message . '，如果没有跳转点击这里</a></p></div><script>setTimeout(function(){location.href = "' . $badURL . '"}, 3000);</script></body></html>');
		}
	}

	/**
	 * 获取当前运行参数
	 */
	private function GetRunStatus() {

		$arr = array();
		$ver = explode('.', THINK_VERSION);

		if ('3' == $ver[0]) {
			switch ($ver[1]) {
			case '0': //ThinkPHP 3.0
			case '1': //ThinkPHP 3.1
				$arr['Module'] = 'admin';
				$arr['Controller'] = isset($GLOBALS['_GET']['_URL_'][0]) ? $GLOBALS['_GET']['_URL_'][0] : C('DEFAULT_MODULE');
				$arr['Action'] = isset($GLOBALS['_GET']['_URL_'][1]) ? $GLOBALS['_GET']['_URL_'][1] : 'index';
				break;

			case '2': //ThinkPHP 3.2
				$arr['Module'] = MODULE_NAME;
				$arr['Controller'] = CONTROLLER_NAME;
				$arr['Action'] = ACTION_NAME;
				break;

			default:
				$this->error('权限验证：未知的版本 3.' . $ver[1]);
				break;
			}
		} else {
			$this->error('权限验证：暂时只支持ThinkPHP3.x');
		}

		$arr['URI'] = strtolower($arr['Module']) . '/' . strtolower($arr['Controller']) . '/' . strtolower($arr['Action']);
		return $arr;
	}

	/**
	 * 读取当前登陆用户组的权限配置
	 */
	private function GetAuthList() {
		$gid = $_SESSION['auth_group'];
		if (!S('auth_group_' . $gid)) {
			$UserGroup = M('AuthGroup');
			$ugroup = $UserGroup->find($gid);

			if ($ugroup) {
				$authArray = unserialize($ugroup['ag_auth']);
				//不存在权限，则写入默认权限
				if (!is_array($authArray)) {
					if ($gid == 1) {
						//管理员自动增加初始权限
						$authArray = $this->AuthDefault;
						$ugroup['ag_auth'] = serialize($authArray);
						$UserGroup->save($ugroup);
					} else {
						$this->error('请联系管理员在后台增加权限');
					}
				}
				S('auth_group_' . $gid, $authArray);
			} else {
				unset($UserGroup);
				$this->error('权限验证：用户权限组不存在，请联系管理员处理');
			}
		}
		return S('auth_group_' . $gid);
	}

	/**
	 * 检查是否有权限
	 */
	private function Check() {
		$_auth = $this->GetAuthList();
		$_run = $this->GetRunStatus();
		if (!in_array($_run['URI'], $_auth)) {
			$this->error('权限验证：没有权限' . (APP_DEBUG === true ? '(' . $_run['URI'] . ')' : ''));
		}

	}
}

?>