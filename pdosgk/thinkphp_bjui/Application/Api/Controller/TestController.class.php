<?php
namespace Api\Controller;
use Think\Controller;
class TestController extends Controller {
	public function index(){
		//记录code
		$code = I('get.code');
		if($code){
			$client_id = urlencode('5a977ff1d594f150f728f5d5');
			$client_secret = urlencode('23f3080023d5438ba39ceab86a0ca110');
			// $code = urlencode('111222333444555666');
			$redirect_uri = urlencode('https://mini.pdosgk.com/wish/index');

			$url = 'https://sandbox.merchant.wish.com/api/v2/oauth/access_token';
			$params['client_id'] = $client_id;
			$params['client_secret'] = $client_secret;
			$params['code'] = $code;
			$params['grant_type'] = 'authorization_code';
			$params['redirect_uri'] = $redirect_uri;

			$content = getCurlData($url, $params, 'post');
			$response_data = json_decode($content, true);
			if($response_data['code'] == 0){
				session('access_token', $response_data['data']['access_token']);
				session('refresh_token', $response_data['data']['refresh_token']);
			}
			var_dump($response_data);
		}
}
	public function get_access_token(){
		echo session('access_token');
		$url = 'https://sandbox.merchant.wish.com/api/v2/auth_test';
		$params['access_token'] = session('access_token');
		$content = getCurlData($url, $params, 'post');
		$response_data = json_decode($content, true);

		var_dump($response_data);
	}


	//保存post信息
	public function save($data = null) {
		if (!$data) {
			if (IS_POST) {
				$data = file_get_contents("php://input"); //接收post数据
			} else {
				$data = $_GET;
			}
		}
		//$file_in = file_get_contents("php://input"); //接收post数据
		$info['refer'] = $_SERVER['REQUEST_URI'];
		$info['data'] = serialize($data);
		$info['create_time'] = NOW_TIME;
		$info['ip'] = get_client_ip(0, true);
		$result = M('post_log')->add($info);

	}
	public function _curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$txt = curl_exec($ch);
		if (curl_errno($ch)) {
			return false;
		}
		curl_close($ch);
		return json_decode($txt, true);
	}
	
}