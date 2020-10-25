<?php

namespace HypoFan\gateway;

use Workerman\Worker;
use GatewayWorker\Register;
use GatewayWorker\Gateway;
use GatewayWorker\BusinessWorker;

/**
 * Gateway-Worker控制器扩展类
 * @author Hypo Fan
 * @abstract
 */
abstract class GatewayServer {
	
	const VERSION = '1.0.0';
	
	/**
	 * 通过setRegister设置的RegisterWorker数组集合，数组键名为设置的name
	 * @var array
	 */
	protected $registerWorkers;
	/**
	 * 通过setGateway设置的GatewayWorker数组集合，数组键名为设置的name
	 * @var array
	 */
	protected $gatewayWorkers;
	/**
	 * 通过setBusiness设置的BusinessWorker数组集合，数组键名为设置的name
	 * @var type 
	 */
	protected $businessWorkers;
	
	/**
	 * RegisterWorker、GatewayWorker和BusinessWorker之间的通信密钥
	 * @var string 
	 */
	protected $secretKey;
	
	/**
	 * 事件处理类，默认是当前类
	 * @var string 
	 */
	protected $eventHandle;
	
	public function __construct() {
		$this->eventHandle = get_class($this);
		$this->start();
		$this->init();
		Worker::runAll();
	}
	
	/**
	 * 初始化
	 */
	protected function init() {}

	/**
	 * 启动Worker，必需重写该方法，setRegister、setGateway和setBusiness建议在该方法中使用
	 */
	abstract protected function start();

	/**
	 * 设置RegisterWorker
	 * @param string $name RegisterWorker进程的名称
	 * @param int $count 进程数，默认1
	 * @param string $port 监听端口，默认1238
	 * @param string $ip 监听IP，默认0.0.0.0，即监听当前服务器所有网卡
	 * @return void 无返回值
	 */
	protected function setRegister($name, $count = 1, $port = '1238', $ip = '0.0.0.0') {
		$register = new Register('text://' . $ip . ':' . $port);
		$register->name = $name;
		$register->count = $count;
		if ($this->secretKey) {
			$register->secretKey = $this->secretKey;
		}
		$this->registerWorkers[$name] = $register;
	}
	
	/**
	 * 设置GatewayWorker
	 * @param string $protocol Gateway的通讯协议，格式为protocol://ip:port
	 * @param string $name GatewayWorker进程的名称
	 * @param array $config GatewayWorker配置，详见http://workerman.net/gatewaydoc/gateway-worker-development/gateway.html（Gateway类可以定制的内容）
	 * @return void 无返回值
	 */
	protected function setGateway($protocol, $name = 'GatewayWorker', $config = []) {
		$gateway = new Gateway($protocol);
		$gateway->name = $name;
		foreach ($config as $key => $val) {
			$gateway->$key = $val;
		}
		if ($this->secretKey) {
			$gateway->secretKey = $this->secretKey;
		}
		$this->gatewayWorkers[$name] = $gateway;
	}
	
	/**
	 * 设置BusinessWorker
	 * @param string $name BusinessWorker进程的名称
	 * @param array $config BusinessWorker配置，详见http://workerman.net/gatewaydoc/gateway-worker-development/business-worker.html（BusinessWorker类可以定制的内容）
	 * @return void 无返回值
	 */
	protected function setBusiness($name, $config = []) {
		$business = new BusinessWorker();
		$business->name = $name;
		$business->eventHandler = $this->eventHandle;
		foreach ($config as $key => $val) {
			$business->$key = $val;
		}
		if ($this->secretKey) {
			$business->secretKey = $this->secretKey;
		}
		$this->businessWorkers[$name] = $business;
	}
	
	/**
	 * 当BusinessWorker进程启动时触发的回调函数
	 * @param BusinessWorker $businessWorker BusinessWorker进程实例
	 * @return void 无返回值
	 */
	public static function onWorkerStart(BusinessWorker $businessWorker) {}
	
	/**
	 * 当客户端连接上gateway进程时触发的回调函数
	 * @param string $client_id 客户端ID
	 * @return void 无返回值
	 */
	public static function onConnect(string $client_id) {}
	
	/**
	 * 当收到客户端请求后触发的回调函数
	 * @param string $client_id 客户端ID
	 * @param mixed $data 完整的客户端请求数据，数据类型取决于Gateway所使用协议的decode方法的返回值类型
	 * @return void 无返回值
	 */
	abstract public static function onMessage(string $client_id, $data);
	
	/**
	 * 当客户端与Gateway的连接断开时触发的回调函数
	 * @param string $client_id 客户端ID
	 */
	public static function onClose(string $client_id) {}
	
	/**
	 * 当businessWorker进程退出时触发的回调函数
	 * @param BusinessWorker $businessWorker BusinessWorker进程实例
	 */
	public static function onWorkerStop(BusinessWorker $businessWorker) {}
	
}
