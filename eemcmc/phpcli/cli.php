<?php
define('IN_CLI', PHP_SAPI === 'cli');

if ( IN_CLI )
{
	set_time_limit(0);

	for ($i = 1; $i < $_SERVER['argc']; $i++)
	{
		$arg = explode('=', $_SERVER['argv'][$i]);
		if (count($arg) > 1 || strncmp($arg[0], '-', 1) === 0)
		{
			$_GET[ltrim($arg[0], '-')] = isset($arg[1]) ? $arg[1] : true;
		}
		$_REQUEST = array_merge($_REQUEST,$_GET);
	}

	require_once dirname(__FILE__) . '/unirest.class.php';

	if ( empty($_REQUEST['url']) )
	{
		return CliConsole::output('error', '无效参数 url');
	}

	$url = trim($_REQUEST['url']);
	$method = empty($_REQUEST['method']) ? 'get' : trim($_REQUEST['method']);
	$headers = empty($_REQUEST['sheaders']) ? '' : trim($_REQUEST['sheaders']);
	if ( empty($headers) ) $headers = array();
	else{
		$headers = json_decode($headers);
	}

	unset($_REQUEST['url']);
	unset($_REQUEST['method']);
	unset($_REQUEST['sheaders']);

	$response = http_request($method, $url, $_REQUEST, $headers);

	$response_body = $response->body;
	if ( is_array($response_body) && !empty($response_body))
	{
		$response_body = $response->body;
	}
	else
	{
		$response_body = $response->raw_body;
	}

	CliConsole::output('response_time', date('Y-m-d H:i:s', time()));
	
	$xheaders = empty($_REQUEST['xheaders']) ? '' : trim($_REQUEST['xheaders']);
	if ( !empty($xheaders) )
	{
		CliConsole::output('request_headers', $headers);

		$response_headers = $response->headers;	
		switch( $xheaders )
		{
			case 'print':
				CliConsole::output('response_headers', $response_headers);
				break;
			case 'only':
				return CliConsole::output('response_headers', $response_headers);
				break;	
		}
	}

	return CliConsole::output('response', $response_body);
}
else
{
	// header("Content-Type: text/html;charset=utf-8");
	header("Status: 404 Not Found");
	exit;
}

class CliConsole
{
	static function output($note, $msg)
	{
		$msg = print_r($msg,true);
		if ( is_windows() )
		{
			$msg = iconv("UTF-8", "GBK//IGNORE", $msg);
		}
		fwrite(STDOUT, "[$note]: " . $msg . PHP_EOL);
	}
}


/**
 * if operating system === windows
 */
function is_windows()
{ 
	return 'win' === strtolower(substr(php_uname("s"), 0, 3));
}

function http_request($method, $url, $params=array(), $headers=array())
{
	Unirest::verifyPeer(false);
	Unirest::timeout(86400 * 360);// 修正缺省超时时间
	$method = trim(strtolower($method));
	switch ($method) {
		case 'post':
			$url .= '?' . time();
			$response = Unirest::post($url, $headers, $params);
			break;
		default:
			$params['t'] = time();
			$response = Unirest::get($url, $headers, $params);
			break;	
	}
	return $response;
}