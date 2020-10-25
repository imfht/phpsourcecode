<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\model;

/**
 * 微信
 * @author sigmazel
 * @since v1.0.2
 */
class _wx{
	//获取token
	public function token($appid, $secret, $refresh = false){
	    $data = json_decode(file_get_contents(ROOTPATH.'/_cache/token/access_token.json'), 1);
	    
	    if($data['expire_time'] + 0 < time() || $refresh){
	    	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
	    	$res = json_decode($this->request($url), 1);
	    	$access_token = $res['access_token'];
	    	
	    	if($access_token){
	    		$data['expire_time'] = time() + 4000;
	    		$data['access_token'] = $access_token;
	    		
	    		if(!is_dir(ROOTPATH.'/_cache/token')){
					$res = @mkdir(ROOTPATH.'/_cache/token', 0777, true);
					@chown(ROOTPATH.'/_cache/token', 'apache');
				}
				
	    		$fp = fopen(ROOTPATH.'/_cache/token/access_token.json', 'w+');
	    		fwrite($fp, json_encode($data));
	    		fclose($fp);
	    	}
	    }else $access_token = $data['access_token'];
	    
	    return $access_token;
	}
	
	//http请求
	public function http($url){
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 50);
		
		if(substr($url, 0, 5) == 'https'){
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		
		$res = curl_exec($curl);
		curl_close($curl);
	
		return $res;
	}
	
	//http带文件请求
	public function http_pager($url, $multipart){
		if(!$url || !$multipart['name'] || !$multipart['filename'] || !$multipart['path']) return json_encode(array('errno' => 100, 'errstr' => '参数错误！'));
		
		$bits = parse_url($url);
	    $host = $bits['host'];
	    $port = isset($bits['port']) ? $bits['port'] : 80;
	    $path = isset($bits['path']) ? $bits['path'] : '/';
	    $path .= '?'.$bits['query'];
	    
		$fsockopen = fsockopen($host, $port, $errno, $errstr, 30); 
	    if(!$fsockopen) return json_encode(array('errno' => $errno, 'errstr' => $errstr));
	    
	    srand((double) microtime() * 1000000);
	    $boundary = md5(time());
	    
	    $data = '--'.$boundary."\r\n";
	    $data .= "content-disposition: form-data; name=\"{$multipart[name]}\"; filename=\"{$multipart[filename]}\"\r\n"; 
	    $data .= "content-type: application/x-www-form-urlencoded\r\n\r\n"; 
	    $data .= file_get_contents($multipart['path'])."\r\n"; 
	    $data .= '--'.$boundary."\r\n"; 
	    $data .= "--\r\n\r\n"; 
	    
	    fwrite($fsockopen, "POST {$path} http/1.1\r\n"); 
	    fwrite($fsockopen, "host: {$host}\r\n"); 
	    fwrite($fsockopen, "content-type: multipart/form-data; boundary=".$boundary."\r\n"); 
	    fwrite($fsockopen, "content-length: ".strlen($data)."\r\n"); 
	    fwrite($fsockopen, "connection: close\r\n\r\n"); 
	    
	    fputs($fsockopen, $data); 
	    $response = ''; 
	    
		while(!feof($fsockopen)){
			$response .= fgets($fsockopen, 1024);
		}
		
		fclose($fsockopen); 
		
		$pos = strpos($response, "\r\n\r\n"); 
		$response = substr($response, $pos+4); 
		
		return $response; 
	}
	
	//http带参数请求
	public function request($url, $params = '', $type = 'GET'){
		$curl = curl_init();
	
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_POST, $type == 'POST' ? 1 : 0);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	
		$result = curl_exec($curl);
		if(!$result) $result = curl_error($curl);
	
		curl_close($curl);
	
		return $result;
	}
	
	//返回文本消息
	public function response2text($arr){
	$xml = "<xml>
<ToUserName><![CDATA[{$arr[ToUserName]}]]></ToUserName>
<FromUserName><![CDATA[{$arr[FromUserName]}]]></FromUserName>
<CreateTime>{$arr[CreateTime]}</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[{$arr[Content]}]]></Content>
</xml>";
	return $xml;
	}
	
	//返回音乐消息
	public function response2music($arr){
	return "<xml>
<ToUserName><![CDATA[{$arr[ToUserName]}]]></ToUserName>
<FromUserName><![CDATA[{$arr[FromUserName]}]]></FromUserName>
<CreateTime>{$arr[CreateTime]}</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
<Music>
<Title><![CDATA[{$arr[Title]}]]></Title>
<Description><![CDATA[{$arr[Description]}]]></Description>
<MusicUrl><![CDATA[{$arr[MusicUrl]}]]></MusicUrl>
<HQMusicUrl><![CDATA[{$arr[HQMusicUrl]}]]></HQMusicUrl>
</Music>
</xml>";
	}
	
	//返回图文消息
	public function response2news($news){
	$xml = "<xml>
<ToUserName><![CDATA[{$news[ToUserName]}]]></ToUserName>
<FromUserName><![CDATA[{$news[FromUserName]}]]></FromUserName>
<CreateTime>{$news[CreateTime]}</CreateTime>
<MsgType><![CDATA[news]]></MsgType>";
	
	$xml .= "<ArticleCount>".count($news['Items'])."</ArticleCount><Articles>";
	foreach($news['Items'] as $key => $article){
		$xml .= "<item>
<Title><![CDATA[{$article[Title]}]]></Title> 
<Description><![CDATA[{$article[Description]}]]></Description>
<PicUrl><![CDATA[{$article[PicUrl]}]]></PicUrl>
<Url><![CDATA[{$article[Url]}]]></Url>
</item>";
	}
	
	$xml .= "</Articles></xml>";
	return $xml;
	}
	
	//返回客服消息
	public function response_service($postObj){
		$xml = "<xml>
<ToUserName><![CDATA[{$postObj[FromUserName]}]]></ToUserName>
<FromUserName><![CDATA[{$postObj[ToUserName]}]]></FromUserName>
<CreateTime>{$postObj[CreateTime]}</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
		
		exit_echo($xml);
	}
	
	//返回消息
	public function response_message($postObj, $message){
		$arr = array(
		'ToUserName' => $postObj['FromUserName'],
		'FromUserName' => $postObj['ToUserName'],
		'CreateTime' => time(),
		'Content' => $message);
		
		exit_echo($this->response2text($arr));
	}
	
	//返回自动回复消息
	public function response_auto($postObj){
		global $setting, $wx_setting;
		
		if($wx_setting['AUTOTYPE'] == 1){
			$arr = array(
			'ToUserName' => $postObj['FromUserName'],
			'FromUserName' => $postObj['ToUserName'],
			'CreateTime' => time(),
			'Content' => strip_tags($wx_setting['AUTOTEXT']));
			 
			exit_echo($this->response2text($arr));
		}
		
		if($wx_setting['AUTOTYPE'] == 2){
			$news = array(
			'ToUserName' => $postObj['FromUserName'],
			'FromUserName' => $postObj['ToUserName'],
			'CreateTime' => time(),
			'Items' => array(
				array(
				'Title' => $wx_setting['AUTOTITLE'],
				'Description' => $wx_setting['AUTODESCRIPTION'], 
				'PicUrl' => $setting['SiteHost'].'/'.$wx_setting['AUTOPIC'][0], 
				'Url' =>  strexists($wx_setting['AUTOURL'], 'http://') ? $wx_setting['AUTOURL'] : $setting['SiteHost'].'/'.$wx_setting['AUTOURL']
				)
			));
			 
			exit_echo($this->response2news($news));
		}
	}
	
	//返回文章图文消息
	public function response_articles($articles, $postObj){
		global $setting;
		
		$news = array(
		'ToUserName' => $postObj['FromUserName'],
		'FromUserName' => $postObj['ToUserName'],
		'CreateTime' => time(), 
		'Items' => array());
		
		foreach($articles as $akey => $article){
			$tmpitem = array('Title' => '', 'Description' => '', 'PicUrl' => '', 'Url' => '');
			$tmpitem['Title'] = $article['TITLE'];
			$tmpitem['Description'] = cutstr(strip_tags($article['SUMMARY']), 120);
			
			if(is_array($article['FILE01'])) $tmpitem['PicUrl'] = $akey == 0 ? $setting['SiteHost'].'/'.$article['FILE01'][3] : $setting['SiteHost'].'/'.$article['FILE01'][0];
			$tmpitem['Url'] = "{$setting[SiteHost]}mobile.do?ac=api&op=text&id={$article[ARTICLEID]}";
			
			$news['Items'][] = $tmpitem;
			
			unset($tmpitem);
		}
		
		exit_echo($this->response2news($news));
	}
	
	
}
?>