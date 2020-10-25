<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;

/**
 * Class WxToken 微信Token类
 * @package wechat
 */
class Token extends Base
{
    // 获取token API地址
    private static $getTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET';

    /**
     * [gain 获取微信access_token]
     * @param  string $appid [微信AppID]
     * @param  string $appSecret [微信AppSecret]
     * @return [string] [微信access_token]
     */
    public static function gain(string $appid,string $appSecret)
    {
        $param = \WeChat\Extend\File::param('access_token');
        if ($param === null or (isset($param['time']) and time() - $param['time'] > 7150)) {
            // 进行微信AppID 和 AppSecret的验证
            if(empty($appid) or empty($appSecret)){
                self::error('请设置管理端微信公众号开发者APPID 和 APPSECRET~ !');
            }

            // 获取参数验证规则
            if (strlen(trim($appid)) != 18 or strlen(trim($appSecret)) != 32) {
                self::error('请设置正确格式的微信公众号开发者APPID 和 APPSECRET~ !');
            }

            // 准备数据
            static::$getTokenUrl = str_replace('APPID', $appid, static::$getTokenUrl);
            static::$getTokenUrl = str_replace('APPSECRET', $appSecret, static::$getTokenUrl);

            // 返回结果
            $result = self::get(static::$getTokenUrl);
            isset($result['access_token']) &&  \WeChat\Extend\File::param('access_token', $result);
            return $result;
        } else {
            return $param['access_token'];
        }
        self::error('扩展文件夹权限不足~ !');
    }

}
