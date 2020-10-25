<?php
/**
 * 发送http请求类
 * -----------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */
namespace herosphp\http;
use herosphp\exception\HeroException;
use herosphp\string\StringUtils;

class HttpClient {

	/**
	 * 发送 http GET 请求
	 * @param $url
	 * @param $params
	 * @param array $headers 请求头信息
	 * @param bool $return_header 是否返回头信息
	 * @return mixed
	 */
	public static function get( $url, $params=null, $headers=null, $return_header = false )
	{
		$self = new self();
		if ( is_array($params) ) {
			$params = http_build_query($params);
			if ( strpos($url, '?') == false ) {
				$url .= '?'.$params;
			} else {
				$url .= '&'.$params;
			}
		}

		$curl = $self->_curlInit($url, $headers);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		return $self->_doRequest($curl, $return_header);
	}
	public static function getWithHeader($url, $params)
	{
		return self::get($url, $params, null, true);
	}

	/**
	 * 使用代理访问
	 * @param $url
	 * @param $proxy  代理配置
	 * @param $params
	 * @return mixed
	 */
	public static function getProxy($url, $proxy, $params, $returnHeader=false) {
		$self = new self();
		if ( is_array($params) ) {
			$params = http_build_query($params);
			if ( strpos($url, '?') == false ) {
				$url .= '?'.$params;
			} else {
				$url .= '&'.$params;
			}
		}
		$curl = $self->_curlInit($url, null);
		curl_setopt ($curl, CURLOPT_PROXY, $proxy);
		curl_setopt($curl, CURLOPT_HTTPGET, true);

		return $self->_doRequest($curl, $returnHeader);
	}

	/**
	 * 发送http POST 请求
	 * @param $url
	 * @param $params
	 * @param null $headers
	 * @return bool|mixed
	 */
	public static function post($url, $params, $headers=null)
	{
		$self = new self();
		if ( is_array($params) ) {
			$params = http_build_query($params);
		}
		$curl = $self->_curlInit($url, $headers);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

		return $self->_doRequest($curl, false);
	}

    /**
     * 发送restful PUT请求
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function put($url, $params) {
        $self = new self();
        if ( is_array($params) ) {
            $params = StringUtils::jsonEncode($params);
        }
        $curl = $self->_curlInit($url, array('Content-Type' => 'application/json'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        return $self->_doRequest($curl, false);
    }

    /**
     * 发送restful DELETE请求
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function delete($url, $params) {
        $self = new self();
        if ( is_array($params) ) {
            $params = StringUtils::jsonEncode($params);
        }
        $curl = $self->_curlInit($url, array('Content-Type' => 'application/json'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        return $self->_doRequest($curl, false);
    }

	/**
	 * 发送Http请求
	 * @param $curl
	 * @param $return_header
	 * @return mixed
	 * @throws HeroException
	 */
	private static function _doRequest($curl, $return_header=false) {

		$ret	= curl_exec($curl);
		$info	= curl_getinfo($curl);

		curl_close($curl);
		if( $ret == false ) {
			throw new HeroException("cURLException:".curl_error($curl));
		}

		if(  $return_header ) {
			return ['header' => $info, 'body'   => $ret];
		} else {
			return $ret;
		}

	}

	/**
	 * 创建curl对象
	 * @param $url
	 * @param $headers
	 * @return resource
	 */
	private static function _curlInit($url, $headers) {
		$curl	= curl_init();
		if( stripos( $url, 'https://') !== FALSE ) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		if ( is_array($headers) ) {
			$_headers = array();
			foreach ( $headers as $key => $value ) {
				$_headers[] = "{$key}:$value";
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $_headers);
		}
		return $curl;
	}
}