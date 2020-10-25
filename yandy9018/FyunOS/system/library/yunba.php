<?php

/**
 * ElephantIOClient is a rough implementation of socket.io protocol.
 * It should ease you dealing with a socket.io server.
 *
 * @author Ludovic Barreca <ludovic@balloonup.com>
 */
class ElephantIOClient {
	const TYPE_DISCONNECT = 0;
	const TYPE_CONNECT = 1;
	const TYPE_HEARTBEAT = 2;
	const TYPE_MESSAGE = 3;
	const TYPE_JSON_MESSAGE = 4;
	const TYPE_EVENT = 5;
	const TYPE_ACK = 6;
	const TYPE_ERROR = 7;
	const TYPE_NOOP = 8;
	private $socketIOUrl;
	private $serverHost;
	private $serverPort = 80;
	private $session;
	private $fd;
	private $buffer;
	private $lastId = 0;
	private $read;
	private $checkSslPeer = true;
	private $debug;
	private $handshakeTimeout = null;
	private $callbacks = array ();
	private $emitCallbacks = array();
	private $handshakeQuery = '';
	public function __construct($socketIOUrl, $socketIOPath = 'socket.io', $protocol = 1, $read = true, $checkSslPeer = true, $debug = false) {
		$this->socketIOUrl = $socketIOUrl . '/' . $socketIOPath . '/' . ( string ) $protocol;
		$this->read = $read;
		$this->debug = $debug;
		$this->parseUrl ();
		$this->checkSslPeer = $checkSslPeer;
	}
	
	/**
	 * Set query to be sent during handshake.
	 *
	 * @param array $query
	 *        	Query paramters as key => value
	 * @return Client
	 */
	public function setHandshakeQuery(array $query) {
		$this->handshakeQuery = '?' . http_build_query ( $query );
		
		return $this;
	}
	
	/**
	 * Initialize a new connection
	 *
	 * @param boolean $keepalive        	
	 * @return Client
	 */
	public function init($keepalive = false) {
		$this->handshake ();
		$this->connect ();
		
		if ($keepalive) {
			$this->keepAlive ();
		} else {
			return $this;
		}
	}
	
	/**
	 * Keep the connection alive and dispatch events
	 *
	 * @access public
	 */
	public function keepAlive() {
		while ( is_resource ( $this->fd ) ) {
			if ($this->session ['heartbeat_timeout'] > 0 && $this->session ['heartbeat_timeout'] + $this->heartbeatStamp - 5 < time ()) {
				$this->send ( self::TYPE_HEARTBEAT );
				$this->heartbeatStamp = time ();
			}
			
			$r = array (
					$this->fd 
			);
			$w = $e = null;
			
			if (stream_select ( $r, $w, $e, 5 ) == 0)
				continue;
			
			$res = $this->read ();
			$sess = explode ( ':', $res );
			if (( int ) $sess [0] === self::TYPE_EVENT) {
				unset ( $sess [0], $sess [1], $sess [2] );
				
				$response = json_decode ( implode ( ':', $sess ), true );
				$name = $response ['name'];
				$data = $response ['args'] [0];
				
				//$this->stdout ( 'debug', 'Receive event "' . $name . '" with data "' . var_export($data, true) . '"' );
				
				if (! empty ( $this->callbacks [$name] )) {
					foreach ( $this->callbacks [$name] as $callback ) {
						call_user_func ( $callback, $data );
					}
				}
			}
			else if ((int)$sess[0] === self::TYPE_ACK) {
				$emitId = (int)$sess[3];
				if (isset($this->emitCallbacks[$emitId])) {
					call_user_func($this->emitCallbacks[$emitId]);
				}
			}
		}
	}
	
	/**
	 * Read the buffer and return the oldest event in stack
	 *
	 * @access public
	 * @return string // https://tools.ietf.org/html/rfc6455#section-5.2
	 */
	public function read() {
		// Ignore first byte, I hope Socket.io does not send fragmented frames, so we don't have to deal with FIN bit.
		// There are also reserved bit's which are 0 in socket.io, and opcode, which is always "text frame" in Socket.io
		fread ( $this->fd, 1 );
		
		// There is also masking bit, as MSB, but it's 0 in current Socket.io
		$payload_len = ord ( fread ( $this->fd, 1 ) );
		
		switch ($payload_len) {
			case 126 :
				$payload_len = unpack ( "n", fread ( $this->fd, 2 ) );
				$payload_len = $payload_len [1];
				break;
			case 127 :
				$this->stdout ( 'error', "Next 8 bytes are 64bit uint payload length, not yet implemented, since PHP can't handle 64bit longs!" );
				break;
		}
		
		// Use buffering to handle packet size > 16Kb
		$read = 0;
		$payload = '';
		while ( $read < $payload_len && ($buff = fread ( $this->fd, $payload_len - $read )) ) {
			$read += strlen ( $buff );
			$payload .= $buff;
		}
		$this->stdout ( 'debug', 'Received payload ' . $payload );
		
		return $payload;
	}
	
	/**
	 * Attach an event handler function for a given event
	 *
	 * @access public
	 * @param string $event        	
	 * @param callable $callback        	
	 * @return string
	 */
	public function on($event, $callback) {
		if (! is_callable ( $callback )) {
			throw new InvalidArgumentException ( 'ElephantIOClient::on() type callback must be callable.' );
		}
		
		if (! isset ( $this->callbacks [$event] )) {
			$this->callbacks [$event] = array ();
		}
		
		// @TODO Handle case where callback is a string
		if (in_array ( $callback, $this->callbacks [$event] )) {
			$this->stdout ( 'debug', 'Skip existing callback' );
			return;
		}
		
		$this->callbacks [$event] [] = $callback;
	}
	
	/**
	 * Send message to the websocket
	 *
	 * @access public
	 * @param int $type        	
	 * @param int $id        	
	 * @param int $endpoint        	
	 * @param string $message        	
	 * @return ElephantIO\Client
	 */
	public function send($type, $id = null, $endpoint = null, $message = null) {
		if (! is_int ( $type ) || $type > 8) {
			throw new InvalidArgumentException ( 'ElephantIOClient::send() type parameter must be an integer strictly inferior to 9.' );
		}
		
		$raw_message = $type . ':' . $id . ':' . $endpoint . ':' . $message;
		$payload = new ElephantIOPayload ();
		$payload->setOpcode ( ElephantIOPayload::OPCODE_TEXT )->setMask ( true )->setPayload ( $raw_message );
		$encoded = $payload->encodePayload ();
		
		fwrite ( $this->fd, $encoded );
		
		// wait 100ms before closing connexion
		usleep ( 100 * 1000 );
		
		$this->stdout ( 'debug', 'Sent ' . $raw_message );
		
		return $this;
	}
	
	/**
	 * Emit an event
	 *
	 * @param string $event        	
	 * @param array $args        	
	 * @param string $endpoint        	
	 * @param function $callback
	 *        	- ignored for the time being
	 * @return ElephantIO\Client
	 */
	public function emit($event, $args, $endpoint = null, $callback = null) {
		static $emitId = 1000000;
		
		$emitId ++;
		if (is_callable($callback)) {
			$this->emitCallbacks[$emitId] = $callback;
		}
		return $this->send ( self::TYPE_EVENT, $emitId, $endpoint, json_encode ( array (
				'name' => $event,
				'args' => $args 
		) ) );
	}
	
	/**
	 * Close the socket
	 *
	 * @return boolean
	 */
	public function close() {
		if (is_resource ( $this->fd )) {
			$this->send ( self::TYPE_DISCONNECT );
			fclose ( $this->fd );
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Send ANSI formatted message to stdout.
	 * First parameter must be either debug, info, error or ok
	 *
	 * @access private
	 * @param string $type        	
	 * @param string $message        	
	 */
	private function stdout($type, $message) {
		if (! defined ( 'STDOUT' ) || ! $this->debug) {
			return false;
		}
		
		$typeMap = array (
				'debug' => array (
						36,
						'- debug -' 
				),
				'info' => array (
						37,
						'- info  -' 
				),
				'error' => array (
						31,
						'- error -' 
				),
				'ok' => array (
						32,
						'- ok    -' 
				) 
		);
		
		if (! array_key_exists ( $type, $typeMap )) {
			throw new InvalidArgumentException ( 'ElephantIOClient::stdout $type parameter must be debug, info, error or success. Got ' . $type );
		}
		
		fwrite ( STDOUT, "[" . $typeMap [$type] [0] . " " . $typeMap [$type] [1] . "  " . $message . "\r\n" );
		//fwrite ( STDOUT, "\033[" . $typeMap [$type] [0] . "m" . $typeMap [$type] [1] . "\033[37m  " . $message . "\r\n" );
	}
	private function generateKey($length = 16) {
		$c = 0;
		$tmp = '';
		
		while ( $c ++ * 16 < $length ) {
			$tmp .= md5 ( mt_rand (), true );
		}
		
		return base64_encode ( substr ( $tmp, 0, $length ) );
	}
	
	/**
	 * Set Handshake timeout in milliseconds
	 *
	 * @param int $delay        	
	 */
	public function setHandshakeTimeout($delay) {
		$this->handshakeTimeout = $delay;
	}
	
	/**
	 * Handshake with socket.io server
	 *
	 * @access private
	 * @return bool
	 */
	private function handshake() {
		$url = $this->socketIOUrl;
		
		if (! empty ( $this->handshakeQuery )) {
			$url .= $this->handshakeQuery;
		}
		
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		if (! $this->checkSslPeer) {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		}
		
		if (! is_null ( $this->handshakeTimeout )) {
			curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT_MS, $this->handshakeTimeout );
			curl_setopt ( $ch, CURLOPT_TIMEOUT_MS, $this->handshakeTimeout );
		}
		
		$res = curl_exec ( $ch );
		
		if ($res === false || $res === '') {
			throw new Exception ( curl_error ( $ch ) );
		}
		
		$sess = explode ( ':', $res );
		$this->session ['sid'] = $sess [0];
		$this->session ['heartbeat_timeout'] = $sess [1];
		$this->session ['connection_timeout'] = $sess [2];
		$this->session ['supported_transports'] = array_flip ( explode ( ',', $sess [3] ) );
		
		if (! isset ( $this->session ['supported_transports'] ['websocket'] )) {
			throw new Exception ( 'This socket.io server do not support websocket protocol. Terminating connection...' );
		}
		
		return true;
	}
	
	/**
	 * Connects using websocket protocol
	 *
	 * @access private
	 * @return bool
	 */
	private function connect() {
		$this->fd = fsockopen ( $this->serverHost, $this->serverPort, $errno, $errstr );
		
		if (! $this->fd) {
			throw new Exception ( 'fsockopen returned: ' . $errstr );
		}
		
		$key = $this->generateKey ();
		
		$out = "GET " . $this->serverPath . "/websocket/" . $this->session ['sid'] . " HTTP/1.1\r\n";
		$out .= "Host: " . $this->serverHost . "\r\n";
		$out .= "Upgrade: WebSocket\r\n";
		$out .= "Connection: Upgrade\r\n";
		$out .= "Sec-WebSocket-Key: " . $key . "\r\n";
		$out .= "Sec-WebSocket-Version: 13\r\n";
		$out .= "Origin: *\r\n\r\n";
		
		fwrite ( $this->fd, $out );
		
		$res = fgets ( $this->fd );
		
		if ($res === false) {
			throw new Exception ( 'Socket.io did not respond properly. Aborting...' );
		}
		
		if (($subres = substr ( $res, 0, 12 )) != 'HTTP/1.1 101') {
			throw new Exception ( 'Unexpected Response. Expected HTTP/1.1 101 got ' . $subres . '. Aborting...' );
		}
		
		while ( true ) {
			$res = trim ( fgets ( $this->fd ) );
			if ($res === '')
				break;
		}
		
		if ($this->read) {
			if ($this->read () != '1::') {
				throw new Exception ( 'Socket.io did not send connect response. Aborting...' );
			} else {
				$this->stdout ( 'info', 'Server report us as connected !' );
			}
		}
		
		// $this->send(self::TYPE_CONNECT);
		$this->heartbeatStamp = time ();
	}
	
	/**
	 * Parse the url and set server parameters
	 *
	 * @access private
	 * @return bool
	 */
	private function parseUrl() {
		$url = parse_url ( $this->socketIOUrl );
		
		$this->serverPath = $url ['path'];
		$this->serverHost = $url ['host'];
		$this->serverPort = isset ( $url ['port'] ) ? $url ['port'] : null;
		
		if (array_key_exists ( 'scheme', $url ) && $url ['scheme'] == 'https') {
			$this->serverHost = 'ssl://' . $this->serverHost;
			if (! $this->serverPort) {
				$this->serverPort = 443;
			}
		}
		
		return true;
	}
}

class ElephantIOPayload
{
	const OPCODE_CONTINUE = 0x0;
	const OPCODE_TEXT = 0x1;
	const OPCODE_BINARY = 0x2;
	const OPCODE_NON_CONTROL_RESERVED_1 = 0x3;
	const OPCODE_NON_CONTROL_RESERVED_2 = 0x4;
	const OPCODE_NON_CONTROL_RESERVED_3 = 0x5;
	const OPCODE_NON_CONTROL_RESERVED_4 = 0x6;
	const OPCODE_NON_CONTROL_RESERVED_5 = 0x7;
	const OPCODE_CLOSE = 0x8;
	const OPCODE_PING = 0x9;
	const OPCODE_PONG = 0xA;
	const OPCODE_CONTROL_RESERVED_1 = 0xB;
	const OPCODE_CONTROL_RESERVED_2 = 0xC;
	const OPCODE_CONTROL_RESERVED_3 = 0xD;
	const OPCODE_CONTROL_RESERVED_4 = 0xE;
	const OPCODE_CONTROL_RESERVED_5 = 0xF;

	private $fin = 0x1;
	private $rsv1 = 0x0;
	private $rsv2 = 0x0;
	private $rsv3 = 0x0;
	private $opcode;
	private $mask = 0x0;
	private $maskKey;
	private $payload;

	public function setFin($fin) {
		$this->fin = $fin;

		return $this;
	}

	public function getFin() {
		return $this->fin;
	}

	public function setRsv1($rsv1) {
		$this->rsv1 = $rsv1;

		return $this;
	}

	public function getRsv1() {
		return $this->rsv1;
	}

	public function setRsv2($rsv2) {
		$this->rsv2 = $rsv2;

		return $this;
	}

	public function getRsv2() {
		return $this->rsv2;
	}

	public function setRsv3($rsv3) {
		$this->rsv3 = $rsv3;

		return $this;
	}

	public function getRsv3() {
		return $this->rsv3;
	}

	public function setOpcode($opcode) {
		$this->opcode = $opcode;

		return $this;
	}

	public function getOpcode() {
		return $this->opcode;
	}

	public function setMask($mask) {
		$this->mask = $mask;

		if ($this->mask == true) {
			$this->generateMaskKey();
		}

		return $this;
	}

	public function getMask() {
		return $this->mask;
	}

	public function getLength() {
		return strlen($this->getPayload());
	}

	public function setMaskKey($maskKey) {
		$this->maskKey = $maskKey;

		return $this;
	}

	public function getMaskKey() {
		return $this->maskKey;
	}

	public function setPayload($payload) {
		$this->payload = $payload;

		return $this;
	}

	public function getPayload() {
		return $this->payload;
	}

	public function generateMaskKey() {
		$this->setMaskKey($key = openssl_random_pseudo_bytes(4));

		return $key;
	}

	public function encodePayload()
	{
		$payload = (($this->getFin()) << 1) | ($this->getRsv1());
		$payload = (($payload) << 1) | ($this->getRsv2());
		$payload = (($payload) << 1) | ($this->getRsv3());
		$payload = (($payload) << 4) | ($this->getOpcode());
		$payload = (($payload) << 1) | ($this->getMask());

		if ($this->getLength() <= 125) {
			$payload = (($payload) << 7) | ($this->getLength());
			$payload = pack('n', $payload);
		} elseif ($this->getLength() <= 0xffff) {
			$payload = (($payload) << 7) | 126;
			$payload = pack('n', $payload).pack('n*', $this->getLength());
		} else {
			$payload = (($payload) << 7) | 127;
			$left = 0xffffffff00000000;
			$right = 0x00000000ffffffff;
			$l = ($this->getLength() & $left) >> 32;
			$r = $this->getLength() & $right;
			$payload = pack('n', $payload).pack('NN', $l, $r);
		}

		if ($this->getMask() == 0x1) {
			$payload .= $this->getMaskKey();
			$data = $this->maskData($this->getPayload(), $this->getMaskKey());
		} else {
			$data = $this->getPayload();
		}

		$payload = $payload.$data;

		return $payload;
	}

	public function maskData($data, $key) {
		$masked = '';

		for ($i = 0; $i < strlen($data); $i++) {
			$masked .= $data[$i] ^ $key[$i % 4];
		}

		return $masked;
	}
}

/**
 * Yunba客户端类
 *
 * @author Liu <q@yun4s.cn>
 * @version 1.0
 */
class Yunba {
	private $_server = "sock.yunba.io";
	private $_port = 3000;
	private $_appKey = "";
	private $_qos0 = 0;
	private $_qos1 = 1;
	private $_qos2 = 2;
	
	/**
	 * 客户端对象
	 * 
	 * @var ElephantIOClient
	 */
	private $_client;

	private $_initCallback;
	private $_recCallback;
	private $_messageCallbacks = array();
	
	private $_callbacks = array();
	private $_callstack = array();
	private $_callback;
	private $_callId = 0;
	
	const VERSION = "1.0";
	
	/**
	 * 构造器
	 * 
	 * @param array $setup 配置选项，包括server（可选）,port（可选）,appkey,debug（可选）四个参数
	 * @throws Exception
	 */
	public function __construct(array $setup) {
		if (isset($setup["server"])) {
			$this->_server = $setup["server"];
		}
		if (isset($setup["port"])) {
			$this->_server = $setup["port"];
		}
		if (isset($setup["appkey"])) {
			$this->_appKey = $setup["appkey"];
		}
		else {
			throw new Exception("Need 'appkey' option");
		}
		
		$this->_client = new ElephantIOClient("http://" . $this->_server . ":" . $this->_port, "socket.io", 1, false, true, isset($setup["debug"]) ? $setup["debug"] : false);
	}
	
	/**
	 * 初始化
	 * 
	 * @param callable $initCallback 初始化结果回调
	 * @param callable $recCallback 重新连接回调
	 */
	public function init($initCallback = null, $recCallback = null) {
		if (is_callable($initCallback)) {
			$this->_initCallback = $initCallback;
		}
		if (is_callable($recCallback)) {
			$this->_recCallback = $recCallback; 
		}
		
		$this->on("socketconnectack", array($this, "_initCallbackMethod"));
		$this->on("connack", array($this, "_connectCallbackMethod"));
		$this->on("disconnect", array($this, "_reconnectCallbackMethod"));
		$this->on("error", array($this, "_disconnectCallbackMethod"));
		$this->on("reconnect", array($this, "_disconnectCallbackMethod"));
		$this->on("reconnect_failed", array($this, "_disconnectCallbackMethod"));
		$this->on("suback", array($this,  "_subscribeCallbackMethod"));
		$this->on("message", array($this, "_messageCallbackMethod"));
		$this->on("puback", array($this, "_publishCallbackMethod"));
		$this->on("unsuback", array($this, "_unsubscribeCallbackMethod"));
		$this->_client->init();
	}
	
	/**
	 * 连接
	 * 
	 * @param callable $callback 回调
	 */
	public function connect($callback = null) {
		$this->emit("connect", array(
			"appkey" => $this->_appKey
		), $callback);
	}
	
	/**
	 * 断开连接
	 * 
	 * @param callable $callback 回调
	 */
	public function disconnect($callback = null) {
		$this->emit("disconn", array(), $callback);
	}
	
	/**
	 * 触发事件
	 * 
	 * @param string $event 事件名
	 * @param array $args 参数
	 * @param callable $callback 回调
	 */
	public function emit($event, array $args, $callback = null) {
		$this->_callId ++;
		if (is_callable($callback)) {
			$this->_callbacks[$this->_callId] = $callback;
		}
		
		$this->_client->emit($event, $args, null, array($this, "push_callback_" . $this->_callId));
	}
	
	/**
	 * 监听事件
	 * 
	 * @param string $event 事件名
	 * @param callable $callback 回调
	 */
	public function on($event, $callback) {
		$this->_client->on($event, $callback);
	}
	
	/**
	 * 订阅
	 * 
	 * @param array $args 参数，包括topic, qos两个参数
	 * @param callable $subscribeCallback 订阅结果回调
	 * @param callable $messageCallback 消息接收回调
	 */
	public function subscribe (array $args, $subscribeCallback = null, $messageCallback = null) {
		$channel = isset($args["topic"]) ? $args["topic"] : "";
		$qos = isset($args["qos"]) ? $args["qos"] : $this->_qos1;
		
		if (is_callable($messageCallback)) {
			if (!isset($this->_messageCallbacks[$channel])) {
				$this->_messageCallbacks[$channel] = array();
			}
			$this->_messageCallbacks[$channel][] = $messageCallback;
		}
		
		$this->emit("subscribe", array(
			"topic" => $channel,
			"qos" => $qos	
		), $subscribeCallback);
	}
	
	/**
	 * 取消订阅
	 * 
	 * @param array $args 参数，包括topic一个参数
	 * @param callable $callback 回调
	 */
	public function unsubscribe (array $args, $callback = null) {
		$channel = isset($args) ? $args["topic"] : "";
		$this->emit("unsubscribe", array(
			"topic" => $channel
		), $callback);
	}
	
	/**
	 * 发布消息
	 * 
	 * @param array $args 参数，包括topic, msg, qos三个参数
	 * @param callable $callback 回调
	 */
	public function publish (array $args, $callback = null) {
		$channel = isset($args["topic"]) ? $args["topic"] : "";
		$msg = isset($args["msg"]) ? $args["msg"] : "";
		$qos = isset($args["qos"]) ? $args["qos"] : $this->_qos1;
		$this->emit("publish", array(
			"topic" => $channel,
			"msg" => $msg,
			"qos" => $qos		
		), $callback);
	}
	
	/**
	 * 等待通讯
	 */
	public function wait() {
		$this->_client->keepAlive();
	}
	
	private function _fetchCallback() {
		return array_shift($this->_callstack);
	}
	
	public function _initCallbackMethod() {
		if ($this->_initCallback) {
			call_user_func($this->_initCallback, true);
		}
	}
	
	public function _connectCallbackMethod($data) {
		$callback = $this->_fetchCallback();
		if ($callback) {
			call_user_func($callback, $data["success"], isset($data["msg"]) ? $data["msg"] : null);
		}
	}
	
	public function _disconnectCallbackMethod($data) {
		$callback = $this->_fetchCallback();
		if ($callback) {
			call_user_func($callback, true);
		}
	}
	
	public function _reconnectCallbackMethod() {
		if ($this->_recCallback) {
			call_user_func($this->_recCallback);
		}
	}
	
	public function _subscribeCallbackMethod($data) {
		$callback = $this->_fetchCallback();
		if ($callback) {
			call_user_func($callback, $data["success"], isset($data["msg"]) ? $data["msg"] : null);
		}
	}
	
	public function _messageCallbackMethod($data) {
		$topic = $data["topic"];
		if (!empty($this->_messageCallbacks[$topic])) {
			foreach ($this->_messageCallbacks[$topic] as $callback) {
				call_user_func($callback, $data);
			}
		}
	}
	
	public function _publishCallbackMethod($data) {
		$callback = $this->_fetchCallback();
		if ($callback) {
			if ($data["success"]) {
				call_user_func($callback, true, array(
					"messageId" => $data["messageId"]
				));
			}
			else {
				call_user_func($callback, false);
			}
		}
	}
	
	public function _unsubscribeCallbackMethod($data) {
		$callback = $this->_fetchCallback();
		if ($callback) {
			call_user_func($callback, $data["success"]);
		}
	}
	
	public function __call($method, $args) {
		if (preg_match("/^push_callback_(\\d+)$/", $method, $match)) {
			$callId = $match[1];
			if (isset($this->_callbacks[$callId])) {
				$this->_callstack[] = $this->_callbacks[$callId];
				unset($this->_callbacks[$callId]);
			}
		}
	}
}

?>