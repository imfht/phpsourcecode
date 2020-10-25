<?php
namespace app;

use app\libs\Socket;
use app\helpers\Logger;
use app\helpers\Data;

//下面是sock类
class Boot{
	
	/**
	 * 客户端连接数限制，
	 */
	protected $maxClientNumber = 200;
	
	public function __construct($host, $port){
		Socket::create($host, $port);
	}

	function run() {
		$userManager = \app\manager\User::instance();
		$roomManager = \app\manager\Room::instance();
		
		$master = Socket::getMaster();

		while(true) {
			$changes = Socket::getAll();
			$write = $except = NULL;
			$res = @ socket_select($changes, $write, $except, 1);
			
			$roomManager->runTask();

			if(empty($changes)) continue;
			
			foreach($changes as $sock) {
				if($sock == $master) {
					$client = @ socket_accept($master);
					
					if(!$client) continue;
					
					$uid = $userManager->create($client);
					Socket::add($uid, $client);
					continue;
				}
			
				$len = 0;
				$buffer = '';

				do {
					$l = socket_recv($sock, $buf, 1000, 0);
					$len += $l;
					$buffer .= $buf;
				} while($l == 1000);
				
				$user = $userManager->setCurrent($sock);
				
				if(!$user) {
					Logger::add("Not find socket!");
					continue;
				}

				//如果接收的信息长度小于7，则该client的socket为断开连接
				if($len < 7) {
					Socket::del($user->id);
					continue;
				}
				
				// 握手
				if(!$user->isHandshake) {
					$user->handshake($buffer);
					Logger::add("User ".$user->id." handshake");
					
					if( Socket::getClientNumber() > $this->maxClientNumber ) {
						Socket::write($user->sock, ['type'=>'errorMsg', 'msg'=>'服务器爆满！休息片刻吧。']);
						Socket::del($user->id);
					}
					continue;
				}
				
				$buffer = Data::decode($buffer);
				$buffer && $buffer = @ json_decode($buffer, true);
				if($buffer == false) {
					continue;
				}

				$router = new \app\libs\Router();
				$router->run($buffer);
				$router = null;
			}
		}
	}
}