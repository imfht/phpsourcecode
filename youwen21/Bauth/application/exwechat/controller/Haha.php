<?php

namespace app\exwechat\controller;

use think\Controller;
use youwen\exwechat\api\accessToken;
use youwen\exwechat\api\account\QRCode;
use youwen\exwechat\api\account\shortUrl;
use youwen\exwechat\api\ips;
use youwen\exwechat\api\menu\menu;
use youwen\exwechat\api\user\user;
use youwen\exwechat\api\custom\custom;
use youwen\exwechat\api\message\message;

class haha extends Controller
{
    // 未通
    public function messageSend()
    {
        $openid = Array();
        // $openid[0] = 'o5dxUt74EkMKka6V3iCJLsk4uc78';
        // $openid[1] = 'o5dxUt2DCXa5wDLLv0dbzfDv-whU';
        // $openid[2] = 'o5dxUtwP4BsWpTEUQKg8kjlnubX0';
        // $openid[3] = 'o5dxUt7QcpRljb72J6OoDsVZEW_4';
        // $openid[4] = 'o5dxUt9uyNazLJoM99D7yLb_TCmM';
        // $openid[5] = 'o5dxUt8SRhTrjD9rGEoLlN9q1uUQ';
        // $openid[6] = 'o5dxUt4oiWnhQSQP71hTO6xHeN_g';
        // $openid[7] = 'o5dxUtyNm4G-3XRyr42O_Q4yOT9Y';
        // $openid[8] = 'o5dxUt1hd6SxQpQTInwG4LZ41BUo';
        // $openid[9] = 'o5dxUt1S4MJVFru5kXqX7BaQw84w';
        // $openid[10] = 'o5dxUtxSlxDXzPl3040lecLcRS_c';
        // $openid[11] = 'o5dxUt8pW68kVo7-p2YeG1UCOqfs';
        // $openid[12] = 'o5dxUtwHq46WIU5_K4S6q-R6KM4A';
        // $openid[13] = 'o5dxUt3QaeCWbjI9qGyeJyHmm_Gc';
        // $openid[14] = 'o5dxUtzcJa6PdO-SsLCQtDaxaI7g';
        // $openid[15] = 'o5dxUt1UStzJjWQas1La5Dr6I5gU';
        // $openid[16] = 'o5dxUt0h_2wzgVBULQxbAQ2194AI';
        // $openid[17] = 'o5dxUt5CZl0YtL8EMpoYgNyBzqr0';
        // $openid[18] = 'o5dxUt2SWZ90iY1VVL7CexBdiDFU';
        // $openid[19] = 'o5dxUt9Eq-oJqG6XUwVpByasMk-o';
        // $openid[20] = 'o5dxUt8JFOWQOROfZodgJJY9B0v0';
        // $openid[21] = 'o5dxUt8eTUy-tJ5OZHJqhZpdn7qE';
        // $openid[22] = 'o5dxUt6VYGN75HyY6x8LOex_eqfM';
        // $openid[23] = 'o5dxUt5r363mZvaCfhoAm3ORC9gQ';
        // $openid[24] = 'o5dxUt6kR2i0Juxh-KXz7SLwsLF8';
        // $openid[25] = 'o5dxUt5ilri_cOfIJ-N3C6_ih-hc';
        // $openid[26] = 'o5dxUty_mSfC6v5GDOYfSsoYQf48';
        // $openid[27] = 'o5dxUt27cwbovAQwHHy0a0JWW8Lo';
        $openid[28] = 'o5dxUt4XMyD4R9jLrkeKhhFnMYKA';
        // $openid[29] = 'o5dxUtxQvQ3KlsW8T-7yrq_9Xeto';
        // $openid[30] = 'o5dxUtw1_Sa0oFz0mzD8vbJHHd0U';
        // $openid[31] = 'o5dxUt4Q_tpo80U5bn2SaGQC5Dtw';
        // $openid[32] = 'o5dxUt6rKvwM_vUiYVzYmJ20lHaw';
        // $openid[33] = 'o5dxUt-3LruObQ1HkbTfS7JOLdNg';
        // $openid[34] = 'o5dxUt4UzAmiB6s1f3Zu81O_AF6E';
        // $openid[35] = 'o5dxUtwZa3M597glt3TISTlFpwn4';
        // $openid[36] = 'o5dxUtyQ7ZDjCLZ3ODlMHXPiq0vs';
        // $openid[37] = 'o5dxUt_hyxX389PfFYUlaXomiOGA';
        // $openid[38] = 'o5dxUt_Bv1AsneSCmDR6bP3Rz85Q';
        // $openid[39] = 'o5dxUt9wuecR_V7UrkgaszXRs404';
        // $openid[40] = 'o5dxUt6rLOX3f0a9x-7k5fWG5a4w';
        $data = [];
        $data['touser'][] = implode(',', $openid);
        $data['msgtype'] = 'text';
        $data['text']['content'] = '微信开发重构，exwechat开发库包测试。高级菜单功能，个性化菜单功能 可根据1、用户标签（开发者的业务需求可以借助用户标签来完成）2、性别3、手机操作系统4、地区（用户在微信客户端设置的地区）5、语言（用户在微信客户端设置的语言） 显示不同的公众号菜单';
        echo '<pre>';
        print_r( $data );
        exit('</pre>');
        $class = new message($_GET['token']);
        $ret = $class->send($data);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    // 未通
    public function messageSendall()
    {
//         '{
//    "filter":{
//       "is_to_all":false
//       "group_id":"2"
//    },
//    "text":{
//       "content":"CONTENT"
//    },
//     "msgtype":"text"
// }';
        $data['filter']['is_to_all'] = 'true';
        $data['filter']['group_id'] = '0';
        $data['msgtype'] = 'text';
        $data['text']['content'] = '微信开发重构，exwechat开发库包测试。高级菜单功能，个性化菜单功能 可根据1、用户标签（开发者的业务需求可以借助用户标签来完成）2、性别3、手机操作系统4、地区（用户在微信客户端设置的地区）5、语言（用户在微信客户端设置的语言） 显示不同的公众号菜单';
        // echo '<pre>';
        // print_r( json_encode($data, JSON_UNESCAPED_UNICODE) );
        // exit('</pre>');
        $class = new message($_GET['token']);
        $ret = $class->sendall($data);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    // 测试未通过  需要一个正确的media_id
    public function messageUploadNews()
    {
        $news = [
            [
                "thumb_media_id"=>"qI6_Ze_6PtV7svjolgs-rN6stStuHIjs9_DidOHaj0Q-mwvBelOXCFZiq2OsIU-p",
                "author"=>"xxx",
                "title"=>"Happy Day",
                "content_source_url"=>"www.qq.com",
                "content"=>"content",
                "digest"=>"digest",
                "show_cover_pic"=>"1"
            ]
        ];
        $data['articles'] = $news;
        $class = new message($_GET['token']);
        $ret = $class->uploadnews($data);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function messageUploadimg()
    {
        $class = new message($_GET['token']);
        $ret = $class->uploadimg($_GET['img']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function customList()
    {
        $class = new custom($_GET['token']);
        $ret = $class->customList();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function menuCreate()
    {
        $data = [];
        $data[0] = [
            "type" => "click",
            "name" => "今日歌曲",
            "key" => "V1001_TODAY_MUSIC",
        ];
        $data[1] = [
            "name" => "高级1",
            "sub_button" => [
                [
                    "type"=> "scancode_waitmsg", 
                    "name"=> "扫码带提示", 
                    "key"=> "rselfmenu_0_0", 
                    "sub_button"=>[ ]
                ],
                [
                    "type"=> "scancode_push", 
                    "name"=> "扫码推事件", 
                    "key"=> "rselfmenu_0_1", 
                    "sub_button"=> [ ]
                ],
                [
                    "type"=> "pic_sysphoto", 
                    "name"=> "系统拍照发图", 
                    "key"=> "rselfmenu_1_0", 
                    "sub_button"=> [ ]
                ],
                [
                    "type"=> "pic_photo_or_album", 
                    "name"=> "拍照或相册发图", 
                    "key"=> "rselfmenu_1_1", 
                    "sub_button"=> [ ]
                ],
                [
                    "type"=>"pic_weixin", 
                    "name"=>"微信相册发图", 
                    "key"=>"rselfmenu_1_2", 
                    "sub_button"=>[ ]
                ],
            ],
        ];
        $data[2] = [
            "name" => "高级2",
            "sub_button" => [
                [
                    "name"=> "发送位置", 
                    "type"=> "location_select", 
                    "key"=> "rselfmenu_2_0"
                ],
                [
                    "type"=> "media_id", 
                    "name"=> "获取一张图片", 
                    "media_id"=> "2l6HDOnKdL_nRpmM1svLC_iL2i7dRxREGiT2JC3tKgU"
                ],
                [
                    "type"=> "view_limited", 
                    "name"=> "打开某图文消息", 
                    "media_id"=> "2l6HDOnKdL_nRpmM1svLCy3vdrvmeT2WoC7PLyHIJQI"
                ],
                [
                    "type" => "view",
                    "name" => "bauth",
                    "url" => "http://demo.exwechat.com/",
                ]
            ],
        ];
        $menu['button'] = $data;
        // echo '<pre>';
        // print_r( json_encode($menu) );
        // exit('</pre>');
        $class = new menu($_GET['token']);
        $ret = $class->create($menu);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    public function menuDelete()
    {
        $class = new menu($_GET['token']);
        $ret = $class->delete();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function menuInfo()
    {
        $class = new menu($_GET['token']);
        $ret = $class->menuInfo();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function menuGet()
    {
        $class = new menu($_GET['token']);
        $ret = $class->get();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function qrCode()
    {
        $class = new QRCode($_GET['token']);
        $ret = $class->temporaryQR($_GET['scene_id']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function shortUrl()
    {
        $class = new shortUrl($_GET['token']);
        $ret = $class->create($_GET['url']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function remark()
    {
        $token = $_GET['token'];
        $data['openid'] = $_GET['openid'];
        $data['remark'] = $_GET['remark'];
        $class = new user($token);
        $ret = $class->remark($data['openid'], $data['remark']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function getUserBlackList()
    {
        $token = $_GET['token'];
        // $openid = $_GET['openid'];
        $class = new user($token);
        $ret = $class->getBlackList();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    public function getUserInfo()
    {
        $token = $_GET['token'];
        $openid = $_GET['openid'];
        $class = new user($token);
        $ret = $class->getUserInfo($openid);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function getUser()
    {
        $token = $_GET['token'];
        $class = new user($token);
        $ret = $class->getUsers();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function ips()
    {
        $token = $_GET['token'];
        $class = new ips($token);
        $ret = $class->getIps();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function accessToken()
    {
        $appid = 'wx70fe57dfaad1a35f';
        $appsecret = '62df1a5d360ffbe8c8a305b5a712f61e';

        $access = new accessToken($appid, $appsecret);
        $token = $access->getAccessToken();
        echo '<pre>';
        print_r($token);
        exit('</pre>');
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        $handler = new FileSessionHandler();
        session_set_save_handler(
            array($handler, 'open'),
            array($handler, 'close'),
            array($handler, 'read'),
            array($handler, 'write'),
            array($handler, 'destroy'),
            array($handler, 'gc')
        );

        // the following prevents unexpected effects when using objects as save handlers
        register_shutdown_function('session_write_close');

        session_start();
        // proceed to set and retrieve values by key from $_SESSION
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function xx()
    {
        $handler = new FileSessionHandler();
        session_set_save_handler($handler, true);
        session_start();
    }

}
