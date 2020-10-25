<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/4/3 Time: 16:36
 */

namespace app\api\controller\v1;

use think\restful\exception\ApiException;
use think\restful\jwt\Jwt;

/**
 * Class Reflex API基类
 * @package app\api\controller\v1
 */
class Base extends \think\restful\Api
{
    public function __construct($debug = false)
    {
        parent::__construct($debug);
    }

    protected function handle()
    {
        if ($this->config['API_AUTHORIZATION']){
            // 开启JWT验证,执行业务代码
            // 没有jwt参数 或 signature 签名
            if (!isset($this->param['jwt'])){
                ApiException::exception('缺少API授权信息 jwt~');
            }
            if (!isset($this->param['signature'])){
                ApiException::exception('缺少API授权信息 signature~');
            }

            $jwtArr = Jwt::decode($this->param['jwt'],$this->config['API_AUTHORIZATION_KEY']);
            $userJwtSignature = md5(join(',',$jwtArr['data']));
            if ($userJwtSignature !== $this->param['signature']) {
                $this->error('API授权信息错误~');
            }
        }
    }
}