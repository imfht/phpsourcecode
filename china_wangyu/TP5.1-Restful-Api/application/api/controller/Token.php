<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/4/3 Time: 17:34
 */

namespace app\api\controller;

use think\Request;
use think\restful\Base;
use think\restful\jwt\Jwt;
use think\restful\response\Json;
class Token extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create()
    {
        $param = $this->param;
        if(empty($param['userName']) or empty($param['userLoginKey'])){
            return Json::json(404,'参数userName/userLoginKey不能为空~');
        }
        $token = $tokenTemplate = $this->config['API_AUTHORIZATION_TOKEN'];
        $token['iat'] = time();
        $token['nbf'] = $token['iat']  + 10;
        $token['exp'] = $token['iat'] + 600;
        $token['data'] = ['userName'=>$param['userName'],
            'userLoginKey'=>$param['userLoginKey']];
        $jwt = Jwt::encode($token,$this->config['API_AUTHORIZATION_KEY']);
        return Json::json(200,'操作成功~',[
            'jwt'=>$jwt,
            'tt'=>  $token['iat'],
            'exp' => $token['exp'],
            'signature' => md5(join(',',$token['data']))
        ]);
    }

    /**
     * 刷新时长
     * @return array
     */
    public function reset(){
        $param = $this->param;
        if(empty($param['jwt']))return Json::json(404,'参数jwt不能为空~');
        $jwtArr = Jwt::reset($jwt,$this->config['API_AUTHORIZATION_KEY']);
        return Json::json(200,'操作成功~',[
            'jwt'=> $jwtArr['jwt'],
            'tt'=>  $jwtArr['jwt']['iat'],
            'exp' => $jwtArr['jwt']['exp'],
            'signature' => md5(join(',',$jwtArr['token']['data']))
        ]);
    }
}