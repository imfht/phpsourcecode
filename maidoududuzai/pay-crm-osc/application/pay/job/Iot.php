<?php

namespace app\pay\job;

use \think\Db;
use \think\Env;
use \think\queue\Job;

class Iot
{

	public function fire(Job $job, $data = [])
	{

	}

	public function init(Job $job, $data)
	{
		
	}

	public function test(Job $job, $data)
	{
		
	}

	// 二维码语音播报
	public function qrcode_speech(Job $job, $data)
	{
		echo Tool::show($data);
		if(!empty($data['device_id']) && !empty($data['text']) && !empty($data['amount'])) {
			try {
				$client = stream_socket_client('tcp://127.0.0.1:' . Env::get('socket.text_port'), $errno, $errmsg, 1);
				$res = [
					'device_id' => $data['device_id'],
					'status' => '',
					'message'=> [
						'command' => 'speech',
						'contents' => [
							'text' => $data['text'],
							'amount' => $data['amount'],
						],
					],
				];
				$data = JSON($res);
				fwrite($client, $data . "\n");
				$res['status'] = fread($client, 2048);
				echo Tool::show($data);
			} catch (\Exception $e) {
				echo Tool::show($e->getMessage());
			}
		}
		$job->delete();
	}

}

