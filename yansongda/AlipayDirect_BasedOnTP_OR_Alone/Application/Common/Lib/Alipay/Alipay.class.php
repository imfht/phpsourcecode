<?php
namespace Common\Lib\Alipay;

/**
* 支付宝即时到帐接口集成
* ----------------
* @author @闫嵩达<yansong.da@qq.com>
* 2014/11/10 14:12
* ----------------
*/
class Alipay
{
	
	private $alipay_gateway = 'https://mapi.alipay.com/gateway.do?';
	private $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

	/**
	 * 到支付宝付款
	 * @param  [array] $para [商品请求参数]
	 * @return []       []
	 */
	public function toAlipay($para)
	{
		$para['service'] = 'create_direct_pay_by_user';
		$para['partner'] = C('alipay_partner');
		$para['payment_type'] = '1';
		$para['seller_email'] = C('alipay_seller_email');
		$para['notify_url'] = U('Home/Alipay/alipayNotify', '', true, true);
		$para['return_url'] = U('Home/Alipay/alipayReturn', '', true, true);
		$para['_input_charset'] = 'utf-8';
		$para_filter = $this->paraFilter($para);//除去空值和签名参数
		$para_sort = $this->argSort($para_filter);//对待签名参数数组排序
		$mysign = $this->createSign($para_sort);//生成签名结果
		$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = 'MD5';
		
		$linkpara = $this->createLinkstring($para_sort);//生成url参数
        $url = $this->alipay_gateway.$linkpara;
        redirect($url);//跳转到支付宝付款
	}

	/**
     * 验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	public function isAlipay($data){
		if( empty($data) ) {//判断get来的数组是否为空
			return false;
		} else {
			//验证签名是否正确
			$isTruesign = $this->getSignVeryfy($data);
			//return $isTruesign;
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'false';
			if ( !empty($data["notify_id"]) ) {
				$responseTxt = $this->getResponse($data["notify_id"]);
			}
			if ( preg_match("/true$/i",$responseTxt) && $isTruesign ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @return 签名验证结果
     */
	private function getSignVeryfy($para_temp)
	{
		//除去待签名参数数组中的空值和签名参数 
		$para_filter = $this->paraFilter($para_temp);
		//对待签名参数数组排序
		$para_sort = $this->argSort($para_filter);
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->createLinkstring($para_sort);
		if ( md5($prestr.C('alipay_key')) ==  $para_temp['sign'] ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 除去数组中的空值和签名参数
	 * @param $para 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
	 */
	private function paraFilter($para) {
		$para_filter = array();
		while (list ($key, $val) = each ($para)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para_filter[$key] = $para[$key];
		}
		return $para_filter;
	}
	/**
	 * 对数组排序
	 * @param $para 排序前的数组
	 * return 排序后的数组
	 */
	private function argSort($para) {
		ksort($para);
		reset($para);
		return $para;
	}

	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	private function createSign($para_sort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->createLinkstring($para_sort);
		return md5($prestr.C('alipay_key'));
	}

	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	private function createLinkstring($para) {
		$arg  = "";
		foreach ($para as $key => $val) {
			$arg .= $key."=".$val."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
		return $arg;
	}

	/**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
	private function getResponse($notify_id)
	{
		$veryfy_url = $this->https_verify_url."partner=" . C('alipay_partner') . "&notify_id=" . $notify_id;
		$curl = curl_init($veryfy_url);
		curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
		curl_setopt($curl, CURLOPT_CAINFO,getcwd().'\\cacert.pem');//证书地址
		$responseText = curl_exec($curl);
		//dump( curl_error($curl) );
		curl_close($curl);
		return $responseText;
	}

}