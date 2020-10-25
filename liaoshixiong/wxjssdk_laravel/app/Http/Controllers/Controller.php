<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Routing\Controller as BaseController;
use EasyWeChat\Foundation\Application;

class Controller extends BaseController
{
    public function index(Request $request){
        $url = ($request->input('url'));

        //是否开启debug模式
        if(env('APP_DEBUG', 'false')){
            return $this->__getJssdkContent($url);
        }

        //判断是否有缓存
        if(Cache::has($url)){
           return Cache::get($url);
        }

        $data = $this->__getJssdkContent($url);

        //设置过期时间
        $expiresAt = Carbon::now()->addDays(7);
        Cache::put($url, $data, $expiresAt);

        return $data;
    }


    private function __getJssdkContent($url){
        $options = [
            'app_id'  => env('WX_APPID'),         // AppID
            'secret'  => env('WX_APPSECRET'),     // AppSecret
            'token'   => '',          // Token
            'aes_key' => '',                    // EncodingAESKey
        ];

        $app = new Application($options);
        $js = $app['js'];
        $js->setUrl($url);

        $jsContent = $js->config([
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone',
            'startRecord',
            'stopRecord',
            'onVoiceRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'onVoicePlayEnd',
            'uploadVoice',
            'downloadVoice',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'translateVoice',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'closeWindow',
            'scanQRCode',
            'chooseWXPay',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard'
        ], env('WX_JSSDK_DEBUG', 'false'));

        return 'wx.config('. $jsContent .');';
    }
}
