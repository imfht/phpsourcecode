<?php
/*
    Mini Portal System
*/

if($_GET['act'] == 'logged')
{
	if( isset( $_POST['ip'] ) && isset ( $_POST['mac'] ) )
	{
		$ip = $_POST['ip']; $mac = $_POST['mac'];
		
		// 开放此用户上网
		exec("sudo iptables -I portal 1 -t mangle -m mac --mac-source $mac -j RETURN");
		exec("sudo rmtrack " . $ip);
		echo "OK";
		exit;
	}
	else
	{
		echo "ERROR";
		exit;
	}
}

if($_GET['act'] == 'logout')
{
	if(!isset( $_POST['ip'] ))
	{
		echo "ERROR";
		exit;
	}
	// 获得IP地址
	$ip = $_POST['ip'];
	
	// linux 下 arp 命令的地址
	$arp = "/usr/sbin/arp";
	
	// 运行 arp 命令来获取用户 MAC 地址
	$mac = shell_exec("sudo $arp -an " . $ip);
	preg_match('/..:..:..:..:..:../',$mac , $matches);
	$mac = @$matches[0];
	
	// 如果 MAC 地址获取失败
	if( $mac === NULL)
	{
		echo "ERROR";
		exit;
	}
	
	// 执行用户下线（从iptables中删除绕过验证的指令）注：为什么要用循环？防止脑残用户多次登录。所以要删光他的记录。
	while( $chain = shell_exec("sudo iptables -t mangle -L | grep ".strtoupper($mac) ) !== NULL )
	{
		exec("sudo iptables -D portal -t mangle -m mac --mac-source ".strtoupper($mac)." -j RETURN");
	}

	exec("sudo rmtrack " . $ip);
	echo "OK";
	exit;
}

echo "UNKNOWN INTERFACE";

?>
