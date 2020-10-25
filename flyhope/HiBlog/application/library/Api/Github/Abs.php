<?php

/**
 * Github认证抽象类
 *
 * @package Github
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Api\Github;
abstract class Abs extends \Api\Abs {
    
    //Github接口地址
    protected static $_url_basic = 'https://api.github.com/';
    
    
    /**
     * AccessToken在SESSION中的名称
     * 
     * @var string
     */
    const ACCESS_TOKEN_SESSION = 'github-access-token';
    
    /**
     * POST提交数据
     *
     * @param string $path           URL路径
     * @param array  $param          参数
     * @param string $custom_request 自定义请求方式
     * @param string $timeout        超时时间
     *
     * @return \Comm\Request\Single
     */
    protected function _post($path, $param = null, $custom_request = null, $timeout = null) {
        if(is_array($param)) {
            $param = \Comm\Json::encode($param);
        }
        return parent::_post($path, $param, $custom_request, $timeout);
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \Api\Abs::_prepareRequest()
     */
    protected function _prepareRequest(\Comm\Request\Single $request) {
        $access_token = self::showAccessToken();
        $request->appendHeader(["Authorization: token {$access_token}"]);
    }
    
    /**
     * 获取AccessToken
     * 
     * @return \mixed
     */
    static public function showAccessToken() {
        $access_token = \Yaf_Registry::get('github-access-token');
        $access_token || $access_token = \Comm\Arg::session('github-access-token');
        return $access_token;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Api\Abs::_process()
     */
    protected function _process($result, \Comm\Request\Single $request) {
        $result = json_decode($result);
        if(!empty($result->message)) {
            $request_info = $request->showInfo();
            $code = isset($request_info['http_code']) ? $request_info['http_code'] : 0;
            
            $e = new \Exception\Api($result->message, $code);
            $e->http_code = $code;
            throw $e;
        }
        return $result;
    }
}
