<?php

namespace App\Plugin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Ixudra\Curl\Facades\Curl;


/**
 * 微信助手
 * 依赖："ixudra/curl": "6.*",
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 15:48
 */
class WxHelp
{
    /**
     * 小程序登陆获取openid
     * @param $jsCode
     * @return array
     */
    public static function getOpenInfoByMinPro($jsCode)
    {
        $url  = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
        $url  = sprintf($url, config('wx.min_project.appid'), config('wx.min_project.secret'), $jsCode);
        $json = Curl::to($url)
            ->asJson()
            ->get();
        mylog([$json, $url], 'getOpenInfoByMinPro');
        if (isset($json->openid)) {
            return ['status' => 0, 'data' => $json->openid];
        }
        return ['status' => $json->errcode, 'msg' => $json->errmsg];
    }

    /**
     * 请求微信,返回code,state
     * @param $redirect_uri
     * @param string $scope [可选]snsapi_userinfo|snsapi_base(默认)
     * @param string $state [可选]标识
     */
    public static function weChatRedirect($redirect_uri, $scope = 'snsapi_base', $state = 'STATE')
    {
        $redirect_uri = urlencode($redirect_uri);
        $app_id       = config('wx.app_id');
        $url          = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
        $url          = sprintf($url, $app_id, $redirect_uri, $scope, $state);
        header('Location:' . $url);
    }

    /**
     * 网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
     * @param $code
     * @return mixed
     */
    public static function getAuthAccessToken($code)
    {
        $app_id = config('wx.app_id');
        $secret = config('wx.app_secret');
        $url    = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
        $url    = sprintf($url, $app_id, $secret, $code);
        $result = self::getTo($url);
        return json_decode($result, true);
    }

    /**
     * 拉取用户信息(需scope为 snsapi_userinfo)
     * @param $access_token
     * @param $openid
     * @return mixed
     */
    public static function snsapiUserInfo($access_token, $openid)
    {
        $url    = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
        $url    = sprintf($url, $access_token, $openid);
        $result = self::getTo($url);
        return json_decode($result, true);
    }

    /**
     * 生成二维码
     * @param $action_name
     * @param $scene_str
     * @param int $day
     * @return mixed
     */
    public static function qrCode($action_name, $scene_str, $day = 30)
    {
        $params = [
            'action_name' => $action_name,
            'action_info' => [
                'scene' => [
                    'scene_str' => $scene_str,
                ]
            ],
        ];
        if ($action_name == 'QR_STR_SCENE') {
            $params['expire_seconds'] = $day * 3600 * 24;
        }
        $format = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
        $url    = sprintf($format, self::getAccessToken());
        $json   = Curl::to($url)
            ->withData($params)
            ->asJson()
            ->post();
        return $json;
    }

    /**
     * 生成QR图片
     * @param $ticket
     * @return array
     */
    public static function qrImage($ticket)
    {
        $format = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s';
        $url    = sprintf($format, $ticket);
        $path   = public_path('qr') . '/';
        try {
            if (!is_dir($path) && !mkdir($path)) {
                return setResult(1, $path . '目录不存在或没有写的权限');
            }
            $img  = file_get_contents($url);
            $bool = file_put_contents($path . $ticket . '.png', $img);
            if ($bool) {
                return setResult(0, 'ok');
            }
            return setResult(2, '创建失败');
        } catch (\Exception $ex) {
            return setResult(400, $ex->getMessage());
        }

    }

    //回复文本消息
    public static function proText($object, $content)
    {
        if (!isset($content) || empty($content)) {
            return "";
        }

        $xmlTpl
                = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    /**
     * 设置菜单
     * @param $json_data
     * @return mixed
     */
    public static function setMenu($json_data)
    {
        $format = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s';
        $url    = sprintf($format, self::getAccessToken());
        $result = Curl::to($url)
            ->withData($json_data)
            ->withContentType('application/json')
            ->withHeader('Content-Length:' . strlen($json_data))
            ->post();
        return json_decode($result);
    }

    /**
     * 查询菜单
     * @return mixed
     */
    public static function queryMenu()
    {
        $format = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=%s';
        $url    = sprintf($format, self::getAccessToken());
        $result = Curl::to($url)->get();
        return json_decode($result);
    }

    /**
     * 删除菜单
     * @return mixed
     */
    public static function clearMenu()
    {
        $format = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s';
        $url    = sprintf($format, self::getAccessToken());
        $result = Curl::to($url)->get();
        return json_decode($result);;
    }

    /**
     * 获取access_token 这是基础token
     * @return bool|mixed
     */
    public static function getAccessToken()
    {
        $expire       = Carbon::now()->addSecond(7000);
        $access_token = Cache::remember('access_token', $expire, function () {
            $format = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
            $url    = sprintf($format, config('wx.app_id'), config('wx.app_secret'));
            $json   = Curl::to($url)->get();
            if (json_decode($json)) {
                $obj = json_decode($json);
                return $obj->access_token;
            }
            return '';
        });
        return $access_token;
    }

    /**
     * 获取用户列表
     * @return mixed
     */
    public static function getUserList()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid=';
        $url = sprintf($url, self::getAccessToken());
        return self::getTo($url);
    }

    /**
     * 微信开发验证
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @param $token
     * @return bool
     */
    public static function checkSignature($signature, $timestamp, $nonce, $token)
    {
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取openid
     * @param $code
     * @return bool|string
     */
    function getOpenInfo($code)
    {
        if (!$this->is_weixin()) {
            return false;
        }
        $url_format = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
        $url        = sprintf($url_format, config('wx.app_id'), config('wx.app_secret'), $code);
        $data       = $this->curlGet($url);
        if ($data) {
            return $data;
        }
        return false;
    }

    /**
     * 获取用户信息，包括头像
     * @param $openid
     * @param $access_token
     * @param string $lang
     * @return string
     */
    function getUserInfo($openid, $access_token, $lang = 'zh_CN')
    {
        $query = array(
            'access_token' => $access_token,
            'openid'       => $openid,
            'lang'         => $lang,
        );
        $url   = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($query);
        $data  = $this->curlGet($url);
        if ($data) {
            return $data;
        }
        return false;
    }

    /**
     * 获取临时二维码
     * @param $access_token
     * @param $day
     * @param $scene_str
     * @return mixed
     */
    public function getTempQrCode($access_token, $day, $scene_str)
    {
        $format = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
        $url    = sprintf($format, $access_token);
        if (is_numeric($scene_str)) {
            $data = [
                'action_name' => 'QR_SCENE',
                'action_info' => [
                    'scene' => [
                        'scene_id' => $scene_str,
                    ]
                ],
            ];
        } else {
            $data = [
                'action_name' => 'QR_STR_SCENE',
                'action_info' => [
                    'scene' => [
                        'scene_str' => $scene_str,
                    ]
                ],
            ];
        }
        $params = [
            'expire_seconds' => $day * 24 * 3600,
        ];
        $params = array_merge($params, $data);
        $result = Curl::to($url)
            ->withData($params)
            ->asJson()
            ->post();
        return $result;
    }

    /**
     * 获取永久二维码
     * @param $access_token
     * @param $scene_str
     * @return mixed
     */
    public function getForeverQrCode($access_token, $scene_str)
    {
        $format = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
        $url    = sprintf($format, $access_token);
        if (is_numeric($scene_str)) {
            $data = [
                'action_name' => 'QR_LIMIT_SCENE',
                'action_info' => [
                    'scene' => [
                        'scene_id' => $scene_str,
                    ]
                ],
            ];
        } else {
            $data = [
                'action_name' => 'QR_LIMIT_STR_SCENE',
                'action_info' => [
                    'scene' => [
                        'scene_str' => $scene_str,
                    ]
                ],
            ];
        }
        $result = Curl::to($url)
            ->withData($data)
            ->asJson()
            ->post();
        return $result;
    }

    /**
     * 判断是否是微信
     * @return bool
     */
    function is_weixin()
    {
        if (strpos($_SERVER['HTTP_HOST'], '192.168') !== false) return false;
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * curl get 支持https, curl_get简写
     * @param string $url 请求url
     * @param int $timeout 超时设置,默认60秒
     * @return string 返回结果
     */
    function curlGet($url, $timeout = 60)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    public static function getTo($url, $timeout = 30)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    /**
     * 发送模板信息
     * @param $json
     * @return mixed
     */
    public static function sendTemplateInfo($json)
    {
        $url    = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s';
        $url    = sprintf($url, self::getAccessToken());
        $result = self::postTo($url, $json);
        return $result;
    }

    /**
     * 数组转换成xml
     * @param $arr
     * @return string
     */
    public static function arrToXml($arr)
    {
        return '<xml>' . self::xmlByArr($arr) . '</xml>';
    }

    /**
     * arr转xml
     * @staticvar string $xml_str
     * @param type $arr
     * @return string
     */
    public static function xmlByArr($arr)
    {
        static $xml_str = '';
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml_str .= "<" . $key . ">" . $val . "</" . $key . ">";
            } elseif (is_array($val)) {
                $xml_str .= "<" . $key . ">";
                self::xmlByArr($val);
                $xml_str .= "</" . $key . ">";
            } else {
                $xml_str .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        return $xml_str;
    }

    /**
     * xml转obj
     * @param $xml
     * @return \SimpleXMLElement
     */
    public static function xmlToObj($xml)
    {
        libxml_disable_entity_loader(true);
        return simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    /**
     * post to
     * @param $url
     * @param $data
     * @param string $header
     * @return mixed
     */
    public static function postTo($url, $data, $header = '')
    {
        $ch = curl_init();
        //设置header
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url); // url
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // json数据
        $dataJson = curl_exec($ch); // 返回值
        curl_close($ch);
        return $dataJson;
    }
}