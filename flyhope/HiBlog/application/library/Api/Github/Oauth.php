<?php

/**
 * Github Oauth接口
 *
 * @package Api
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Api\Github;
class Oauth extends \Api\Abs{
    
    //OAuth授权接口地址
    protected static $_url_basic = 'https://github.com/login/oauth/';
    
    /**
     * 获取操作对象
     * 
     * @param unknown $test_param
     * 
     * @return \Api\Github\Oauth
     */
    static public function init($test_param = false) {
        return new self();
    }
    
    /**
     * 通过Code获取AccessToken
     * 
     * @param string $client_id
     * @param string $client_secret
     * @param string $code
     * 
     * @example object(stdClass)#14 (3) { ["access_token"]=> string(40) "xxx" ["token_type"]=> string(6) "bearer" ["scope"]=> string(11) "public_repo" }
     * 
     * @return array
     */
    public function accessToken($client_id, $client_secret, $code) {
        return $this->_post('access_token', array(
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'code'          => $code,
        ));
    }
    
    /**
     * (non-PHPdoc)
     * @see \Api\Abs::_prepareRequest()
     */
    protected function _prepareRequest(\Comm\Request\Single $request) {
        $request->setHeader(['Accept: application/json']);
    }
    
    
} 
