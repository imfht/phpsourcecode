<?php

defined('IN_CART') or die;

/**
 *
 * Http 处理类
 * 
 */
class Http
{
//	/**
//	 *
//	 * 处理get请求
//	 * 
//	 */
//	public function get($url,$args = array()) {
//		$r = array_merge(array("method"=>"get"),$args);
//		return $this->request($url,$r);
//	}

    /**
     *
     * 处理post请求
     * 
     */
    public function post($url, $args = array())
    {
        $r = array_merge(array("method" => "post"), array("body" => $args));
        return $this->request($url, $r);
    }

    /**
     *
     * 发出请求
     * 
     */
    private function request($url, $args = array())
    {
        //默认请求数据
        $defaults = array(
            'method' => 'GET',
            'timeout' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(),
            'body' => null,
            'decompress' => true);

        //整理默认请求于args
        $r = array_merge($defaults, $args);

        $r['body'] = http_build_query($r['body']);

        //解析url
        $arrurl = parse_url($url);

        $opts = array(
            'http' => array(
                'timeout' => $r['timeout'],
                'method' => strtoupper($r['method']),
                'protocol_version' => $r['httpversion'],
                'header' => "User-Agent: cart" . CRLF
                . "Host: " . $arrurl['host'] . CRLF
                . "Content-type: application/x-www-form-urlencoded" . CRLF
                . "Content-Length: " . strlen($r['body']) . CRLF
                . "Connection: Close" . CRLF
                . CRLF,
                'content' => $r['body']
            )
        );


        $stream = stream_context_create($opts);
        $content = file_get_contents($url, false, $stream);
        return $content;
    }

}
