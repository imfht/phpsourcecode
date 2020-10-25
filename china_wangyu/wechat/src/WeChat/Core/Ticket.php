<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;

/**
 * Class WxTicket 微信ticket类 含签名生成
 * @package wechat
 */
class Ticket extends Base
{
    // 微信ticket (jsapi)
    private static $getTicketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi';

    /**
     * 设置微信ticket
     * @param string $accessToken 微信普通token
     * @return bool 微信 ticket|false
     */
    public static function gain(string $accessToken)
    {
        $param = \WeChat\Extend\File::param('ticket');
        if ($param === null or empty($param)) {

            // 准备数据
            static::$getTicketUrl = str_replace('ACCESS_TOKEN',$accessToken,static::$getTicketUrl);
            $result = self::get(static::$getTicketUrl);

            // 返回数据
            isset($result['ticket']) && \WeChat\Extend\File::param('ticket', $result);
            return $result;
        } else {
            return $param['ticket'];
        }
    }


    /**
     * 获取微信JSDK
     * @param string $ticket 获取微信JSDK签名
     * @param string $redirect_url 微信JSDK
     * @return mixed
     */
    public static function sign(string $ticket, string $redirect_url = '')
    {
        $url = empty($redirect_url) ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : $redirect_url;
        $timestamp = time();
        $nonceStr = self::createNonceStr();
        $string = 'jsapi_ticket=' . $ticket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $url;
        $param['rawString'] = $string;
        $param['signature'] = sha1($param['rawString']);
        $param['nonceStr'] = $nonceStr;
        $param['timestamp'] = $timestamp;
        $param['url'] = $url;
        return $param;
    }


    /**
     * 创建随机字符微信版本
     * @param int $length
     * @return string
     */
    private static function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
