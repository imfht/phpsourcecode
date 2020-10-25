<?php

namespace app\mock\controller;

class Config
{
	public function index(){
		$file_path = __DIR__.DS.'..'.DS.'mockcfg.php';
		$status_map = ['200', '200/0', '200/text', '500/json', '500/text', 'timeout'];
		if(!\file_exists($file_path)){
			$data = [
				"active"=> "1",
				"config"=> [
					"pay"=> [
						"cash"=> [
							"pay"=> "0",
							"refund"=> "0",
							"index"=> "0",
							"query"=> "0",
							"check"=> "0",
							"slider"=> "0",
							"login"=> "0",
							"order"=> "0"
						],
						"mall"=> [
							"pay_order"=> "0",
							"index"=> "0",
							"goods_cat"=> "0",
							"goods"=> "0",
							"goods_detail"=> "0"
						],
						"user"=> [
							"alipay_card_activateurl"=> "0",
							"alipay_card_userinfo"=> "0",
							"alipay_card_open"=> "0"
						]
					],
					"home"=> [
						"url"=> [
							"create"=> "0"
						]
					]
				]
			];
			\file_put_contents($file_path, \json_encode($data));
		}
		$mockcfg = \json_decode(file_get_contents($file_path), true);

		if(request()->isAjax()){
			$active = input('active/s');
			if(isset($active)){
				$mockcfg['active'] = $active ? '1' : '0';
			}else{
				$module = input('module/s');
				if(!$module) return \make_json(0, '缺少参数module');
				$controller = input('controller/s');
				if(!$controller) return \make_json(0, '缺少参数controller');
				$action = input('action/s');
				if(!$action) return \make_json(0, '缺少参数action');
				$status = input('status/s');
				if(!\in_array($status, \array_keys($status_map))){
					return \make_json(0, 'status参数不合法');
				}
				$mockcfg['config'][$module][$controller][$action] = $status;
			}
			$res = \file_put_contents($file_path, \json_encode($mockcfg));
			if(!$res) return \make_json(0, '修改失败');
			return \make_json(1, 'ok');
		}
		include \befen\view();
	}
}