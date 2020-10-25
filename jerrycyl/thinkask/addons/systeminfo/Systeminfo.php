<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace addons\systeminfo;
use app\common\controller\Addons;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

class Systeminfo extends Addons {

	public $info = array(
		'name'        => 'Systeminfo',
		'title'       => '系统环境信息',
		'description' => '用于显示一些服务器的信息',
		'status'      => 1,
		'author'      => 'molong',
		'version'     => '0.1',
	);

	public function install() {
		return true;
	}

	public function uninstall() {
		return true;
	}
	public function version(){
		echo "v1.2.3";
	}
	public function login_success(){
		echo "string";
	}

	//实现的AdminIndex钩子方法
	public function AdminIndex($param) {
		$config = $this->getConfig();

		if (false) {
//extension_loaded('curl')
			$url    = 'http://www.tensent.cn/index.php?m=home&c=version&a=check_version';
			$params = array(
				'version' => ONETHINK_VERSION,
				'domain'  => $_SERVER['HTTP_HOST'],
				'auth'    => sha1(config('DATA_AUTH_KEY')),
			);

			$vars = http_build_query($params);
			$opts = array(
				CURLOPT_TIMEOUT        => 5,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL            => $url,
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => $vars,
				CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
			);

			/* 初始化并执行curl请求 */
			$ch = curl_init();
			curl_setopt_array($ch, $opts);
			$data  = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
		}

		if (!empty($data) && strlen($data) < 400) {
			$config['new_version'] = $data;
		}

		$this->assign('addons_config', $config);
		// if ($config['display']) {
			// $this->template('widget');
		// }
	}
}