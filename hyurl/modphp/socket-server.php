<?php
// ModPHP 压缩包名称，如果设置，ModPHP 将从 ZIP 中加载内核
defined('MOD_ZIP') or define('MOD_ZIP', '');

set_time_limit(0); //设置脚本永不超时
if(!extension_loaded('sockets'))
	exit("Extension 'sockets' is not loaded, cannot start socket server.");

require_once (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '').'mod/common/init.php'; //引入初始化程序

/**
 * SocketServer 说明：
 * 直接执行 socket-server.php 将 ModPHP 运行于 Socket 服务器模式。
 * 客户端通过发送 JSON 数据向服务器提交请求，服务器也回应以 JSON 数据。
 * 除非是重现会话，否则 JSON 中必须包含 {obj} 和 {act} 属性，其他属性将作为请求参数。
 * 登录用户的示例(JavaScript)：
 * obj = {obj:'user', act:'login', user_name: 'someone', user_password: ''}
 * WebSocket.send(JSON.stringify(obj));
 * 重现会话的示例(JavaScript)：
 * WebSocket.send(JSON.stringify({MODID: 'fh33v6neol7qt1r0optbspgnv6'}));
 */

/** 具体逻辑 */
$SOCKET_INFO = $SOCKET_USER = array(); //保存连接信息的全局变量
if(is_agent()){
	if(php_sapi_name() == 'cgi-fcgi') //Socket 服务器不能通过 FastCGI 开启
		report_500(lang('mod.socketFastCGIWarning'));
	if(!config('mod.installed') || !is_admin()) report_403();
}
SocketServer::on('open', function($event){ //绑定连接事件
	global $SOCKET_INFO, $SOCKET_USER;
	do_hooks('socket.open', $event);
	if(isset($event['request_headers']['Cookie'])){
		$cookie = explode_assoc($event['request_headers']['Cookie'], '; ', '=');
		$sname = session_name();
		if(!empty($cookie[$sname])){
			socket_retrive_session($cookie[$sname], $event); //重现会话
		}
	}
	$srcId = (int)$event['client'];
	$SOCKET_INFO[$srcId] = array( //保存连接信息到内存中
		'request_headers' => $event['request_headers'], //请求头
		'session_id' => session_id(), //会话 ID
		'user_id' => config('mod.installed') ? me_id() : 0, //用户 ID
		);
})->on('message', function($event){ //绑定消息事件
	global $SOCKET_INFO, $SOCKET_USER, ${'DENIES'.INIT_TIME};
	$installed = config('mod.installed');
	do_hooks('socket.message', $event);
	if(error()) goto sendResult;
	if($event['type'] == 'text'){ //文本消息
		$data = json_decode($event['data'], true);
		conv_request_vars($data); //转义参数
	}else{ //二进制消息作为 Data URI Scheme 上传处理
		if(class_exists('finfo')){
			$finfo = new Finfo(FILEINFO_MIME_TYPE);
			$type = $finfo->buffer($event['data']);
		}else{
			$type = 'text/plain';
		}
		$data = array(
			'obj'=>'file',
			'act'=>'upload',
			'file'=>"data:{$type},{$event['data']}",
			);
	}
	$_GET['obj'] = isset($data['obj']) ? $data['obj'] : '';
	$_GET['act'] = isset($data['act']) ? $data['act'] : '';
	$obj = strtolower($_GET['obj']);
	$act = $_GET['act'];
	unset($data['obj'], $data['act']);
	$srcId = (int)$event['client'];
	$header = $SOCKET_INFO[$srcId]['request_headers']; //取出客户端请求头
	$hasHost = isset($header[0], $header['Host']);
	if($hasHost) detect_site_url($header[0], $header['Host']); //检测网站地址
	if(isset($data['HTTP_REFERER']) || $hasHost){
		if(empty($data['HTTP_REFERER'])){
			if($url = parse_url(site_url())){
				extract($url);
				$header = explode(' ', $header[0]);
				$port = isset($port) ? ':'.$port : (is_ssl() ? ':443' : '');
				$data['HTTP_REFERER'] = $scheme.'://'.$host.$port.$header[1];
			}
		}
		$_SERVER['HTTP_REFERER'] = isset($data['HTTP_REFERER']) ? $data['HTTP_REFERER'] : ''; //设置来路页面
		$init = array('__DISPLAY__' => null);
		if($installed){
			do_hooks('mod.init', $init); //系统初始化接口
			if(error()) goto sendResult;
		}
		$url = explode('?', $_SERVER['HTTP_REFERER']);
		$tplPath = template_path('', false);
		if($url[0] == site_url('mod.php')){
			display_file($tplPath.config('site.errorPage.403'), true);
		}elseif($init['__DISPLAY__'] === false){
			display_file($tplPath.config('site.errorPage.404'), true);
		}elseif($init['__DISPLAY__']){
			display_file($init['__DISPLAY__'], true);
		}else{
			display_file($url[0]);
		}
	}
	if(!display_file()) display_file(__SCRIPT__, true);
	$sname = session_name();
	if(!empty($data[$sname])){
		if(!socket_retrive_session($data[$sname], $event)) //尝试重现会话
			goto forbidden;
	}elseif($sid = $SOCKET_INFO[$srcId]['session_id']){
		session_retrieve($sid); //重现会话
	}
	if(!$SOCKET_INFO[$srcId]['session_id'])
		$SOCKET_INFO[$srcId]['session_id'] = session_id();
	if(!$SOCKET_INFO[$srcId]['user_id'])
		$SOCKET_INFO[$srcId]['user_id'] = $installed ? me_id() : 0;
	if(is_403()){
		report_403();
	}elseif(is_404()){
		report_404();
	}elseif(is_500()){
		report_500();
	}elseif(($obj == 'mod' || is_subclass_of($obj, 'mod')) && (method_exists($obj, $act) || is_callable(hooks($obj.'.'.$act))) && !in_array($obj.'::'.strtolower($act), ${'DENIES'.INIT_TIME})){
		$uid = $installed ? me_id() : 0;
		sendResult:
		do_hooks('mod.client.call', $data);
		$result = error() ?: $obj::$act($data);
		$result = array_merge($result, array('obj'=>$_GET['obj'], 'act'=>$_GET['act']));
		error(null); //清空错误信息
		do_hooks('mod.client.call.complete', $result); //在获取结果后执行回调函数
		//调用类方法并将结果发送给客户端
		SocketServer::send(@json_encode($result)); //发送 JSON 结果
		if($installed && $obj == 'user' && $result['success']){
			if(!strcasecmp('login', $act)){ //登录操作
				$uid = $result['data']['user_id'];
				if(!isset($SOCKET_USER[$uid])) $SOCKET_USER[$uid] = array();
				if(!in_array($event['client'], $SOCKET_USER[$uid])){
					$SOCKET_USER[$uid][] = &$event['client']; //将用户 ID 和 socket 客户端绑定
				}
			}elseif(!strcasecmp('logout', $act) && $uid){ //登出
				$i = array_search($event['client'], $SOCKET_USER[$uid]);
				if($i !== false) unset($SOCKET_USER[$uid][$i]); //清除内存中的用户信息
				if(!$SOCKET_USER[$uid]) unset($SOCKET_USER[$uid]);
			}
			$SOCKET_INFO[$srcId]['session_id'] = session_id();
			$SOCKET_INFO[$srcId]['user_id'] = me_id();
		}
	}elseif($installed && !$obj && !$act && isset($data[$sname]) && $data[$sname] == session_id()){
		SocketServer::send(json_encode(user::getMe())); //重现会话操作，将登录用户的信息发送给客户端
	}else{
		forbidden:
		report_403(); //未被授权的操作
	}
})->on('error', function($event){ //绑定错误事件
	do_hooks('socket.error', $event);
})->on('close', function($event){ //绑定关闭事件
	global $SOCKET_INFO, $SOCKET_USER;
	do_hooks('socket.close', $event);
	$srcId = (int)$event['client'];
	if(!empty($SOCKET_INFO[$srcId]['user_id'])){ //如果用户已登录，则清除登录信息
		$uid = $SOCKET_INFO[$srcId]['user_id'];
		$i = array_search($event['client'], $SOCKET_USER[$uid]);
		if($i !== false) unset($SOCKET_USER[$uid][$i]);
		if(!$SOCKET_USER[$uid]) unset($SOCKET_USER[$uid]);
	}
	unset($SOCKET_INFO[$srcId]);
});
if(!file_exists($file = __ROOT__.'.socket-server')) file_put_contents($file, 'on');
if(!SocketServer::server()){
	$STDOUT = lang('mod.socketOnTip');
	if($encoding = get_cmd_encoding())
		$STDOUT = iconv('UTF-8', $encoding, $STDOUT) ?: $STDOUT;
	$file = fopen($file, 'r');
	if(!flock($file, LOCK_EX | LOCK_NB)){ //通过检测 .socket-server 文件是否被加锁来判断 Socket 服务器是否在运行
		is_agent() ? report_500($STDOUT) : exit($STDOUT."\n");
	}
	//启动监听
	SocketServer::listen(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : config('mod.SocketServer.port'), function($socket) use($STDOUT){
		if(!is_agent()) fwrite(STDOUT, $STDOUT."\n");
	});
}
