<?php
namespace Common\Lib\Alipay;

/**
 * 手机网页版支付宝类。仅限MD5加密方法
 * ------------------------------------
 * @author 闫嵩达<yansong.da@qq.com>
 * @lastModify :2014-10-4 13:06
 * ------------------------------------
 */
class AlipayMobileweb {

	var $alipay_gateway = 'http://wappaygw.alipay.com/service/rest.htm?';
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';

	/**
	 * 构建函数
	 * @param [type] $alipay_config [description]
	 */
	public function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
	}

	/**
	 * 获取token值
	 * @param  [type] $para_temp [未处理的请求数组]
	 * @return [type]            [获得的Token]
	 */
	public function getToken($para_temp) {
		$result = '';
		//待请求参数数组【去空值、排序】
		$request_data = $this->getUnsigncode($para_temp, 1);
		//将待请求参数数组 转换为 字符串
		$request_str = $this->createString($request_data);
		//生成md5签名结果并加在请求数组之后
		$request_data['sign'] = md5($request_str . $this->alipay_config['key']);
		//远程获取数据
		$result = $this->toPost($this->alipay_gateway, $this->alipay_config['cacert'],$request_data,trim(strtolower($this->alipay_config['input_charset'])));
		$result = urldecode($result);
		$para_text = $this->responsePost($result);
		if ( $para_text['request_token'] != '') {
			return $para_text['request_token'];
		} else {
			$this->err('获取Token值错误！请检查！');
		}
	}

	/**
	 * 获取跳转到支付宝付款链接
	 * @param  [type] $para_temp [包含token的未处理请求数组]
	 * @return [type]            [获取到的url链接]
	 */
	public function url($para_temp)
	{
		$url = '';
		//待请求参数数组【去空值、排序】
		$request_data = $this->getUnsigncode($para_temp, 1);
		//将待请求参数数组 转换为 字符串
		$request_str = $this->createString($request_data);
		//生成md5签名结果
		$request_data['sign'] = md5($request_str . $this->alipay_config['key']);
		$requestDatabyget = $this->createString($request_data);
		return $this->alipay_gateway.$requestDatabyget;
	}

	/**
	 * 验证call_back_url所收到的请求是否为支付宝官方返回
	 * @param  [type] $getRequest [get得到的数组]
	 * @return [type]             [验证成功返回true]
	 */
	public function verifyReturn($getRequest)
	{
		//待请求参数数组【去空值、排序】
		$request_data = $this->getUnsigncode($getRequest, 1);
		//将待请求参数数组 转换为 字符串
		$request_str = $this->createString($request_data);
		//生成md5签名结果
		$request_data['sign'] = md5($request_str . $this->alipay_config['key']);
		if ( $request_data['sign'] == $getRequest['sign']) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 验证notify_url所收到的请求是否为支付宝官方返回
	 * @param  [type] $postRequest [post得到的数据数组]
	 * @return [type]              [description]
	 */
	public function verifyNotify($postRequest)
	{
		$doc = new DOMDocument();
		$doc->loadXML($postRequest['notify_data']);
		$notify_id = $doc->getElementsByTagName( "notify_id" )->item(0)->nodeValue;
		//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
		$responseTxt = 'true';
		if ( !empty($notify_id)) {
			$responseTxt = $this->getResponse($notify_id);
		}
		//待请求参数数组【去空值、排序】
		$request_data = $this->getUnsigncode($getRequest, 0);
		//将待请求参数数组 转换为 字符串
		$request_str = $this->createString($request_data);
		//生成md5签名结果
		$request_data['sign'] = md5($request_str . $this->alipay_config['key']);
		if ( ($request_data['sign'] == $postRequest['sign']) && preg_match("/true$/i",$responseTxt)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 得到处理后的数组【除去空值和签名参数 -> 对数组按字母排序】
	 * @param  [type] $para_temp [description]
	 * @param  [type] $isSort    [description]
	 * @return [type]            [description]
	 */
	private function getUnsigncode($para_temp, $isSort)
	{
		//除去数组中的空值和签名参数
		$para_filter = array();
		while (list ($key, $val) = each ($para_temp)) {
			if($key == "sign" || $val == "")continue;
			else	$para_filter[$key] = $para_temp[$key];
		}
		if ( $isSort ) {//其他情况按字母顺序排序
			ksort($para_filter);
			reset($para_filter);
		} else {//异步通知固定排序
			$para_filter['service'] = $para_temp['service'];
			$para_filter['v'] = $para_temp['v'];
			$para_filter['sec_id'] = $para_temp['sec_id'];
			$para_filter['notify_data'] = $para_temp['notify_data'];
		}
		return $para_filter;
	}

	/**
	 * 将数组转换为url请求字符串
	 * @param  [type] $para [description]
	 * @return [type]       [description]
	 */
	private function createString($para) {
		$arg  = "";
		while (list ($key, $val) = each ($para)) {
			$arg.=$key."=".$val."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
		
		return $arg;
	}

	/**
	 * 对支付宝进行post数据
	 * @param  [type] $url           [post的url]
	 * @param  [type] $cacert_url    [证书路径]
	 * @param  [type] $para          [请求参数]
	 * @param  string $input_charset [字符集]
	 * @return [type]                [请求结果]
	 */
	private function toPost($url, $cacert_url, $para, $input_charset = '')
	{
		if (trim($input_charset) != '') {
			$url = $url."_input_charset=".$input_charset;
		}
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
		curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
		curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl,CURLOPT_POST,true); // post传输数据
		curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
		$responseText = curl_exec($curl);
		//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close($curl);
		
		return $responseText;
	}

	/**
	 * 对支付宝进行get请求
	 * @param  [type] $url        [请求url]
	 * @param  [type] $cacert_url [证书路径]
	 * @return [type]             [请求结果]
	 */
	private function toGet($url,$cacert_url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
		curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
		$responseText = curl_exec($curl);
		//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close($curl);
		
		return $responseText;
	}

	/**
	 * 对post请求后得到的数据进行处理
	 * @param  [type] $str_text [需要处理的数据]
	 * @return [type]           [处理后的数据]
	 */
	private function responsePost($str_text) {
		//以“&”字符切割字符串
		$para_split = explode('&',$str_text);
		//把切割后的字符串数组变成变量与数值组合的数组
		foreach ($para_split as $item) {
			//获得第一个=字符的位置
			$nPos = strpos($item,'=');
			//获得字符串长度
			$nLen = strlen($item);
			//获得变量名
			$key = substr($item,0,$nPos);
			//获得数值
			$value = substr($item,$nPos+1,$nLen-$nPos-1);
			//放入数组中
			$para_text[$key] = $value;
		}
		
		if( ! empty ($para_text['res_data'])) {
			//token从res_data中解析出来（也就是说res_data中已经包含token的内容）
			$doc = new DOMDocument();
			$doc->loadXML($para_text['res_data']);
			$para_text['request_token'] = $doc->getElementsByTagName( "request_token" )->item(0)->nodeValue;
		}
		
		return $para_text;
	}

	/**
	 * 对get请求后的数据进行处理（仅在验证notifyurl是否为支付宝发出时用到）
	 * @param  [type] $notify_id [notify_id]
	 * @return [type]            [description]
	 */
	private function getResponse($notify_id) {
		//$transport = strtolower(trim($this->alipay_config['transport']));
		$partner = trim($this->alipay_config['partner']);
		$veryfy_url = $this->http_verify_url;
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = $this->toGet($veryfy_url, $this->alipay_config['cacert']);
		
		return $responseTxt;
	}

	private function err($msg)
	{
		header('Content-type:text/html;charset=utf-8');
		echo $msg;
		exit;
	}
}