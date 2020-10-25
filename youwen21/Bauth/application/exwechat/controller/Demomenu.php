<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\menu\menu;

/**
 * 菜单 案例
 */
class Demomenu
{
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
}
