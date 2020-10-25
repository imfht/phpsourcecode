<?php
namespace herosphp\rsa;

/**
 * 签名的简单算法实现
 * @author yangjian<yangjian102621@gmail.com>
 * @since v3.0.0
 */
class SignUtil
{

    /**
     * 不需要签名的参数
     * @var array
     */
	private static $unSignKeyList = array(
		"__version" => 1,
		"__sign" => 1
	);

    /**
     * 生成签名
     * @param $url
     * @param $params
     * @return string
     */
	public static function sign($url, $params)
	{
		ksort($params);
		$sourceSignString = $url.self::signString($params);
		return md5($sourceSignString);
	}

    /**
     * 组合签名字符串
     * @param $params
     * @return string
     */
	private static function signString($params)
	{

		if( is_string($params) ) {
			return $params;
		}
		//拼原String
		$newparams = array();
		//保留需要参与签名的属性
		foreach ( $params as $key => $value ) {
			if ( !isset(self::$unSignKeyList[$key]) ) {
				$newparams[] = $value;
			}
		}
		return json_encode($newparams ,JSON_UNESCAPED_UNICODE);
	}
}
