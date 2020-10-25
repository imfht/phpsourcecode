<?php  
/**
 * @name 安全的cookie存储方案 
 * @author crazymus < 291445576 >
 * @licenced Apache2.0
 * @updated 2015-08-29
 * @des 加密方法来自http://www.nowamagic.net/librarys/veda/detail/2094
 *      感谢原作者的辛勤劳动及分享精神
 */
class Scookie{
	/* 密钥,请自定义 */
	private static $key = 'YOUR KEY';
	/* 支持的配置项 */
	private static $options = array(
		/* 过期时间,0表示浏览器关闭时过期 */
		'expire' => 0,
		/* 访问路径 */
		'path' => '/',
		/* 可访问域名 */
		'domain' => ''
	);
	
	/* 
     * @name 设置cookie
	 * @param $name string 名称 
	 * @param $value mixed 值,字符串或数组 
	 * @param $options array 选项
	 * @return 无
	 */
	public static function set($name,$value,$options=array()){
		if(!isset($options['domain'])){
			$options['domain'] = $_SERVER['HTTP_HOST'];
		}
		self::$options = array_merge(self::$options,$options);
		$options = self::$options;
		extract($options);
		$value = self::_encrypt(json_encode($value),'E',self::$key);
		setcookie($name,$value,$expire,$path,$domain);
	}
	
	/**
	 * @name 取回cookie 
	 * @param $name string 名称 
	 * @return mixed 值,字符串或数组
	 */
	public static function get($name){
		if(!isset($_COOKIE[$name])){
			return null;
		}
		$value = $_COOKIE[$name];
		$value = json_decode(self::_encrypt($value,'D',self::$key),true);
		return $value;
	}
	
	/* 加密核心, 私有方法, 不可调用 */
	private static function _encrypt($string,$operation,$key){
		$key=md5($key);
		$key_length=strlen($key);
		$string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
		$string_length=strlen($string);
		$rndkey=$box=array();
		$result='';
		for($i=0;$i<=255;$i++){
			$rndkey[$i]=ord($key[$i%$key_length]);
			$box[$i]=$i;
		}
		for($j=$i=0;$i<256;$i++){
			$j=($j+$box[$i]+$rndkey[$i])%256;
			$tmp=$box[$i];
			$box[$i]=$box[$j];
			$box[$j]=$tmp;
		}
		for($a=$j=$i=0;$i<$string_length;$i++){
			$a=($a+1)%256;
			$j=($j+$box[$a])%256;
			$tmp=$box[$a];
			$box[$a]=$box[$j];
			$box[$j]=$tmp;
			$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
		}
		if($operation=='D'){
			if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
				return substr($result,8);
			}
			else{
				return'';
			}
		}
		else{
			return str_replace('=','',base64_encode($result));
		}
	}
}




?>