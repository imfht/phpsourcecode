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
 * 小程序
 * Class Sms
 * @package App\Libs
 */
class MiniProgram
{

    private $app;

    function __construct()
    {
        $config = [
            'app_id' => config('weixin.miniprogram.appid'),
            'secret' => config('weixin.miniprogram.secret'),
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
        ];

        $this->app = Factory::miniProgram($config);
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
     * 获取session_key
     * @param $code
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function sessionKey($code)
    {
        try {
            $cache_key = 'weixin_session_key:' . $code;
            $session_key = cache($cache_key);
            if (!$session_key) {
                $result = $this->app->auth->session($code);
                if (isset($result['session_key'])) {
                    $session_key = $result['session_key'];
                    cache([$cache_key => $session_key], 600);
                } else {
                    api_error($result['errcode'] . '|' . $result['errmsg']);
                    return false;
                }
            }
            return $session_key;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取解密后的信息
     * @param $code
     * @param $iv
     * @param $encrypt_data
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function decryptData($code, $iv, $encrypt_data)
    {
        try {
            $session_key = self::sessionKey($code);
            if ($session_key) {
                $decrypted_data = $this->app->encryptor->decryptData($session_key, $iv, $encrypt_data);
                return $decrypted_data;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
