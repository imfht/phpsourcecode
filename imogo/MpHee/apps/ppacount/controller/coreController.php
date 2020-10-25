<?php
class coreController extends baseController{	
	
	public function wechat(){
		$hash = $_GET['hash'];
		$ppacountinfo = $this->model->table('ppacount')->where( array('hash'=>$hash) )->find();
		$options = array(
		    'token'=>$ppacountinfo['token'], //填写你设定的key
			'encodingaeskey'=>$ppacountinfo['encodingaeskey'], //填写加密用的EncodingAESKey
		);
		$weObj = new Wechat($options);
		$weObj->valid();
		$data['ppid'] = $ppacountinfo['id'];
		$data['revdata'] = $weObj->getRev()->getRevData();
		$type = $weObj->getRev()->getRevType();
		
		$reply = api(getApps(),'Reply',$data);
		
		foreach ($reply as $key=>$value){
		    if (!empty($value)){
			    if(is_array($value)){
				    $weObj->news($value)->reply();
					break;
				}else{
				    $weObj->text($value)->reply();
					break;
				}
				break;
			}
		}
	}
	
	public function fuwuc(){
		$options = array(
		    'appid'=>'2014050400005583',
		);
		$fuwu = new AlipayFuwu($options);
		$service = $fuwu->getRev()->getService();
		if( $service == "alipay.service.check" ){
			$fuwu->getRev()->valid();//这个是验证消息用的
		}else{		
		$biz = $fuwu->getRev()->getRevText();
		//$fuwu->Message($biz);
		/*$articles_arr = array( array (
				'actionName' => iconv ( "UTF-8", "GBK", '立即购买' ),
				'desc' => iconv ( "UTF-8", "GBK", '欢迎来到美丽的合肥' ),
				'imageUrl' => 'http://www.baidu.com/img/bdlogo.png',
				'title' => iconv ( "UTF-8", "GBK", '这个是标题' ),
				'url' => 'http://www.baidu.com',
				'authType' => 'loginAuth' 
		) );*/
		//$a = file_get_contents('http://www.xiaodoubi.com/bot/api.php?chat='.$biz);
		$aa = $fuwu->getGis();
		$json = file_get_contents("http://api.map.baidu.com/telematics/v3/weather?location=".$aa['city']."&output=json&ak=44dc390653b2d7382f6fbbdc71879674");
		$jsonarray = json_decode($json,true);
		$aaa = $aa['city'].'今天天气：\r\n\n'.$jsonarray['results'][0]['weather_data'][0]['date'].'\r\n'.$jsonarray['results'][0]['weather_data'][0]['weather'].'\r\n'.$jsonarray['results'][0]['weather_data'][0]['wind'].'\r\n'.$jsonarray['results'][0]['weather_data'][0]['temperature'];
		$fuwu->text($aaa)->reply();
		}
	}
	
	public function user(){
		$options = array(
		    'appid'=>'2014050400005583',
		);
		$fuwu = new AlipayFuwu($options);
		
		$code = $_GET['auth_code'];
		$appid = $_GET['app_id'];
		
		$this->us = $fuwu->getUserInfo($code);
		
		$this->display();
	}
	
	public function notify(){
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		logger('a',$postStr);
	}
	
	public function wepay(){
		$options = array(
 			'appid'=>'wxbd6a21f015769dca', //填写高级调用功能的app id
 			'appsecret'=>'460db9297eff4c70ed09a173615c1983', //填写高级调用功能的密钥
 			'partnerid'=>'1220189601', //财付通商户身份标识
 			'partnerkey'=>'14a8e142c8ae662af79057f8ecbdf889', //财付通商户权限密钥Key
 			'paysignkey'=>'NfKiezStebcNV7YWnn6CVKJI95sugW5MkruCnqMNuG0jL4sc2SmIYkk0OTH6APHg8X9E07tOU1zH6De621e7JLF0mcHn6G75uEFuTK7w166Ltzr9vWs3mp7119MTAZiY' //商户签名密钥Key
 		);
		
		$weObj = new Wechat($options);
		
		$out_trade_no = getcode('30');
		
		$notify_url = urlencode( 'http://tteam.chinacloudapp.cn'.url('core/notify') );
		
		$this->page = $weObj->createPackage($out_trade_no,'这是具体描述','1',$notify_url,'127.0.0.1');
		
		
		$this->display();
	}
	
}