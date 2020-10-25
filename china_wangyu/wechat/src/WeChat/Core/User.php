<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;

/**
 * Class WxUser 微信用户类
 * @package wechat
 */
class User extends Base
{
    // 第一步：用户同意授权，获取code
    private static $getCodeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=snsapi_userinfo&state=state&connect_redirect=1#wechat_redirect';

    // 第二步：通过code换取网页授权access_token
    private static $getOpenIdUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code';

    // 第三步：拉取用户信息(需scope为 snsapi_userinfo)
    private static $getUserInfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';

    // 第四步：拉取用户信息(普通access_token版)
    private static $getUserInfoUrlByToken = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';

    /**
     * []
     * @param  string $appid []
     *
     */

    /**
     * code 重载http,获取微信授权
     * @param string $appid 微信公众号APPID
     * @header 重载链接获取code
     */
    public static function code(string $appid)
    {
        empty($appid) && self::error('请设置管理端微信公众号开发者APPID ~ !');
        //当前域名
        $service_url = urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        static::$getCodeUrl = str_replace('APPID',$appid,static::$getCodeUrl);
        static::$getCodeUrl = str_replace('REDIRECT_URI',$service_url,static::$getCodeUrl);
        self::header(static::$getCodeUrl);
    }

    /**
     * 获取用户 OPENID
     * @param string $code     微信授权CODE
     * @param string $appid    微信appid
     * @param string $appSecret    微信appSecret
     * @param bool $type    true:获取用户信息 | false:用户openid
     * @return array    用户信息|用户openid
     */
    public static function openid(string $code, string $appid,string $appSecret,bool $type = false)
    {
        //验证参数
        (empty($appid) or empty($appSecret)) && self::error('请设置管理端微信公众号开发者APPID 和 APPSECRET~ !');
        empty($code) && self::error('请验证是否传了正确的参数 code ~ !');

        //获取用户数据
        static::$getOpenIdUrl = str_replace('APPID',$appid,static::$getOpenIdUrl);
        static::$getOpenIdUrl = str_replace('SECRET',$appSecret,static::$getOpenIdUrl);
        static::$getOpenIdUrl = str_replace('CODE',$code,static::$getOpenIdUrl);

        $result = self::get(static::$getOpenIdUrl);

        return $type == false ? $result : self::userinfo($result['access_token'], $result['openid']);
    }


    /**
     * 获取用户信息(通过code换取网页授权access_token版)
     * @param string $access_token 授权获取用户关键参数：access_token
     * @param string $openid   用户openid
     * @return array
     */
    public static function userInfo(string $access_token, string $openid)
    {
        (empty($access_token) or empty($openid)) && self::error('getOpenid()方法设置参数~ !');

        static::$getUserInfoUrl = str_replace('ACCESS_TOKEN',$access_token,static::$getUserInfoUrl);
        static::$getUserInfoUrl = str_replace('OPENID',$openid,static::$getUserInfoUrl);

        return self::get(static::$getUserInfoUrl);
    }

    /**
     * 获取用户信息(普通ACCESS_TOKEN获取版)
     * @param string $access_token 普通access_token
     * @param string $openid   用户openid
     * @return array
     */
    public static function newUserInfo(string $access_token,string $openid)
    {
        (empty($access_token) or empty($openid)) && self::error('getOpenid()方法设置参数~ !');

        static::$getUserInfoUrlByToken = str_replace('ACCESS_TOKEN',$access_token,static::$getUserInfoUrlByToken);
        static::$getUserInfoUrlByToken = str_replace('OPENID',$openid,static::$getUserInfoUrlByToken);

        return self::get(static::$getUserInfoUrlByToken);
    }
}
