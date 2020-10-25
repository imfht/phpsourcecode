<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class UserAnalyseKit
{
    /**
     * 获取用户增减数据
     * @param IApp $App
     * @param string $beginTime 例如“2014-12-02”
     * @param string $endTime 与$beginTime最大时间跨度为7天
     * @return \WxSDK\core\common\Ret data:
     * {
            * "list": [
            * {
            * "ref_date": "2014-12-07",
            * "user_source": 0,  //用户的渠道，数值代表的含义如下： 0代表其他合计 1代表公众号搜索 17代表名片分享 30代表扫描二维码 43代表图文页右上角菜单 51代表支付后关注（在支付完成页） 57代表图文页内公众号名称 75代表公众号文章广告 78代表朋友圈广告
            * "new_user": 0,
            * "cancel_user": 0
            * }//后续还有ref_date在begin_date和end_date之间的数据
            * ]
        * }
     */
    public static function getUserSummary(IApp $App, string $beginTime, string  $endTime){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$user_getusersummary);
            $value = ["begin_date"=>$beginTime,
                "end_date"=>$endTime];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }

    /**
     * 获取累计用户数据
     * @param IApp $App
     * @param string $beginTime
     * @param string $endTime
     * @return \WxSDK\core\common\Ret data:
     * {
            * "list": [
            * {
            * "ref_date": "2014-12-07",
            * "cumulate_user": 1217056 //总用户量
            * }, //后续还有ref_date在begin_date和end_date之间的数据
            * ]
        * }
     */
    public static function getUserCumulate(IApp $App, string $beginTime, string  $endTime){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$user_getusercumulate);
            $value = ["begin_date"=>$beginTime,
                "end_date"=>$endTime];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }
}