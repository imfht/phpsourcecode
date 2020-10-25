<?php

namespace WxSDK\core\module;


use WxSDK\core\common\IApp;
use WxSDK\core\model\card\Card;
use WxSDK\core\model\card\send\Card4LandingPage;
use WxSDK\core\model\card\send\SendCard;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class CardKit
{
    /**
     * @param IApp $accessToken
     * @param Card $card
     * @return \WxSDK\core\common\Ret data:{   "errcode":0,   "errmsg":"ok",   "card_id":"p1Pj9jr90_SQRaVqYI239Ka1erkI"}
     */
    public static function create(IApp $accessToken, Card $card)
    {
        $ret = $accessToken->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_create);
            $data = array(
                "card" => $card
            );
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 创建二维码，可扫一扫获取卡券
     * @param IApp $accessToken
     * @param SendCard $sendCard
     * @param int $expireSeconds 指定二维码的有效时间，范围是60 ~ 1800秒。不填默认为365天有效
     * @return \WxSDK\core\common\Ret data:{
     * "errcode": 0,
     * "errmsg": "ok",
     * "ticket":  "gQHB8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0JIV3lhX3psZmlvSDZmWGVMMTZvAAIEsNnKVQMEIAMAAA==",  //获取ticket后需调用换取二维码接口获取二维码图片，详情见字段说明。
     * "expire_seconds": 1800,
     * "url": "http://weixin.qq.com/q/BHWya_zlfioH6fXeL16o ",
     * "show_qrcode_url": " https://mp.weixin.qq.com/cgi-bin/showqrcode?  ticket=gQH98DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0czVzRlSWpsamlyM2plWTNKVktvAAIE6SfgVQMEgDPhAQ%3D%3D"
     * }
     */
    public static function createQrcode4GetCard(IApp $accessToken, SendCard $sendCard, int $expireSeconds = null)
    {
        $ret = $accessToken->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_qrcode_create);
            $data = array(
                "action_name" => "QR_CARD",
                "action_info" => ["card"=>Tool::removeNullInJson(json_encode($sendCard,JSON_UNESCAPED_UNICODE))],
            );
            if(null != $expireSeconds){
                $data["expire_seconds" ] = $expireSeconds;
            }
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
//             $data = Tool::removeNullInJson($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 创建二维码，可扫一扫获取多张卡券
     * @param IApp $accessToken
     * @param int $expireSeconds
     * @param SendCard ...$sendCard 领取多张的二维码一次最多填入5个card_id，否则报错
     * @return \WxSDK\core\common\Ret data:{
    * "errcode": 0,
    * "errmsg": "ok",
    * "ticket":  "gQHB8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0JIV3lhX3psZmlvSDZmWGVMMTZvAAIEsNnKVQMEIAMAAA==",  //获取ticket后需调用换取二维码接口获取二维码图片，详情见字段说明。
    * "expire_seconds": 1800,
    * "url": "http://weixin.qq.com/q/BHWya_zlfioH6fXeL16o ",
    * "show_qrcode_url": " https://mp.weixin.qq.com/cgi-bin/showqrcode?  ticket=gQH98DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0czVzRlSWpsamlyM2plWTNKVktvAAIE6SfgVQMEgDPhAQ%3D%3D"
    * }
     */
    public static function createQrcode4GetCards(IApp $App, int $expireSeconds =null, SendCard... $sendCard)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_qrcode_create);
            $data = array(
                "action_name" => "QR_MULTIPLE_CARD",
                "action_info" => array("multiple_card"=>array(
                    "card_list"=>$sendCard
                )),
            );
            if(null != $expireSeconds){
                $data["expire_seconds" ] = $expireSeconds;
            }
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
//             $data = Tool::removeNullInJson($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 创建卡券货架
     * @param IApp $App
     * @param string $bannerUrl 页面的banner图片链接，须调用，建议尺寸为640*300
     * @param string $page_title 页面的title
     * @param bool $can_share 页面是否可以分享,填入true/false
     * @param string $scene //投放页面的场景值； SCENE_NEAR_BY 附近 SCENE_MENU 自定义菜单 SCENE_QRCODE 二维码 SCENE_ARTICLE 公众号文章 SCENE_H5 h5页面 SCENE_IVR 自动回复 SCENE_CARD_CUSTOM_CELL 卡券自定义cell
     * @param Card4LandingPage ...$card4LandingPages 卡券列表，每个item有两个字段
     * @return \WxSDK\core\common\Ret
     */
    public static function createLandingPage(IApp $App, string $bannerUrl, string $page_title, bool $can_share, string $scene, Card4LandingPage... $card4LandingPages)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_landingpage_create);
            $data = array(
                "banner" => $bannerUrl,
                "page_title"=>$page_title,
                "can_share"=>$can_share,
                "scene"=>$scene,
                "card_list" => $card4LandingPages,
            );
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
//             $data = Tool::removeNullInJson($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 导入卡券的code
     * @param IApp $App
     * @param string $cardId
     * @param array $codes
     * @return \WxSDK\core\common\Ret data:
     * {
    * "errcode":0,
    * "errmsg":"ok",
    * "succ_code":2, //成功个数
    * "duplicate_code":1, //重复导入的code会自动被过滤
    * "fail_code":1, //失败个数
    * }
     */
    public static function importCode(IApp $App, string $cardId, array $codes)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_code_import);
            $data = array(
                "card_id" => $cardId,
                "code"=>$codes
            );
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }
    /**
     * 核查导入的code
     * @param IApp $App
     * @param string $cardId
     * @param array $codes
     * @return \WxSDK\core\common\Ret data:
     * {
    * "errcode":0,
    * "errmsg":"ok"
    * "exist_code":["11111","22222","33333"], //已经成功存入的code。
    * "not_exist_code":["44444","55555"] //没有存入的code
    * }
     */
    public static function importCodeCheck(IApp $App, string $cardId, array $codes)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_code_import_check);
            $data = array(
                "card_id" => $cardId,
                "code"=>$codes
            );
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 查询导入的code数量
     * @param IApp $App
     * @param string $cardId
     * @return \WxSDK\core\common\Ret data:{
    * "errcode":0,
    * "errmsg":"ok"，
    * "count":123
    * }
     */
    public static function getCodeCount(IApp $App,string $cardId)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_code_get_count);
            $data = array(
                "card_id" => $cardId,
            );
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }
    /**
     * 获取可插入微信图文的卡券html代码
     * @param IApp $App
     * @param string $cardId
     * @return \WxSDK\core\common\Ret data:
     * {
    * "errcode":0,
    * "errmsg":"ok",
    * "content":"<iframeclass=\"res_iframecard_iframejs_editor_card\"data-src=\"http: \/\/mp.weixin.qq.com\/bizmall\/appmsgcard?action=show&biz=MjM5OTAwODk4MA%3D%3D&cardid=p1Pj9jnXTLf2nF7lccYScFUYqJ0&wechat_card_js=1#wechat_redirect\">"
    * }
     */
    public static function getHtmlInNews(IApp $App,string $cardId)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_code_get_html_in_news);
            $data = array(
                "card_id" => $cardId,
            );
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 设置卡券白名单，用于领取测试的卡券
     * @param IApp $App
     * @param array|null $openIds 测试的openid列表。
     * @param array|null $userNames 测试的微信号列表
     * @return \WxSDK\core\common\Ret
     */
    public static function createWhiteList(IApp $App,array $openIds = null, array $userNames = null)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$card_code_create_test_white_list);
            $data = array( );
            if(null != $openIds){
                $data["openid"] = $openIds;
            }
            if(null != $userNames){
                $data["username"] = $userNames;
            }
//             $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }
}