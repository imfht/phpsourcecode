<?php

namespace app\pay\controller;

//require_once EXTEND_PATH . 'Workerman/Workerman/Autoloader.php';

use \think\Db;
use \think\Env;

use \Workerman\Worker;
use \Workerman\Lib\Timer;

\think\Loader::addNamespace([
	'Workerman' => EXTEND_PATH . 'Workerman/Workerman',
]);

define('TIME_HEARTBEAT', 20);

global $worker;

$worker = new Worker('websocket://0.0.0.0:' . Env::get('socket.http_port'));
$worker->count = 1;
$worker->Client = [];

$worker->onMessage = function($conn, $data) use($worker) {
	console_log('Receive: ' . $data);
	!isset($conn->SN) && $conn->SN = '';
	!isset($conn->version) && $conn->version = '';
	!isset($conn->person_id) && $conn->person_id = 0;
	!isset($conn->device_id) && $conn->device_id = 0;
	$data = json_decode($data);
	if(!empty($data)) {
		$data = ToObject($data);
		$data->SN = !empty($data->SN) ? $data->SN : '';
		$data->version = !empty($data->version) ? $data->version : '';
		$data->person_id = !empty($data->person_id) ? $data->person_id : 0;
		if(!empty($data->SN)) {
			if(empty($conn->SN) || ($conn->person_id != $data->person_id)) {
				$device = Db::name('store_device')->where('SN', '=', $data->SN)->find();
				if($device) {
					$info = ToObject([
						'SN' => $data->SN,
						'version' => $data->version,
						'person_id' => $data->person_id,
						'device_id' => $device['device_id'],
					]);
					login($conn, $info);
				}
			}
			if(!empty($conn->device_id)) {
				$conn->last = _time();
				$conn->send(JSON(['time' => gsdate('Y-m-d H:i:s')]));
				console_log('Send: ' . JSON(['time' => gsdate('Y-m-d H:i:s')]));
			}
		}
	}
};

$worker->onConnect = function($conn) use($worker) {
	Db::name('store_device')->where('SN', '=', '')->update(['version' => '', 'person_id' => 0]);
	//$conn->ip = $conn->getRemoteIp();
	//$conn->send(JSON(['time' => gsdate('Y-m-d H:i:s')]));
};

$worker->onClose = function($conn) use($worker) {
	logout($conn);
};

$worker->onWorkerStart = function($worker) {
	Timer::add(1, function() use($worker) {
		$time = _time();
		foreach($worker->connections as $conn) {
			if(empty($conn->last)) {
				$conn->last = $time;
			} else {
				if(($time - $conn->last) > TIME_HEARTBEAT) {
					$conn->close();
				}
			}
		}
	});
	$text_worker = new Worker('Text://0.0.0.0:' . Env::get('socket.text_port'));
	$text_worker->onMessage = function($conn, $data) {
		console_log('Send: ' . $data);
		$data = json_decode($data, true);
		$res = sendToUid($data['device_id'], JSON($data['message']));
		if($res) {
			$conn->send('ok');
		} else {
			$conn->send('fail');
		}
	};
	$text_worker->listen();
};

function sendToAll($message) {
	global $worker;
	foreach($worker->Client as $uid => $connection) {
		$connection->send($message);
	}
}

function sendToUid($uid, $message) {
	global $worker;
	if(isset($worker->Client[$uid])) {
		$worker->Client[$uid]->send($message);
		return true;
	}
	return false;
}

function login($conn, $info = null) {
	global $worker;
	if(!empty($info->device_id)) {
		console_log('Login: ' . JSON(['version' => $info->version, 'person_id' => $info->person_id, 'SN' => $info->SN]));
		$conn->SN = $info->SN;
		$conn->version = $info->version;
		$conn->person_id = $info->person_id;
		$conn->device_id = $info->device_id;
		$worker->Client[$info->device_id] = $conn;
		Db::name('store_device')->where('device_id', '=', $info->device_id)->update([
			'version' => $info->version,
			'person_id' => $info->person_id,
			'Login_Time' => gsdate('Y-m-d H:i:s')
		]);
	}
}

function logout($conn, $info = null) {
	global $worker;
	if(!empty($conn->device_id)) {
		console_log('Logout: ' . JSON(['version' => $conn->version, 'person_id' => $conn->person_id, 'SN' => $conn->SN]));
		if(isset($worker->Client[$conn->device_id])) {
			unset($worker->Client[$conn->device_id]);
		}
		Db::name('store_device')->where('device_id', '=', $conn->device_id)->update([
			'version' => '',
			'person_id' => 0,
			'Logout_Time' => gsdate('Y-m-d H:i:s')
		]);
	}
}

function console_log($contents = null) {
	if(!empty($contents)) {
		if(!is_array($contents) && !is_object($contents)) {
			echo '[' . gsdate('Y-m-d H:i:s') . ']' . ' ' . $contents . "\n";
		} else {
			echo '[' . gsdate('Y-m-d H:i:s') . ']' . ' ' . JSON($contents) . "\n";
		}
	}
}

Worker::runAll();

