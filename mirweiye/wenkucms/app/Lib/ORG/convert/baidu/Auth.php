<?php
/*
* Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/
class HttpUtil
{
    // 根据RFC 3986，除了：
    //   1.大小写英文字符
    //   2.阿拉伯数字
    //   3.点'.'、波浪线'~'、减号'-'以及下划线'_'
    // 以外都要编码
    public static $PERCENT_ENCODED_STRINGS;
    //填充编码数组
    public static function __init()
    {
        HttpUtil::$PERCENT_ENCODED_STRINGS = array();
        for ($i = 0; $i < 256; ++$i) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[$i] = sprintf("%%%02X", $i);
        }
        //a-z不编码
        foreach (range('a', 'z') as $ch) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }
        //A-Z不编码
        foreach (range('A', 'Z') as $ch) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }
        //0-9不编码
        foreach (range('0', '9') as $ch) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }
        //以下4个字符不编码
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('-')] = '-';
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('.')] = '.';
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('_')] = '_';
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('~')] = '~';
    }
    //在uri编码中不能对'/'编码
    public static function urlEncodeExceptSlash($path)
    {
        return str_replace("%2F", "/", HttpUtil::urlEncode($path));
    }
    //使用编码数组编码
    public static function urlEncode($value)
    {
        $result = '';
        for ($i = 0; $i < strlen($value); ++$i) {
            $result .= HttpUtil::$PERCENT_ENCODED_STRINGS[ord($value[$i])];
        }
        return $result;
    }
    //生成标准化QueryString
    public static function getCanonicalQueryString(array $parameters)
    {
        //没有参数，直接返回空串
        if (count($parameters) == 0) {
            return '';
        }
        $parameterStrings = array();
        foreach ($parameters as $k => $v) {
            //跳过Authorization字段
            if (strcasecmp('Authorization', $k) == 0) {
                continue;
            }
            if (!isset($k)) {
                throw new \InvalidArgumentException(
                    "parameter key should not be null"
                    );
            }
            if (isset($v)) {
                //对于有值的，编码后放在=号两边
                $parameterStrings[] = HttpUtil::urlEncode($k)
                . '=' . HttpUtil::urlEncode((string) $v);
            } else {
                //对于没有值的，只将key编码后放在=号的左边，右边留空
                $parameterStrings[] = HttpUtil::urlEncode($k) . '=';
            }
        }
        //按照字典序排序
        sort($parameterStrings);
        //使用'&'符号连接它们
        return implode('&', $parameterStrings);
    }
    //生成标准化uri
    public static function getCanonicalURIPath($path)
    {
        //空路径设置为'/'
        if (empty($path)) {
            return '/';
        } else {
            //所有的uri必须以'/'开头
            if ($path[0] == '/') {
                return HttpUtil::urlEncodeExceptSlash($path);
            } else {
                return '/' . HttpUtil::urlEncodeExceptSlash($path);
            }
        }
    }
    //生成标准化http请求头串
    public static function getCanonicalHeaders($headers)
    {
        //如果没有headers，则返回空串
        if (count($headers) == 0) {
            return '';
        }
        $headerStrings = array();
        foreach ($headers as $k => $v) {
            //跳过key为null的
            if ($k === null) {
                continue;
            }
            //如果value为null，则赋值为空串
            if ($v === null) {
                $v = '';
            }
            //trim后再encode，之后使用':'号连接起来
            $headerStrings[] = HttpUtil::urlEncode(strtolower(trim($k))) . ':' . HttpUtil::urlEncode(trim($v));
        }
        //字典序排序
        sort($headerStrings);
        //用'\n'把它们连接起来
        return implode("\n", $headerStrings);
    }
}
HttpUtil::__init();
function generateAuthorization($ak, $sk, $method, $host, $uri, $params, $timestamp, $expirationInSeconds) {
    $timeStr = $timestamp->format("Y-m-d\TH:i:s\Z");
    $authStringPrefix = "bce-auth-v1/{$ak}/{$timeStr}/{$expirationInSeconds}";
    $signingKey = hash_hmac('SHA256', $authStringPrefix, $sk);
    $canonicalHeader1 = "host;x-bce-date";
    $canonicalHeader2 = "host:{$host}\n" . "x-bce-date:" . urlencode($timeStr);
    $httpUtil = new HttpUtil();
    $canonicalString = $httpUtil->getCanonicalQueryString($params);
    $canonicalUri = $httpUtil->getCanonicalURIPath($uri);
    $method = strtoupper($method);
    $canonicalRequest = "{$method}\n{$canonicalUri}\n{$canonicalString}\n{$canonicalHeader2}";
    $signature = hash_hmac('SHA256', $canonicalRequest, $signingKey);
    $authorization = "bce-auth-v1/{$ak}/{$timeStr}/{$expirationInSeconds}/{$canonicalHeader1}/{$signature}";
    return $authorization;
}
?>
