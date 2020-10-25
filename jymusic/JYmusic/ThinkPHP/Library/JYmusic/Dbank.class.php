<?php
/*华为网盘解析*/
namespace JYmusic;
class Dbank{		
	function getHwData($code){
			$url = 'http://dl.vmall.com/'.$code;
			preg_match("/globallinkdata\s=\s(.*?);/", $this->curl($url), $arr);
			$data = $arr[1];
			return json_decode($data)->data;
	}
	function decry($url,$key){
		$g = base64_decode($url);
		$f = substr($key,0,2);
		switch ($f) {
			case "ea":
			$d = $g;
			break;
			case "eb":
			$d = $this->aa($g, $this->b($key, $key));
			break;
			case "ed":
			$d = $this->aa($g, md5($key));
			break;
			default:
			$d = $g;
		}
		return $d;
	}

	function aa($d, $e) {
		$g = "";
		$l = strlen($e);
		$f = strlen($d);
		for ($h = 0;$h < $f; $h++) {
			$k = $this->dd($d,$h) ^$this->dd($e,$h % $l);
			$g .= $this->cc($k);
		}
		return $g;
	}
	function bb($h, $l) {
		$k = array();
		$e = 0;
		for ($f = 0; $f < 256; $f++) {
			$k[$f] = $f;
		}
		for ($f = 0; $f < 256; $f++) {
			$e = ($e + $k[$f] + $this->dd($h,$f % strlen($h))) % 256;
			$d = $k[$f];
			$k[$f] = $k[$e];
			$k[$e] = $d;
		}
		$f = 0;
		$e = 0;
		for ($m = 0; $m < strlen($l); $m++) {
			$f = ($f + 1) % 256;
			$e = ($e + $k[$f]) % 256;
			$d = $k[$f];
			$k[$f] = $k[$e];
			$k[$e] = $d;
			$g .= $this->cc($this->dd($l,$m) ^ $k[($k[$f] + $k[$e]) % 256]);
		}
		return $g;
	}

	function cc($str){
		$code = explode(",",$str);
		$re = '';
		foreach($code as $v){
			eval("\$s = $v;");
			$re.= chr($s);
		}
		return $re;
	}

	function dd($str,$i){
		$arr = unpack("C*", $str);
		$n = $arr[$i+1];
		return $n;
	}

	function curl($url) {
		if (!function_exists('file_get_contents')) {
			$data = file_get_contents($url);
		} else {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$ch = curl_init();
			$timeout = 30;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			$data = curl_exec($ch);
			curl_close($ch);
		}
		return $data;
	}

	function down($url){
		$downfile=str_replace(" ","%20",$url);
		$downfile=str_replace("http://","",$downfile);
		$urlarr=explode("/",$downfile);
		$domain=$urlarr[0];
		$getfile=str_replace($urlarr[0],'',$downfile);
		$content = @fsockopen("$domain", 80, $errno, $errstr, 12);
		if (!$content){
			die("无法连接上 $domain ！");
		}
		fputs($content, "GET $getfile HTTP/1.0\r\n");
		fputs($content, "Host: $domain\r\n");
		fputs($content, "Referer: $domain\r\n");//伪造部分
		fputs($content, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
		while (!feof($content)) {
			$tp.=fgets($content, 128);
			if (strstr($tp,"200 OK")){
				header("Location:$url");
				die();
			}
		}
		//302 转向
		$arr=explode("\n",$tp);
		$arr1=explode("Location: ",$tp);//分解出真实地址
		$arr2=explode("\n",$arr1[1]);
		header('Content-Type:application/force-download');//强制下载
		header("location:".$arr2[0]);//转向目标地址
		die();
	}
	
	public function link($code){  	
    	header('Content-type:text/html;charset:utf-8');
		//$uri = $_SERVER["REQUEST_URI"];
		//preg_match("/hwwl\/(.+)\//",$uri,$code);
		//$code = "c0i1rbbxuo";
		if($code!=""){
			$data = $this->getHwData($code);
			if($data){
			$url = $data->resource->files[0]->downloadurl;
			$key = $data->encryKey;
			$downurl = $this->decry($url,$key);
			$this->down($downurl);
			}else{
				echo '无法获取数据！';
			}
		}else{
			echo '参数错误！';
		} 	    		  		
    }
	
	
}