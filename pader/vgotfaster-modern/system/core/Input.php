<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Core;

/**
 * VgotFaster 输入接口类
 *
 * 用户输入数据的接收和过滤
 *
 * @package VgotFaster
 * @subpackage Library
 * @author pader
 */
class Input {

	public $ip = '';

	public function __construct()
	{
		$phpMQG = get_magic_quotes_gpc();
		define('MAGIC_QUOTES_GPC',$GLOBALS['CONFIG']['config']['open_magic_quotes_gpc']);

		//服从框架配置
		if (MAGIC_QUOTES_GPC and !$phpMQG) {
			$magic = 'deepAddslashes';
		} elseif (!MAGIC_QUOTES_GPC and $phpMQG) {
			$magic = 'deepStripslashes';
		}

		if (isset($magic)) {
			foreach (array('_GET','_POST','_REQUEST','_SERVER','_COOKIE') as $var) {
				isset($GLOBALS[$var]) AND $GLOBALS[$var] = $this->$magic($GLOBALS[$var]);
			}
		}
	}

	/**
	 * Get Input Data
	 *
	 * Get request data and you can use function to filter data
	 *
	 * @param string $name
	 * @param string $functions Use function to filter request, example: 'trim|html2text|stripslashes'
	 * @return mixed Filtered request content
	 */
	public function get($name, $functions=NULL) { return $this->fetchGPC($_GET, $name, $functions); }
	public function post($name, $functions=NULL) { return $this->fetchGPC($_POST, $name, $functions); }
	public function cookie($name, $functions=NULL) { return $this->fetchGPC($_COOKIE, $name, $functions); }
	public function server($name, $functions=NULL) { return $this->fetchGPC($_SERVER, $name, $functions); }
	public function request($name, $functions=NULL) { return $this->fetchGPC($_REQUEST, $name, $functions); }

	/**
	 * Get Input Data From GET or Post
	 *
	 * It will fetch GET first, if none set int GET, then fetch POST
	 *
	 * @param string $name
	 * @param null $functions
	 * @return mixed
	 */
	public function gp($name,$functions=NULL) {
		return isset($_GET[$name]) ? $this->fetchGPC($_GET, $name, $functions) : $this->fetchGPC($_POST, $name, $functions);
	}

	/**
	 * 获取URI段
	 *
	 * @param int $number 获取第几段
	 * @return URI segment
	 */
	public function segment($number)
	{
		$number--;
		$uri = $this->uri('uri');
		return isset($uri[$number]) ? $uri[$number] : NULL;
	}

	/**
	 * 获取参数列表
	 *
	 * 可以用于 list() 把参数具体变量化
	 * 例：list($id,$page,$style) = $this->input->params(3);
	 * 使用 function action($id='',$page='',$style='') 的缺点是你必须设定每个参数的默认值，比较繁琐
	 * 如果没有设定默认值，则 PHP 会在参数不完整时报错
	 *
	 * @param int|bool $length 返回参数数组的长度，当参数数组长度不够时，会自动使用 NULL 填充到此长度以确保 list() 能正常工作
	 * @return array Params
	 */
	public function params($length=TRUE)
	{
		if ($length === TRUE or isset($GLOBALS['URI']['params'][$length-1])) {
			return $GLOBALS['URI']['params'];
		} else {
			$params = $this->uri('params');
			return array_pad($params, $length, NULL);
		}
	}

	/**
	 * 将段以 name/value/name/value 的形式组成数组返回
	 *
	 * @param bool $pos 设为数字时为从 URI 第几段算起,默认从动作之后
	 * @return array URI Assoc Data
	 */
	public function assoc($pos=TRUE)
	{
		if ($pos === TRUE) {
			$params = $this->uri('params');
		} else {
			$params = $pos > 1 ? array_slice($this->uri('uri'),$pos-1) : $this->uri('uri');
		}

		$assoc = array();
		foreach (array_chunk($params,2) as $row) {
			$assoc[$row[0]] = isset($row[1]) ? $row[1] : NULL;
		}

		return $assoc;
	}

	/**
	 * Get URI Parameter
	 *
	 * file|controller|action|params|string|uri|route
	 *
	 * @param string $key
	 * @return string|array
	 */
	public function uri($key=NULL)
	{
		if (is_null($key)) {
			return $GLOBALS['URI'];
		} else {
			return $GLOBALS['URI'][$key];
		}
	}

	/**
	 * 获取访问者IP地址
	 *
	 * @return IP Address
	 */
	public function ipAddress()
	{
		if($this->ip != '') return $this->ip;

		if(!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $this->server('HTTP_CLIENT_IP');
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $this->server('HTTP_X_FORWARDED_FOR');
		elseif(!empty($_SERVER['REMOTE_ADDR'])) $ip = $this->server('REMOTE_ADDR');
		else $ip = '';

		preg_match('/[\d\.]{7,15}/', $ip, $ips);
		$this->ip = !empty($ips[0]) ? $ips[0] : 'unknown';
		unset($ips);

		return $this->ip;
	}

	/**
	 * 提取HTML中的文本内容,清除HTML代码
	 *
	 * @param string $str HTML code
	 * @return string
	 */
	public function html2text($str)
	{
		$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU",'',$str);
		$str = str_replace(array('<br />','<br>','<br/>'), "\n", $str);
		$str = strip_tags($str);
		return $str;
	}

	/**
	 * 检查是否正确提交了表单 //debug 此函数还处于调试阶段
	 *
	 * @param string $var 需要检查的变量
	 * @param bool $allowget 是否允许GET方式
	 * @param bool $seccodecheck 验证码检测是否开启
	 * @return bool
	 */
	public function isSubmit($var, $allowget=false, $seccodecheck=false)
	{
		if(empty($GLOBALS['_REQUEST'][$var])) {
			return FALSE;
		} else {
			global $_SERVER;
			if($allowget || ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) ||
				preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
				return TRUE;
			} else {
				showError('submit_invalid');//debug 此处还缺少
			}
		}
	}

	/**
	 * Array Deep Add Slashes
	 *
	 * @param array|string $var
	 * @return array|string
	 */
	public function deepAddslashes($var)
	{
		if (is_array($var)) {
			foreach($var as $key => $val) {
				$var[$key] = $this->deepAddslashes($val);
			}
			return $var;
		} else return addslashes($var);
	}

	/**
	 * Array Deep Strip Slashes
	 *
	 * @param array|string $var
	 * @return
	 */
	public function deepStripslashes($var)
	{
		if(is_array($var)) {
			foreach($var as $key => $val) {
				$var[$key] = $this->deepStripslashes($val);
			}
			return $var;
		} else return stripslashes($var);
	}



	/**
	 * 返回指定全局请求的值
	 *
	 * @param array $GPC
	 * @param string $key
	 * @param bool $functions
	 * @return mixed Var
	 */
	private function fetchGPC(&$GPC, $key, $functions=NULL)
	{
		if (!isset($GPC[$key])) return null;

		$var = $GPC[$key];

		if (!$functions) {
			return $var;
		} else {
			if (!is_array($functions)) {
				$functions = explode('|',$functions);
			}
			foreach ($functions as $f) {
				if (function_exists($f)) {
					$var = $f($var);
				} elseif (method_exists($this,$f)) {
					$var = $this->$f($var);
				} else {
					showError('Unabled to call function '.$f.'()', true, 2);
				}
			}
			return $var;
		}
	}

}
