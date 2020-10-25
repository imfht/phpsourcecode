<?php

declare(strict_types=1);

namespace TencentAI\Kernel;

use Curl\Curl;
use TencentAI\Exception\TencentAIException;

/**
 * 发起网络请求
 */
class Request
{
    private static $app_key;

    private static $app_id;

    private static $format;

    private static $retry;

    public static $debug;

    /**
     * @var Curl
     */
    private static $curl;

    public static function setAppKey(string $app_key): void
    {
        self::$app_key = $app_key;
    }

    public static function setAppId($app_id): void
    {
        self::$app_id = $app_id;
    }

    public static function setRetry($retry): void
    {
        self::$retry = $retry;
    }

    /**
     * @param mixed $format
     */
    public static function setFormat(string $format): void
    {
        self::$format = $format;
    }

    public static function setCurl(Curl $curl, int $timeout): void
    {
        self::$curl = $curl;

        self::$curl->setTimeout($timeout);
    }

    public static function close(): void
    {
        self::$curl = null;
        self::$app_id = null;
        self::$app_key = null;
        self::$format = null;
    }

    /**
     * 生成签名.
     *
     * @param string $request_body
     *
     * @return string
     *
     * @see   https://ai.qq.com/doc/auth.shtml
     */
    private static function sign(string $request_body)
    {
        $app_key = self::$app_key;
        $sign = strtoupper(md5($request_body.'&app_key='.$app_key));

        return $sign;
    }

    /**
     * 逻辑处理.
     *
     * @param string $url
     * @param array  $arg
     * @param bool   $charSetUTF8
     * @param bool   $retry
     * @param bool   $post
     *
     * @return array
     *
     * @throws TencentAIException
     */
    public static function exec(string $url,
                                array $arg,
                                bool $charSetUTF8 = true,
                                bool $retry = false,
                                bool $post = true)
    {
        $retry_settings = 0;

        if (!$retry) {
            $retry_settings = self::$retry;
        }

        if (PHP_OS === 'WINNT') {
            self::$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        }
        $app_id = self::$app_id;
        $format = strtolower(self::$format);

        // @since 7.1
        $nonce_str = session_create_id();
        $data = [
            'app_id' => $app_id,
            'time_stamp' => time(),
            'nonce_str' => $nonce_str,
        ];
        $array = array_merge($data, $arg);
        ksort($array);
        $request_body = http_build_query($array);

        // 签名
        $sign = self::sign($request_body);

        // 最终请求体
        $data = $request_body."&sign=$sign";

        $request_url = 'https://api.ai.qq.com/fcgi-bin/'.$url;

        // 发起请求
        $method = $post ? 'post' : 'get';

        try {
            $json = self::$curl->$method($request_url, $data);

            if ($charSetUTF8) {
                $array = json_decode($json, true);
            } else {
                $json = mb_convert_encoding($json, 'utf8', 'gbk');
                $array = json_decode($json, true);
            }

            // 检查是否返回数组
            if (!\is_array($array)) {
                self::returnStr($json);
            }

            // 检查返回值
            self::checkReturn($array['ret']);
        } catch (\Throwable $e) {
            if (false === $retry) {
                for ($i = $retry_settings; $i > 0; --$i) {
                    self::$debug &&
                    file_put_contents(
                        sys_get_temp_dir().'/tencent_ai.log',
                        json_encode([
                            'url' => $url,
                            'retry' => $i,
                            'request_url' => $request_url,
                            'message' => $e->getMessage(),
                            'code' => $e->getCode(),
                        ], JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND
                    );

                    try {
                        $result = self::exec($url, $arg, $charSetUTF8, true);

                        return $result;
                    } catch (TencentAIException $e) {
                        if (1 === $i) {
                            throw new TencentAIException($e->getCode(), $e->getMessage());
                        }

                        continue;
                    }
                }

                throw new TencentAIException($e->getCode(), $e->getMessage());
            } else {
                throw new TencentAIException($e->getCode(), $e->getMessage());
            }
        }

        if ('json' === $format) {
            return json_encode($array, JSON_UNESCAPED_UNICODE);
        } else {
            return $array;
        }
    }

    /**
     * 结果返回字符串则抛出错误.
     *
     * @param $str
     *
     * @throws TencentAIException
     */
    public static function returnStr($str): void
    {
        throw new TencentAIException(90000, $str);
    }

    /**
     * 检查返回值，不为 0 抛出错误.
     *
     * @param int $ret
     *
     * @throws TencentAIException
     */
    private static function checkReturn(int $ret): void
    {
        if (0 !== $ret) {
            throw new TencentAIException($ret);
        }
    }
}
