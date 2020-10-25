<?php

require_once THIRDPATH . '/OpenSDK/oauth2/client.php';
require_once THIRDPATH . '/OpenSDK/oauth/interface.php';

/**
 * 人人网OAuth2.0 SDK
 * http://dev.renren.com/
 * 依赖：
 * 1、PECL json >= 1.2.0    (no need now)
 * 2、PHP >= 5.2.0 because json_decode (no need now)
 * 3、$_SESSION
 * 4、PECL hash >= 1.1 (no need now)
 *
 * only need PHP >= 5.0
 *
 * 如何使用：
 * 1、将OpenSDK文件夹放入include_path
 * 2、require_once 'OpenSDK/RenRen/SNS2.php';
 * 3、OpenSDK_RenRen_SNS2::init($appkey,$appsecret);
 * 4、OpenSDK_RenRen_SNS2::getAuthorizeURL($token); 获得跳转授权URL
 * 5、OpenSDK_RenRen_SNS2::getAccessToken() 获得access token
 * 6、OpenSDK_RenRen_SNS2::call();调用API接口
 *
 * 建议：
 * 1、PHP5.2 以下版本，可以使用Pear库中的 Service_JSON 来兼容json_decode
 * 2、使用 session_set_save_handler 来重写SESSION。调用API接口前需要主动session_start
 * 3、OpenSDK的文件和类名的命名规则符合Pear 和 Zend 规则
 *    如果你的代码也符合这样的标准 可以方便的加入到__autoload规则中
 *
 * @author icehu@vip.qq.com
 */
class OpenSDK_RenRen extends OpenSDK_OAuth_Interface
{

    /**
     * app key
     * @var string
     */
    protected static $client_id = '';

    /**
     * app secret
     * @var string
     */
    protected static $client_secret = '';

    /**
     * 初始化
     * @param string $appkey
     * @param string $appsecret
     */
    public static function init($appkey, $appsecret)
    {
        self::$client_id = $appkey;
        self::$client_secret = $appsecret;
    }

    /**
     * OAuth 对象
     * @var OpenSDK_OAuth_Client
     */
    private static $oauth = null;
    private static $accessTokenURL = 'https://graph.renren.com/oauth/token';
    private static $authorizeURL = 'https://graph.renren.com/oauth/authorize';
    private static $sessinKeyURL = 'https://graph.renren.com/renren_api/session_key';

    /**
     * OAuth 版本
     * @var string
     */
    protected static $version = '2.0';

    /**
     * 存储access_token的session key
     */
    const ACCESS_TOKEN = 'renren_access_token';

    /**
     * 存储refresh_token的session key
     */
    const REFRESH_TOKEN = 'renren_refresh_token';

    /**
     * 存储expires_in的sieesion key
     */
    const EXPIRES_IN = 'renren_expires_in';

    /**
     * 存储 uid 的session key
     */
    const OAUTH_USER_ID = 'renren_user_id';

    /**
     * authorize接口
     *
     * @param string $url 授权后的回调地址,站外应用需与回调地址一致,站内应用需要填写canvas page的地址
     * @param string $response_type 支持的值包括 code 和token 默认值为code
     * @param string $state 用于保持请求和回调的状态。在回调时,会在Query Parameter中回传该参数
     * @param string $display 授权页面类型 登录和授权页面的展现样式，默认为“page” 可选范围:
     *  - default       默认授权页面
     *  - mobile        支持html5的手机
     *  - popup         弹窗授权页
     *  - wap1.2        wap1.2页面
     *  - wap2.0        wap2.0页面
     *  - js            js-sdk 专用 授权页面是弹窗，返回结果为js-sdk回掉函数
     *  - apponweibo    站内应用专用,站内应用不传display参数,并且response_type为token时,默认使用改display.授权后不会返回access_token，只是输出js刷新站内应用父框架
     * @return string
     */
    public static function getAuthorizeURL($url, $response_type, $state, $display = 'default')
    {
        $params = array();
        $params['client_id'] = self::$client_id;
        $params['redirect_uri'] = $url;
        $params['response_type'] = $response_type;
        $params['state'] = $state;
        $params['display'] = $display;
        return self::$authorizeURL . '?' . http_build_query($params);
    }

    /**
     * access_token接口
     *
     *
     * @param string $type 请求的类型,可以为:code, password, token
     * @param array $keys 其他参数：
     *  - 当$type为code时： array('code'=>..., 'redirect_uri'=>...)
     *  - 当$type为password时： array('username'=>..., 'password'=>...)
     *  - 当$type为token时： array('refresh_token'=>...)
     * @return array
     */
    public static function getAccessToken($type, $keys)
    {
        $params = array();
        $params['client_id'] = self::$client_id;
        $params['client_secret'] = self::$client_secret;
        if ($type === 'token') {
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $keys['refresh_token'];
        } elseif ($type === 'code') {
            $params['grant_type'] = 'authorization_code';
            $params['code'] = $keys['code'];
            $params['redirect_uri'] = $keys['redirect_uri'];
        } elseif ($type === 'password') {
            $params['grant_type'] = 'password';
            $params['username'] = $keys['username'];
            $params['password'] = $keys['password'];
        } else {
            exit("wrong auth type");
        }

        $response = self::request(self::$accessTokenURL, 'POST', $params);
        $token = OpenSDK_Util::json_decode($response, true);
        if (is_array($token) && !isset($token['error'])) {
            self::setParam(self::ACCESS_TOKEN, $token['access_token']);
            self::setParam(self::REFRESH_TOKEN, $token['refresh_token']);
            self::setParam(self::EXPIRES_IN, $token['expires_in']);
            self::setParam(self::OAUTH_USER_ID, $token['user']['id']);
        } else {
            exit("get access token failed." . $token['error']);
        }
        return $token;
    }

    const API_VERSION = '1.0';

    /**
     * 统一调用接口的方法
     * 照着官网的参数往里填就行了
     * 需要调用哪个就填哪个，如果方法调用得频繁，可以封装更方便的方法。
     *
     * 如果上传文件 $method = 'POST';
     * $multi 是一个二维数组
     *
     * array(
     *    '{fieldname}' => array(        //第一个文件
     *        'type' => 'mine 类型',
     *        'name' => 'filename',
     *        'data' => 'filedata 字节流',
     *    ),
     *    ...如果接受多个文件，可以再加
     * )
     *
     * @param string $command 官方说明中的方法名
     * @param array $params 官方说明中接受的参数列表，一个关联数组
     * @param string $method 官方说明中的 method GET/POST
     * @param false|array $multi 是否上传文件 false:普通post array: array ( '{fieldname}'=>'/path/to/file' ) 文件上传
     * @param bool $decode 是否对返回的字符串解码成数组
     * @param OpenSDK_Sina_Weibo::RETURN_JSON|OpenSDK_Sina_Weibo::RETURN_XML $format 调用格式
     */
    public static function call($command, $params = array(), $method = 'POST', $multi = false, $decode = true, $format = 'json')
    {
        if ($format == self::RETURN_XML)
            ;
        else
            $format == self::RETURN_JSON;
        //去掉空数据
        foreach ($params as $key => $val) {
            if (strlen($val) == 0) {
                unset($params[$key]);
            }
        }
        $params['access_token'] = self::getParam(self::ACCESS_TOKEN);
//        $params['source'] = self::$client_id;
        $params['method'] = $command;
        $params['format'] = $format;
        $params['v'] = self::API_VERSION;
        $params['call_id'] = microtime(true);
        $params['sig'] = self::sigCreate($params);
        $response = self::request('http://api.renren.com/restserver.do', $method, $params, $multi);
        if ($decode) {
            if ($format == self::RETURN_JSON) {
                return OpenSDK_Util::json_decode($response, true);
            } else {
                //todo parse xml2array later
                //没必要。用json即可!
                return $response;
            }
        } else {
            return $response;
        }
    }

    /**
     * 计算请求签名
     * @param array $params
     * @return string
     * @ignore
     */
    protected static function sigCreate($params)
    {
        ksort($params);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v;
        }
        return md5($str . self::$client_secret);
    }

    protected static $_debug = false;

    public static function debug($debug = false)
    {
        self::$_debug = $debug;
    }

    /**
     * 获得OAuth2 对象
     * @return OpenSDK_OAuth2_Client
     */
    protected static function getOAuth()
    {
        if (null === self::$oauth) {
            self::$oauth = new OpenSDK_OAuth2_Client(self::$_debug);
        }
        return self::$oauth;
    }

    /**
     *
     * OAuth协议请求接口
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * @param array $multi
     * @return string
     * @ignore
     */
    protected static function request($url, $method, $params, $multi = false)
    {
        if (!self::$client_id || !self::$client_secret) {
            exit('app key or app secret not init');
        }
        return self::getOAuth()->request($url, $method, $params, $multi);
    }

}
