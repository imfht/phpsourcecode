<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/9
 * Time: 上午11:14
 */

namespace App\Libs\Weixin;

use EasyWeChat\Factory;

/**
 * 公众号
 * Class Sms
 * @package App\Libs
 */
class Wechat
{

    private $app;

    function __construct()
    {
        $platform = get_platform();
        if ($platform == 'mp') {
            $config = [
                'app_id' => config('weixin.mp.appid'),
                'secret' => config('weixin.mp.secret'),
                'response_type' => 'array',
            ];
        } else {
            $config = [
                'app_id' => config('weixin.app.appid'),
                'secret' => config('weixin.app.secret'),
                'response_type' => 'array',
            ];
        }
        $this->app = Factory::officialAccount($config);
    }

    /**
     * 获取配置好的实例信息
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * 获取微信授权后的用户信息
     * @param $code
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function userInfo()
    {
        try{
            $user = $this->app->oauth->user();
            $auth_info = $user->getOriginal();
            return $auth_info;
        } catch (\Exception $e) {
            return false;
        }
    }
}
