<?php
/*
    Mini Portal System
*/
require_once('config.php');

function curPageURL()
{
	  $pageURL = 'http';
	  if ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == "1" || $_SERVER["HTTPS"] == "ON") 
	  {
		$pageURL .= "s";
	  }
	  $pageURL .= "://";
		$pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	  return $pageURL;
}

// 得到用户IP
$ip = $_SERVER['REMOTE_ADDR'];

// linux 下 arp 命令的地址
$arp = "/usr/sbin/arp";

// 运行 arp 命令来获取用户 MAC 地址
$mac = shell_exec("sudo $arp -an " . $ip);
preg_match('/..:..:..:..:..:../',$mac , $matches);
$mac = @$matches[0];

// 如果 MAC 地址获取失败，则给出警告。
if( $mac === NULL)
{
        $mac = "ERROR";
}

$arr = array ('ip'=>$ip,'mac'=>$mac);

//跳转到认证服务器地址，并传递用户参数
$url = "Location: " . PORTAL_URL . "?nas=" . NAS_ID . "&ip=" . $ip . "&mac=" . urlencode($mac) . "&redirect=" . urlencode(curPageURL());

header($url);

?>