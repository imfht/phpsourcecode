<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: colin <colin@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace com;

use GuzzleHttp\Client;
use think\facade\Env;

/**
 * @title 版本管理
 * @description 版本管理类
 */
class Version{

	public $client = "";
	public $headers = "";

	public function __construct(){
		$this->headers = [
			// 'Content-Type' => 'application/json',
			// 'Authorization'     => 'Bearer ' . Env::get('test.token'),
		];
		$this->client = new Client([
			'base_uri' => 'http://www.tensent.cn/',
			'timeout'  => 2.0,
		]);
	}

	public function check(){
		$param = [
			'client_url' => 'https://www.com',
			'dd' => 'dd'
		];
		try {
			$response = $this->client->post('api/version/index', [
				'form_params' => $param,
				'headers' => $this->headers,
			]);
			$ret = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
			if($ret['code'] == 1){
				$version = Env::get('version');
				$data = $ret['data'];
				if(version_compare($version, $data['version'], '<')){
					$data['update'] = 1;
				}
				return $data;
			}
		} catch (\Exception $e) {
			//throw $th;
		}
	}

	/**
	 * @title 下载升级包
	 */
	public function downloadZip(){

	}

	/**
	 * @title 解压升级包
	 */
	public function unzipFile(){

	}
}