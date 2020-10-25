<?php

namespace App\Module;

/**
 * Class Umeng
 * @package App\Module
 */
class Umeng
{

    /**
     * 推送通知
     * @param string $platform     ios|android
     * @param string $token        umeng token
     * @param string $title
     * @param string $desc
     * @param array $extra
     * @return array
     */
    public static function notification($platform, $token, $title, $desc, $extra = [])
    {
        if ($platform == 'ios') {
            $body = [
                'appkey' => env('UMENG_PUSH_IOS_APPKEY'),
                'timestamp' => Base::time(),
                'type' => 'unicast',
                'device_tokens' => $token,
                'payload' => array_merge([
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'subtitle' => '',
                            'body' => $desc,
                        ]
                    ],
                ], $extra),
            ];
        } else {
            $body = [
                'appkey' => env('UMENG_PUSH_ANDROID_APPKEY'),
                'timestamp' => Base::time(),
                'type' => 'unicast',
                'device_tokens' => $token,
                'payload' => [
                    'display_type' => 'notification',
                    'body' => [
                        'ticker' => $title,
                        'title' => $title,
                        'text' => $desc,
                    ],
                    'extra' => $extra,
                ],
            ];
        }
        //
        $res = self::curl($platform, 'https://msgapi.umeng.com/api/send', $body);
        if (Base::isError($res)) {
            return $res;
        } else {
            return Base::retSuccess('success');
        }
    }

    /**
     * 发送请求
     * @param $platform
     * @param $url
     * @param $body
     * @param string $method
     * @return array
     */
    private static function curl($platform, $url, $body, $method = 'POST')
    {
        if ($platform == 'ios') {
            $appkey = env('UMENG_PUSH_IOS_APPKEY');
            $secret = env('UMENG_PUSH_IOS_APPMASTERSECRET');
        } else {
            $appkey = env('UMENG_PUSH_ANDROID_APPKEY');
            $secret = env('UMENG_PUSH_ANDROID_APPMASTERSECRET');
        }
        if (empty($appkey)) {
            return Base::retError('no appkey');
        }
        if (empty($secret)) {
            return Base::retError('no secret');
        }
        //
        $postBody = json_encode($body);
        $mysign = md5($method . $url . $postBody . $secret);
        $url.= "?sign=" . $mysign;
        //
        $res = Ihttp::ihttp_request($url, $postBody);
        if (Base::isError($res)) {
            return $res;
        }
        $array = json_decode($res['data'], true);
        $debug = env('UMENG_PUSH_DEBUG');
        if ($debug === true || $debug === 'info' || ($debug === 'error' && $array['ret'] !== 'SUCCESS')) {
            $logFile = storage_path('logs/umeng-push-' . date('Y-m') . '.log');
            file_put_contents($logFile, "[" . date("Y-m-d H:i:s") . "]\n" . Base::array2string_discard([
                    'platform' => $platform,
                    'url' => $url,
                    'method' => $method,
                    'body' => $body,
                    'request' => $res['data'],
                ]) . "\n", FILE_APPEND);
        }
        if ($array['ret'] == 'SUCCESS') {
            return Base::retSuccess('success', $array['data']);
        } else {
            return Base::retError('error', $array['data']);
        }
    }
}
