<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class MsgAnalyseKit
{
    /**
     * 获取消息发送概况数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为7天
     * @return \WxSDK\core\common\Ret data:
     * {
            * "list": [
            * {
            * "ref_date": "2014-12-07",
            * "msg_type": 1,
            * "msg_user": 282,
            * "msg_count": 817
            * }//后续还有同一ref_date的不同msg_type的数据，以及不同ref_date（在时间范围内）的数据
            * ]
        * }
     */
    public static function getUpstreamMsg(IApp $App, string $beginDate, string  $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$msg_get_upstream_msg);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }
    /**
     * 获取消息发送分时数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为1天
     * @return \WxSDK\core\common\Ret data:
     * {
            * "list": [
            * {
            * "ref_date": "2014-12-07",
            * "ref_hour": 0,
            * "msg_type": 1,
            * "msg_user": 9,
            * "msg_count": 10
            * }//后续还有同一ref_hour的不同msg_type的数据，以及不同ref_hour的数据，ref_date固定，因为最大时间跨度为1
            * ]
        * }
     */
    public static function getUpstreamMsgHour(IApp $App, string $beginDate, string  $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$msg_get_upstream_msg_hour);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }

    /**
     * 获取消息发送周数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为30天
     * @return \WxSDK\core\common\Ret data:
     * {
    "list": [
    {
    "ref_date": "2014-12-08",
    "msg_type": 1,
    "msg_user": 16,
    "msg_count": 27
    }    //后续还有同一ref_date下不同msg_type的数据，及不同ref_date的数据
    ]
    }
     */
    public static function getUpstreamMsgWeek(IApp $App, string $beginDate, string  $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$msg_get_upstream_msg_week);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }

    /**
     * 获取消息发送月数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为30天
     * @return \WxSDK\core\common\Ret data:
     * {
    "list": [
    {
    "ref_date": "2014-11-01",
    "msg_type": 1,
    "msg_user": 7989,
    "msg_count": 42206
    }//后续还有同一ref_date下不同msg_type的数据，及不同ref_date的数据
    ]
    }
     */
    public static function getUpstreamMsgMonth(IApp $App, string $beginDate, string  $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$msg_get_upstream_msg_month);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }

    /**
     * 获取消息发送分布数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为15天
     * @return \WxSDK\core\common\Ret data:
     * {
    "list": [
    {
    "ref_date": "2014-12-07",
    "count_interval": 1,
    "msg_user": 246
    }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
    ]
    }
     */
    public static function getUpstreamMsgDist(IApp $App, string $beginDate, string  $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$msg_get_upstream_msg_dist);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }

    /**
     * 获取消息发送分布周数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为30天
     * @return \WxSDK\core\common\Ret data:
     * {
    "list": [
    {
    "ref_date": "2014-12-07",
    "count_interval": 1, //当日发送消息量分布的区间，0代表 “0”，1代表“1-5”，2代表“6-10”，3代表“10次以上”
    "msg_user": 246
    }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
    ]
    }
     */
    public static function getUpstreamMsgDistWeek(IApp $App, string $beginDate, string  $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$msg_get_upstream_msg_dist_week);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }

    /**
     * 获取消息发送分布月
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为30天
     * @return \WxSDK\core\common\Ret data:
     * {
    "list": [
    {
    "ref_date": "2014-12-07",
    "count_interval": 1, //当日发送消息量分布的区间，0代表 “0”，1代表“1-5”，2代表“6-10”，3代表“10次以上”
    "msg_user": 246
    }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
    ]
    }
     */
    public static function getUpstreamMsgDistMonth(IApp $App, string $beginDate, string  $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$msg_get_upstream_msg_dist_month);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }
}