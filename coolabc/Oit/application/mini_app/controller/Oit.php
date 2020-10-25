<?php
namespace app\mini_program_app\controller;

use think\Cache;
use think\Log;
use GuzzleHttp\Client;

/**
 * Class OitMiniPro
 * oit小程序
 * @package app\mini_program_app\controller
 */
class Oit {
    /**
     * @return string
     */
    public function index() {

        return json(['a' => 'abc', 'b' => 'bcd']);
    }

    /**
     * 微信小程序提交 code 与微信服务器交换openid 与 session_key
     * @return \think\response\Json
     */
    public function login() {
        $js_code = input('code');
        $config = [
            'appid' => 'wx1536975ac294b81f',
            'secret' => 'd34a52f4ab764579f892b3d4a9e7feb9',
            'grant_type' => 'authorization_code'
        ];
        //$wei_host = "https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code";
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $config['appid'] . "&secret=" . $config['secret'];
        $url .= "&js_code=" . $js_code . "&grant_type=" . $config['grant_type'];

        /*
        function getcurl($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $content = curl_exec($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($status == 404) {
                return "";
            }
            return $content;
        }
        $content = getcurl($url);
        */

        $client = new Client();
        $res = $client->request('GET', $url);
        $body = $res->getBody();
        $content = $body->getContents();
        $openid_session = json_decode($content, true);
        Log::write($openid_session['openid'], "notice");

        if(array_key_exists('errcode',$openid_session)) {
            return json($openid_session);
        }

        // 记录用户登录成功

        // 生成本服务器的第三方缓存
        $session_key = uniqid("mini", true);
        Cache::set($session_key, $openid_session, 20);
        Log::write($session_key, "notice");

        Log::write($content, "notice");
        return json(['session_key' => $session_key]);
    }

}
