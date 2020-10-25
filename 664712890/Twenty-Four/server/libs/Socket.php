<?php

namespace app\libs;
use app\helpers\Logger;
use app\helpers\Data;

class Socket{
	// 客户端列表
	private static $sockets = [];

	// 主连接
	private static $master;

	public static function create($host, $port) {
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1); //1表示接受所有的数据包
		socket_bind($sock, $host, $port);
		socket_listen($sock);

		Logger::add('Server Started.');
		Logger::add('Listening on   : '.$host.' port '.$port);

		self::$sockets[] = $sock;
		self::$master = $sock;

		return $sock;
	}

	/*
	 * 函数说明：对client的请求进行回应，即握手操作
	 * @$sock
	 * @$buffer 接收client请求的所有信息
	 */
	public static function handshake($sock, $buffer) {
	  // 截取Sec-WebSocket-Key的值并加密，其中$key后面的一部分258EAFA5-E914-47DA-95CA-C5AB0DC85B11字符串应该是固定的
	  $buf  = substr($buffer,strpos($buffer, 'Sec-WebSocket-Key:') + 18);
	  $key = trim(substr($buf,0,strpos($buf,"\r\n")));
	  $newKey = base64_encode(sha1($key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

	  // 按照协议组合信息进行返回
	  $message = "HTTP/1.1 101 Switching Protocols\r\n";
	  $message .= "Upgrade: websocket\r\n";
	  $message .= "Sec-WebSocket-Version: 13\r\n";
	  $message .= "Connection: Upgrade\r\n";
	  $message .= "Sec-WebSocket-Accept: " . $newKey . "\r\n\r\n";
	  @ socket_write($sock, $message, strlen($message));

	  return true;
	}

	public static function add($uid, $sock) {
		self::$sockets[$uid] = $sock;
	}

	public static function getMaster() {
		return self::$master;
	}

	public static function getUser($uid) {
		return @ self::$sockets[$uid];
	}

	public static function getAll() {
		return self::$sockets;
	}

	/**
	 * 关闭 指定 用户的连接
	 */
	public static function del($uid) {
		$sock = @ self::$sockets[$uid];

		if($sock) {
			@ socket_close($sock);
			unset(self::$sockets[$uid]);
		}

		$userManager = \app\manager\User::instance();
		$userManager->del($uid);
	}

	/**
	 * 向客户端发送消息
	 */
	public static function send($uid, $type, $msg = '', $data = []) {
		Logger::add("Send to: ".$uid);

		$sock = @ self::$sockets[$uid];
		if(!$sock) {
			Logger::add("Send failed! Connection not find! User: {$uid}");
			return false;
		}

		$data = [
			'type'=> $type,		// 消息类型
			'msg'=> $msg,			// 消息主体
			'data'=> $data,
			'time'=> time(),	// 时间
		];
		
		return self::write($sock, $data);
	}
	
	public static function write($sock, $data) {
		$data = Data::encode(json_encode($data));
		$res = @ socket_write($sock, $data, strlen($data));

		return $res;
	}

	/**
	 * 获取客户端数量
	 */
	public static function getClientNumber() {
		return count(self::$sockets);
	}
}