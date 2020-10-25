<?php

namespace common\helpers;

use yii\helpers\Json;

/**
 * 一些常用方法的封装
 *
 * @author ken <vb2005xu@qq.com>
 */
class Unit
{

	/**
	 * 取随机数
	 * @param string $length 长度
	 * @param type $numeric
	 * @return type
	 */
	static function random($length, $numeric = 0)
	{
		PHP_VERSION < '4.2.0' ? mt_srand((double) microtime() * 1000000) : mt_srand();
		$seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
		$hash = '';
		$max = strlen($seed) - 1;
		for ($i = 0; $i < $length; $i++)
		{
			$hash .= $seed[mt_rand(0, $max)];
		}
		return $hash;
	}

	/**
	 * 获取手机号运营商类型
	 * @param string $mobile_number 手机号码
	 * @return int 运营商类型, 1中国移动, 2中国联通, 3中国电信, 4其它
	 */
	static function getOperatorType($mobile_number)
	{
		//中国移动
		$cm = array(134, 135, 136, 137, 138, 139, 147, 150, 151, 152, 157, 158, 159, 182, 187, 188);
		//中国联通
		$cu = array(130, 131, 132, 155, 156, 185, 186);
		//中国电信
		$ct = array(133, 153, 180, 189);

		$prefix = substr($mobile_number, 0, 3);

		if (in_array($prefix, $cm))
		{
			$operator = 1;
		}
		elseif (in_array($prefix, $cu))
		{
			$operator = 2;
		}
		elseif (in_array($prefix, $ct))
		{
			$operator = 3;
		}
		else
		{
			$operator = 4;
		}
		return $operator;
	}

	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param array $array 需要拼接的数组
	 * @return string 拼接完成以后的字符串
	 */
	static function createLinkstring($array)
	{
		$arg = "";
		while (list ($key, $val) = each($array))
		{
			$arg.=$key . "=" . $val . "&";
		}
		//去掉最后一个&字符
		$arg = substr($arg, 0, count($arg) - 2);
		return $arg;
	}

	/**
	 * 模拟提交数据函数
	 * @param <type> $url
	 * @param <type> $data
	 * @return <type>
	 */
	static function vpost($url, $data)
	{
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		//curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl))
		{
			echo 'Errno' . curl_error($curl);
		}
		curl_close($curl); // 关键CURL会话
		return $tmpInfo; // 返回数据
	}

	/**
	 * 模拟获取内容函数
	 * @param <type> $url
	 * @return <type>
	 */
	static function vget($url)
	{
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
		//curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl))
		{
			echo 'Errno' . curl_error($curl);
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}

	/**
	 * 获取IP地址信息
	 * @param string $ip
	 * @return array|false
	 */
	public static function getIpInfo($ip)
	{
		$url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
		$ret = self::vget($url);
		$ret = Json::decode($ret, true);
		if ($ret['code'] == 0)
		{
			return $ret['data'];
		}
		return false;
	}

	/**
	 * 将列表格式转成为数组, 如： ,1,2,转为[1, 2]
	 * @param string $ids
	 * @return array
	 */
	public static function strlisttoArray($ids)
	{
		$ids = trim($ids, ',');
		$ids = explode(',', $ids);
		return $ids;
	}
	
	/**
	 * url 为服务的url地址
	 * query 为请求串
	 */
	public static function sockPost($url,$query){
		$data = "";
		$info = parse_url($url);
		$fp = fsockopen($info["host"],80,$errno,$errstr,30);
		if(!$fp){
			return $data;
		}
		$head  = "POST ".$info['path']." HTTP/1.0\r\n";
		$head .= "Host: ".$info['host']."\r\n";
		$head .= "Referer: http://".$info['host'].$info['path']."\r\n";
		$head .= "Content-type: application/x-www-form-urlencoded\r\n";
		$head .= "Content-Length: ".strlen(trim($query))."\r\n";
		$head .= "\r\n";
		$head .= trim($query);
		$write = fputs($fp,$head);
		$header = "";
		while ($str = trim(fgets($fp,4096))) {
			$header.=$str;
		}
		while (!feof($fp)) {
			$data .= fgets($fp,4096);
		}
		return $data;
	}

}

?>