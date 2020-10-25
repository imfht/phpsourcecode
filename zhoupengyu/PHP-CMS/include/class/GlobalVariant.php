<?php
class GlobalVariant{
	var $post;
	var $get;
	var $request;
	var $cookie;
	var $session;
	var $protect = array();
	var $formData = array();
	var $needAddslashes;
	function __construct(){
		$this->needAddslashes = !ini_get("magic_quotes_gpc");
		$ssid = $this->addslashes($_SERVER["HTTP_SSID"]);
		if (isset($_POST["PHPSESSID"])) {
			session_write_close();
            session_id($_POST["PHPSESSID"]);
        }elseif(isset($ssid)){
        	$ssid = base64_decode($ssid);
        	session_id($ssid);
        }
		if(!defined("OPEN_SESSION") || OPEN_SESSION){
			session_start();
		}
        
		$this->post = $this->addslashes($_POST);
		$this->get= $this->addslashes($_GET);
		$this->request = $this->addslashes($_REQUEST);
		$this->cookie = $this->addslashes($_COOKIE[_COOKIES_NAME]);
		$this->session = $_SESSION;

		$this->fpost = $this->addFilter($this->post);
		$this->fget= $this->addFilter($this->get);
		$this->frequest = $this->addFilter($this->request);
		$this->fcookie = $this->addFilter($this->cookie);
		
		$this->setDefaultProtectList();
	}
	function addslashes($array)
	{
		if ($this->needAddslashes)
		{
			foreach ($array as $key => $value)
			{
				if (!is_array($value))
				{
					$array[$key] = addslashes($value);
				}else{
					$array[$key] = $this->addslashes($value);
				}
			}
		}
		return $array;
	}
	
	function addFilter($array)
	{
		if (!is_array($array)) {
			return false;
		}
		foreach ($array as $key => $value)
		{
			if (!is_array($value))
			{
				$array[$key] = $this->filter($value);
			}else{
				$array[$key] = $this->addFilter($value);
			}
		}
		return $array;
	}
	
	function filter($str)
	{
	
		$sarr = array(
					'/\son([a-z]+)=/isU', '/\sid=/is', '/<script/is', '/<\/script/is', '/<iframe/is', '/<\/iframe/is', '/<frameset/is', '/<frame/is', '/<applet/is', '/<meta/is', '/<html/is', '/<\/html/is', '/<body/is', '/<\/body/is', '/<a(.*?)href=([\'"]?)(.*?)script:(.*?)>/isU',"/[+]\/v/i","/<(.*) on(.*)=/i","/<(.*)expression(.*)/is","/<(.*)&#115&#99&#114&#105&#112&#116&#58/is","/\bon(.*)=/is"
								);
		$rarr = array(
				' on\\1=', ' id=', '&lt;script', '&lt;/script', '&lt;iframe', '&lt;/ifeame', '&lt;frameset', '&lt;frame', '&lt;applet', '&lt;meta', '&lt;html', '&lt;/html', '&lt;body', '&lt;/body', '<a\\1href=\\2\\3script&#58;\\4>',"/v","&lt;\\1 on\\2=","&lt;\\1expression\\2","&lt;\\1&#115&#99&#114&#105&#112&#116&#58","\\1="
									);
		$str = preg_replace($sarr, $rarr, $str);
	//	$str = htmlentities($str,ENT_QUOTES,"utf-8");
		return $str; 
	}
	function setDefaultProtectList()
	{
		$this->protect = array("PHPSESSID","user_password","uid","lang","behavior" );
	}
	function SetCookieProtectList($protect_list){
		$this->protect = $protect_list;
	}
	
	function SetCookieData($cookie_name = null,$expire = null){
		$cookie_name = empty($cookie_name)?_COOKIES_NAME:$cookie_name;
		
		foreach($this->cookie as $key => $value){
			if ($value!="" && !is_array($value))
			setcookie($cookie_name . "[$key]",$value,$expire,_HOST_ROOTPATH);
		}
	}
	
	function SetCookieDatas($cookie_name = null,$expire = null){
		
		$cookie_name = empty($cookie_name)?_COOKIES_NAME:$cookie_name;
		$this->getCookieSaveList($cookie_name,$this->cookie,$list);
		foreach ($list as $data)
		{
			setcookie($data["name"],$data["value"],$expire,_HOST_ROOTPATH);
		}
	}
	function getCookieSaveList($cookie_name,$value,&$out,$name ="")
	{
		foreach ($value as $key => $v)
		{
			if (is_array($v)) {
				$this->getCookieSaveList($cookie_name,$v,$out,$name . "[" . $key . "]");
			}else{
				$out[] = array("name"=>$cookie_name . $name . "[" . $key . "]","value" => $v);
			}
		}
	}
	
	function ClearCookieData($index = null , $num = null,$cookie_name = null){
		$cookie_name = empty($cookie_name)?_COOKIES_NAME:$cookie_name;
		if ($index == null){
			if ($_COOKIE[$cookie_name])
			{
				foreach($_COOKIE[$cookie_name] as $key => $value){
					$flag = true;
					foreach ($this->protect as $protect){
						
						if ($key == $protect && $num != "all"){
							$flag = false;
						}
					}
					
					if ($flag){
						setcookie($cookie_name . "[$key]",null,null,_HOST_ROOTPATH);
						unset($this->cookie[$key]);
					}
				}
				
			}
		}else{
			setcookie(_COOKIES_NAME . "[$index]");
			unset($this->cookie[$index]);
		}


	}

	function SetSessionData(){
//		$_SESSION = $this->session;
		session_unset();
		$this->SetSession($this->session,$_SESSION);
	}
	function GetSessionData(){
		
		$this->session = $_SESSION;
	}
	
	function SetSession($datas,&$session)
	{
		foreach ($datas as $key => $data)
		{
			if (is_array($data))
			{
				$datas[$key] = $this->SetSession($data,$session[$key]);
				
			}else{
				$session[$key] = $data;
			}
		}
		
	}
}
?>