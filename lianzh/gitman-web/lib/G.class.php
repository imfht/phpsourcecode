<?php
require_once __DIR__ . '/Arrays.class.php';
require_once __DIR__ . '/Sql.class.php';
require_once __DIR__ . '/TplEngine.class.php';
require_once __DIR__ . '/GitWebhook.class.php';
require_once __DIR__ . '/Form.class.php';

class G
{

	/**
	 * @var SqlDataSource
	 */
	static $ds = null;

	/**
	 * @var TplEngine
	 */
	static $tpl = null;

	/**
	 * 配置信息 数组
	 * @var array
	 */
	static $configs = [];

	static function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    static function diestr($str)
    {
    	self::echo2($str); exit;
    }

	static function normalize($input, $delimiter = ',')
	{
		if (!is_array($input))
		{
			$input = explode($delimiter, $input);
		}
		$input = array_map('trim', $input);
		return array_filter($input, 'strlen');
	}

	static function dump($vars, $label = '', $return = false)
	{
		if (PHP_SAPI === 'cli')
		{
			$content = print_r($vars,true);
			if ( !empty($label) )
			{
				$content = "[$label]: " . $content;
			}			
		    if ($return) { return $content; }
		    self::echo2($content);
		}
		else
		{
			$content = "<pre>\n";
		    if ($label != '') {
		        $content .= "<strong>{$label} :</strong>\n";
		    }
		    $content .= htmlspecialchars(print_r($vars, true),ENT_COMPAT | ENT_IGNORE);
		    $content .= "\n</pre>\n";
		    if ($return) { return $content; }
		    echo $content;
		}
	}

	static function val($arr, $key, $defaults= null)
	{
		return isset($arr[$key]) ? $arr[$key] : $defaults;
	}

	static function js_alert($message = '', $after_action = '', $url = '')
	{
	    $out = "<script type=\"text/javascript\">\n";
	    if (!empty($message)) {
	        $out .= "alert(\"";
	        $out .= str_replace("\\\\n", "\\n", self::t2js(addslashes($message)));
	        $out .= "\");\n";
	    }
	    if (!empty($after_action)) {
	        $out .= $after_action . "\n";
	    }
	    if (!empty($url)) {
	        $out .= "document.location.href=\"";
	        $out .= $url;
	        $out .= "\";\n";
	    }
	    $out .= "</script>";
	    echo $out;
	    exit;
	}

	static function t2js($content)
	{
	    return str_replace(array("\r", "\n"), array('', '\n'), addslashes($content));
	}

	/**
	 * 重定向浏览器到指定的 URL
	 */
	static function redirect($url)
	{
		if (headers_sent()){
			echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
		}
		else{
			header("Location: {$url}");	
		}
	    exit;
	}

	static function echo2($string)
	{
		$winis = 'win' === strtolower(substr(php_uname("s"), 0, 3));
		if (PHP_SAPI === 'cli'){
			if ($winis)
			{
				$string = iconv("UTF-8", "GBK", $string);
			}
			fwrite(STDOUT, $string . PHP_EOL);
		}
		else{
			echo $string;
		}
	}

	static function configLoad()
	{
		if (!empty(self::$configs)){
			G::$ds = Sql::ds(self::$configs['dsn']);
			$tplConfig = array(
				'templateDir' => self::$configs['template_dir'],
				'enableCache' => false,
			);
			G::$tpl = new TplEngine($tplConfig);
		}
	}

	static function app_init()
	{
		static $runIs = false;
		if ($runIs) return;
		$runIs = true;

		error_reporting(E_ALL | E_STRICT);
		date_default_timezone_set('Asia/Shanghai');

		if (PHP_SAPI === 'cli')
		{
			for ($i = 1; $i < $_SERVER['argc']; $i++)
			{
				$arg = explode('=', $_SERVER['argv'][$i]);
				if (count($arg) > 1 || strncmp($arg[0], '-', 1) === 0)
				{
					$_GET[ltrim($arg[0], '-')] = isset($arg[1]) ? $arg[1] : true;
				}
				$_REQUEST = array_merge($_REQUEST,$_GET);
			}
		}
		else 
		{
			session_start();
			header("Content-Type: text/html;charset=utf-8");			
		}

		# 初始化对象
		self::configLoad();

		$q = 'index';
		if ( !empty($_GET['q']) )
		{
			$q = trim( $_GET['q'], "+ \t\r\n\0\x0B" );
			unset( $_GET['q'] );
			$q = preg_replace('/[^a-z0-9\.]/', '', $q);
		}
		if ( empty($q) ) $q = 'index';
		if ( strtolower($q) == 'init' ){
			echo 'no zuo, no die.';exit;
		}
		$action = 'app_' . preg_replace('/[\.]/', '_', $q);
		if ( !is_callable($action) ){
			echo 'no access point: ' . $q;exit;
		}

		if (is_callable('app_init'))
		{
			app_init();
		}

		$action();
	}

	static function jsonSuccess($data)
	{
		return json_encode(array('c'=> 0,'d'=> $data));
	}

	static function jsonFailed($msg='',$statuCode=1, $data='')
	{
		return json_encode(array('c'=> intval($statuCode),'msg'=>$msg,'d'=> $data));
	}
}