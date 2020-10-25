<?php
final class Sendweixin {

	public $openids;      //openid数组必须是经过serialize()函数处理过的
	public $title='';		//内容标题
	public $site_url='';		//图文链接地址
	public $content='';	//内容
	public $picurl='';		//图片地址
	public $type;   //发送类型  1：文本  2：图文

	public function send_weixin(){

		$postUrl = "http://fyunoa.duapp.com/api.php";  //api接口地址

	    $parms = "?openid=".$this->openids."&title=".urlencode($this->title)."&site_url=".urlencode($this->site_url)."&content=".urlencode($this->content)."&type=".$this->type."&picurl=".$this->picurl;  

		$this->httpPost($postUrl, $parms);  //$parms  
	}

	public function httpPost($url, $parms) {  
	    $url = $url . $parms;  
	    if (($ch = curl_init($url)) == false) {  
	        throw new Exception(sprintf("curl_init error for url %s.", $url));  
	    }  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	    curl_setopt($ch, CURLOPT_HEADER, 0);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);  
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	    if (is_array($parms)) {  
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data;'));  
	    }  
	    $postResult = @curl_exec($ch);  
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    if ($postResult === false || $http_code != 200 || curl_errno($ch)) {  
	        $error = curl_error($ch);  
	        curl_close($ch);  
	        throw new Exception("HTTP POST FAILED:".$error);  
	    } else {  
	        // $postResult=str_replace("\xEF\xBB\xBF", '', $postResult);  
	        switch (curl_getinfo($ch, CURLINFO_CONTENT_TYPE)) {  
	            case 'application/json':  
	                $postResult = json_decode($postResult);  
	                break;  
	        }  
	        curl_close($ch);  
	        return $postResult;  
	    }  
	}  	
}
?>