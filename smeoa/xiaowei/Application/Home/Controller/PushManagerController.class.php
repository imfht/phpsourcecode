<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class PushManagerController extends HomeController {
	protected $config = array('app_type' => 'asst');

	function index() {
		$receve_mail_running = $this -> _global("receve_mail_running");
		$receve_mail_timemap = $this -> _global("receve_mail_timemap");
		if ($receve_mail_running) {
			$diff = time() - $receve_mail_timemap;
			if ($diff > 30) {
				$receve_mail_running = false;
			}
		}

		$send_wechat_running = $this -> _global("send_wechat_running");
		$send_wechat_timemap = $this -> _global("send_wechat_timemap");

		if ($send_wechat_running) {
			$diff = time() - $send_wechat_timemap;
			if ($diff > 5) {
				$send_wechat_running = false;
			}
		}

		$this -> assign("receve_mail_running", $receve_mail_running);
		$this -> assign("send_wechat_running", $send_wechat_running);

		$this -> display();
	}

	function stop_receve_mail() {
		$this -> _global("receve_mail_running", false);
	}

	function start_receve_mail() {
		session_write_close();
		ignore_user_abort(true);
		set_time_limit(0);

		$this -> _global("receve_mail_running", true);
		$timemap = $this -> _global("receve_mail_timemap");
		$diff = time() - $timemap;
		$this -> _global("TEST", $diff);
		if ($diff > 10) {
			while (true) {
				$this -> _global("TEST", time());
				$this -> _global("receve_mail_timemap", time());
				$flag = $this -> _global("receve_mail_running");
				//send_push($new,$this->_global("receve_mail_running"),1,1);
				if (empty($flag)) {
					$this -> _global("TEST", "2");
					exit();
				}

				$mail_account_list = M("MailAccount") -> select();
				foreach ($mail_account_list as $account) {
					$flag = $this -> _global("receve_mail_running");
					if (empty($flag)) {
						$this -> _global("TEST", "2");
						exit();
					}
					R("Mail/receve", array($account['id'], true));
					$this -> _global("receve_mail_timemap", time());
					sleep(1);
				}
			}
		} else {

		}
	}

	function stop_send_wechat() {
		$this -> _global("send_wechat_running", false);
	}

	function start_send_wechat() {
		session_write_close();
		ignore_user_abort(true);
		set_time_limit(0);
		$this -> _global("send_wechat_running", true);

		$timemap = $this -> _global("send_wechat_timemap");
		$diff = time() - $timemap;

		if ($diff > 3) {
			while (true) {
				$flag = $this -> _global("send_wechat_running");
				if (empty($flag)) {
					exit();
				}
				sleep(1);
				$this -> _global("send_wechat_timemap", time());

				$where = array();
				$where['westatus'] = array('eq', 1);
				$data = D("PushView") -> where($where) -> find();

				//$test=dump($data,false);
				//$this->wechat_test($test);
				$where['id'] = $data['id'];
				if ($data) {
					M("Push") -> delete($data['id']);
					//$this->wechat_test($test);
					$this -> send_wechat($data['info'], $data['openid']);
				}
			}
		} else {
	
		}
	}

	function receve_mail() {
		session_write_close();
		set_time_limit(0);
		$where['is_del'] = array('eq', 0);
		$mail_account_list = D("MailAccountView") -> where($where) -> select();
		foreach ($mail_account_list as $account) {
			R("Mail/receve", array($account['id'], true));
			sleep(1);
		}
		sleep(1);
		$return['info'] = 'finish';
		$return['status'] = 1;
		$this -> ajaxReturn($return);
	}

	function send_wechat() {
		session_write_close();
		set_time_limit(0);

		$where['westatus'] = array('eq', 1);
		$push_list = D("PushView") -> where($where) -> select();
		foreach ($push_list as $push) {
			M("Push") -> delete($push['id']);
			$this -> _send_wechat($push['info'], $push['openid']);
			sleep(1);
		}
		sleep(5);
		$return['info'] = 'finish';
		$return['status'] = 1;
		$this -> ajaxReturn($return);
	}

	private function _send_wechat($content, $openid = '', $type = 'text') {
		import("Weixin.ORG.Util.ThinkWechat");
		$agent_id=get_system_config('OA_AGENT_ID');
		$weixin = new \ThinkWechat($agent_id);
		// $openid = 'o0ehLt1pOAIEFZtPD4ghluvjamf0';
		$restr = $weixin -> send_msg($content, $openid, $type);		
		return $restr;
	}

	function test() {
		$openid = '1001';
		$this -> _send_wechat(date("Y-m-d h:s"), $openid);
	}

	private function _global($name, $value = '', $path = DATA_PATH) {
		static $_cache = array();
		$filename = $path . $name . '.php';
		if ('' !== $value) {
			if (is_null($value)) {
				// 删除缓存
				return false !== strpos($name, '*') ? array_map("unlink", glob($filename)) : unlink($filename);
			} else {
				// 缓存数据
				$dir = dirname($filename);
				// 目录不存在则创建
				if (!is_dir($dir))
					mkdir($dir, 0755, true);
				$_cache[$name] = $value;
				return file_put_contents($filename, strip_whitespace("<?php\treturn " . var_export($value, true) . ";?>"));
			}
		}
		// 获取缓存数据
		if (is_file($filename)) {
			$value =
			include $filename;
			$_cache[$name] = $value;
		} else {
			$value = false;
		}
		return $value;
	}
}
?>