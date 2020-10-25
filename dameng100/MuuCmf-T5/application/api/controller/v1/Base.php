<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Db;
use think\Request;
use Firebase\JWT\JWT;
use app\api\controller\Api;
use app\api\controller\UnauthorizedException;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 */
class Base extends Api
{
    public $key = 'muucmf';

    public $uid;

    public function _initialize()
    {
        parent::_initialize();
        //$this->checkAccessToken();
    }

    /**
     * 验证jwt权限
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function checkAccessToken()
    {
        $header = Request::instance()->header();

        if (empty($header['token']) || $header['token'] == 'null'){
            return 'Token不存在,拒绝访问';
        }else{
            $checkJwtToken = $this->verifyToken($header['token'], 'access');
            if ($checkJwtToken['status'] == 1001) {
                return true;
            }else{
                return $checkJwtToken['msg'];
            }
        }
    }

    /**
     * 验证刷新token 并返回重新生成的access_token或错误信息
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function checkRefreshToken()
    {
        $header = Request::instance()->header();

        if (empty($header['refresh-token']) || $header['refresh-token'] == 'null'){
            return 'refresh_token不存在,拒绝访问';
        }else{
            $checkJwtToken = $this->verifyToken($header['refresh-token'], 'refresh');
            if ($checkJwtToken['status'] == 1001) {
                //验证成功后重新生成access_token
                $uid = $checkJwtToken['uid'];
                $access_token = $this->createAccessToken($uid);
                //重新生成refresh_token
                $refresh_token = $this->createRefreshToken($uid);

                return ['status' => 1001,'access_token' => $access_token, 'refresh_token' => $refresh_token];

            }else{
                return $checkJwtToken['msg'];
            }
        }
    }

    //校验jwt权限API
    protected function verifyToken($token,$type)
    {
        $key = $this->key;
        // JWT::$leeway = 3;
        try {
            $jwtAuth = json_encode(JWT::decode($token, $key, array('HS256')));
            $authInfo = json_decode($jwtAuth, true);

            $result = [];

            if($type == 'access' && $authInfo['data']['type'] != 'access'){

                return [
                    'status' => 1002,
                    'msg' => 'Access Token无效'
                ];
                
            }

            if($type == 'refresh' && $authInfo['data']['type'] != 'refresh'){
                
                return [
                    'status' => 1002,
                    'msg' => 'Refresh Token无效'
                ];
            
            }
            
            if (!empty($authInfo['data']['uid'])) {

                //赋值给$this->uid;
                $this->uid = $authInfo['data']['uid'];
                model('common/Member')->login($this->uid);

                $result = [
                    'status' => 1001,
                    'uid' => $authInfo['data']['uid'],
                    'msg' => 'Token验证通过'
                ];
            } else {
                $result = [
                    'status' => 1002,
                    'msg' => 'Token验证不通过,用户不存在'
                ];
            }
            return $result;

        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $result = [
                'status' => 1002,
                'msg' => 'Token无效'
            ];
            return $result;
        } catch (\Firebase\JWT\ExpiredException $e) {
            $result = [
                'status' => 1003,
                'msg' => 'Token过期'
            ];
            return $result;
        } catch (Exception $e) {
            
            return $e;
        }
    }

    /**
     * 生成access_token
     *
     * @param      <type>  $uid    The uid
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function createAccessToken($uid)
    {
        $key = $this->key; //jwt的签发密钥，验证token的时候需要用到

        $time = time(); //签发时间

        $expire = $time + 7200; //过期时间

        $token = [
            "iss" => "https://muucmf.cn",//签发组织
            "aud" => "https://muucmf.cn", //签发作者
            "iat" => $time,
            "nbf" => $time,
            "exp" => $expire,
            "data" => [
                "type" => 'access',
                "uid" => $uid,
            ]
        ];

        $access_token = JWT::encode($token, $key);
        
        return $access_token;
    }

    /**
     * 设置刷新TOKEN
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function  createRefreshToken($uid)
    {
        $key = $this->key; //jwt的签发密钥，验证token的时候需要用到

        $time = time(); //签发时间

        $expire = $time + 3600 * 24 *7; //过期时间 为7天

        $token = [
            "iss" => "https://muucmf.cn",//签发组织
            "aud" => "https://muucmf.cn", //签发作者
            "iat" => $time,
            "nbf" => $time,
            "exp" => $expire,
            "data" => [
                "type" => 'refresh',
                "uid" => $uid,
            ]
        ];

        $refresh_token = JWT::encode($token, $key);
        
        return $refresh_token;
    }

    /**
     * 根据token获取uid
     *
     * @return     integer  The uid.
     */
    public function getUid()
    {
        $header = Request::instance()->header();

        if (empty($header['token']) || $header['token'] == 'null'){
            return 0;
        }else{
            $checkJwtToken = $this->verifyToken($header['token'],'access');
            if ($checkJwtToken['status'] == 1001) {
                return $checkJwtToken['uid'];
            }
            return 0;
        }
    }
}