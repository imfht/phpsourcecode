<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Extend;

/**
 * Trait Tool 工具类
 * @package wechat\lib
 */
trait Tool
{
    /**
     * 接口 json 成功输出
     * @param string $msg 输出内容，输出参数~
     * @param array $data
     */

    public static function success($msg = '操作成功', array $data = [])
    {
        if (is_array($msg)){
            Json::success('操作成功~', $data);
        }
        Json::success($msg, $data);
    }

    /**
     * 接口 json 失败输出
     * @param string $msg
     */
    public static function error(string $msg = '操作失败')
    {
        Json::error($msg);
    }

    /**
     * 重载路由
     * @param string $url
     * @param array $params
     */
    public static function header(string $url, array $params = []): void
    {
        Request::header($url, $params);
    }

    /**
     * curl 发送 POST 请求
     * @param string $url
     * @param array $params
     * @return array
     */
    public static function post(string $url,$params = [])
    {
        return Request::request('POST',$url,$params);
    }

    /**
     * curl 发送 GET 请求
     * @param string $url
     * @param array $params
     * @return array
     */
    public static function get(string $url,array $params = [])
    {
        return Request::request('GET',$url,$params);
    }

    /**
     * url拼接数组
     * @param array $params
     * @return string
     */
    public static function url_splice_array(array $params = [])
    {
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 创建唯一字符
     * @param string $strBlur   原字符
     * @param string $strType   加密方式 ：[w所有|s字符|d数字]
     * @param int $strLen   返回字符长度，建议大于16位
     * @return string   字符串
     */
    public static function randOnlyStr(string $strBlur = '',string $strType = 'w',int $strLen = 18):string
    {
        $dStr = '0123456789';
        $sStr = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $wStr = '!$&()*,/:;=?@-._~';
        $strBlurLen = (strlen(static::uniqueString($strBlur)) + 1) == 1 ? 0 : strlen(static::uniqueString($strBlur)) + 1;
        $strSuffix = $strBlurLen > 0 ? '#'.static::uniqueString($strBlur) : '';
        switch ($strType)
        {
            case 's': # 字符串
                return static::getGapStrByStr($sStr,$strLen - $strBlurLen).$strSuffix;
                break;
            case 'd': # 数字
                return static::getGapStrByStr($dStr,$strLen - $strBlurLen).$strSuffix;
                break;
            case 'w': # 匹配包括下划线的任何单词字符。等价于“[A-Za-z0-9_]”。
                return static::getGapStrByStr($dStr.$sStr.$wStr,$strLen - $strBlurLen).$strSuffix;
                break;
            default : # 默认大小写字母
                return static::getGapStrByStr($sStr,$strLen - $strBlurLen).$strSuffix;
                break;
        }
    }

    /**
     * 获取对应字符
     * @param string $str 字符串
     * @param int $strLen 长度
     * @return string 随机字符串
     */
    public static function getGapStrByStr(string $str = '', int $strLen = 18)
    {
        static $newStr = '';
        static $i = 0;
        if ($i < $strLen)
        {
            $newStr .= $str[rand(0,strlen($str))];
            $i ++;
            static::getGapStrByStr($str,$strLen);
        }
        return $newStr;
    }



    /**
     * 生成唯一字符串
     * @param $type $type 类型
     * @return string   字符串
     */
    public static function uniqueString(string $type)
    {
        return bin2hex($type);
    }

    /**
     * 获取唯一字符串类型
     * @param $string $string  唯一字符串
     * @return bool|string  返回结果：字符串或者false
     */
    public static function uniqueType(string $string)
    {
        return hex2bin($string);
    }


    /**
     * 小程序检验数据的真实性，并且获取解密后的明文.
     * @param string $appID 加密的用户数据
     * @param string $sessionKey 与用户数据一同返回的初始向量
     * @param string $encryptedData 解密后的原文
     * @param string $iv 成功0，失败返回对应的错误码
     * @return string
     */
    public static function decryptData(string $appID, string $sessionKey, string $encryptedData, string $iv )
    {
        if (strlen($sessionKey) != 24) return '4';
        if (strlen($iv) != 24) return '3';

        $aesKey = base64_decode($sessionKey);
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher,"AES-128-CBC",$aesKey,1,$aesIV);
        $dataObj = json_decode($result,true);
        if( $dataObj  == NULL ) return '2';
        if( $dataObj['watermark']['appid'] != $appID ) return '1';
        return $result;
    }

}