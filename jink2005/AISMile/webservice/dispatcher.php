<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

ob_start();

require_once(dirname(__FILE__).'/../config/config.inc.php');

//set http auth headers for apache+php-cgi work around
if (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches))
{
	list($name, $password) = explode(':', base64_decode($matches[1]));
	$_SERVER['PHP_AUTH_USER'] = strip_tags($name);
}

//set http auth headers for apache+php-cgi work around if variable gets renamed by apache
if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $matches))
{
	list($name, $password) = explode(':', base64_decode($matches[1]));
	$_SERVER['PHP_AUTH_USER'] = strip_tags($name);
}

// Use for image management (using the POST method of the browser to simulate the PUT method)
$method = isset($_REQUEST['ps_method']) ? $_REQUEST['ps_method'] : $_SERVER['REQUEST_METHOD'];

if (isset($_SERVER['PHP_AUTH_USER']))
	$key = $_SERVER['PHP_AUTH_USER'];
elseif (isset($_GET['ws_key']))
	$key = $_GET['ws_key'];
else
{
	header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized');
	header('WWW-Authenticate: Basic realm="Welcome to MileBiz Webservice, please enter the authentication key as the login. No password required."');
	die;
}

if (isset($_REQUEST['xml']))
{
	// if a XML is in POST
	$input_xml = stripslashes($_REQUEST['xml']);
}
else
{
	// if no XML
	$input_xml = NULL;

	// if a XML is in PUT
	if ($_SERVER['REQUEST_METHOD'] == 'PUT')
	{
		$putresource = fopen("php://input", "r");
		while ($putData = fread($putresource, 1024))
			$input_xml .= $putData;
		fclose($putresource);
	}
}

$params = $_GET;
unset($params['url']);

$class_name = WebserviceKey::getClassFromKey($key);
$bad_class_name = false;
if (!class_exists($class_name))
{
	$class_name = 'WebserviceRequest';
	$bad_class_name = true;
}
// fetch the request
WebserviceRequest::$ws_current_classname = $class_name;
$request = call_user_func(array($class_name, 'getInstance'));
$result = $request->fetch($key, $method, $_GET['url'], $params, $bad_class_name, $input_xml);

// display result
if (ob_get_length() != 0)
	header('Content-Type: application/javascript'); // Useful for debug...

// Manage cache
if (isset($_SERVER['HTTP_LOCAL_CONTENT_SHA1']) && $_SERVER['HTTP_LOCAL_CONTENT_SHA1'] == $result['content_sha1']) {
	$result['status'] = $_SERVER['SERVER_PROTOCOL'].' 304 Not Modified';
}

foreach ($result['headers'] as $param_value)
{
	header($param_value);
}
if (isset($result['type']))
{
//	header($result['content_sha1']);
	if (!isset($_SERVER['HTTP_LOCAL_CONTENT_SHA1']) || $_SERVER['HTTP_LOCAL_CONTENT_SHA1'] != $result['content_sha1'])
	{
		echo $result['content'];
	}
		
}
ob_end_flush();