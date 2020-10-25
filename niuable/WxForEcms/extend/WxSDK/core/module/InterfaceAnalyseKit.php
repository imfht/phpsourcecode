<?php


namespace WxSDK\core\module;


use WxSDK\core\common\IApp;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class InterfaceAnalyseKit
{
    /**
     * 获取接口分析数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为30天
     * @return \WxSDK\core\common\Ret data:
     * {
            * "list": [
            * {
            * "ref_date": "2014-12-07",
            * "callback_count": 36974, //通过服务器配置地址获得消息后，被动回复用户消息的次数
            * "fail_count": 67, //上述动作的失败次数
            * "total_time_cost": 14994291, //总耗时，除以callback_count即为平均耗时
            * "max_time_cost": 5044 //最大耗时
            * }//后续还有不同ref_date（在begin_date和end_date之间）的数据
            * ]
        * }
     */
    public static function getSummary(IApp $App, string $beginDate, string $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$interface_get_summary);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }
    /**
     * 获取接口分析分时数据
     * @param IApp $App
     * @param string $beginDate 例如“2014-12-02”
     * @param string $endDate 与$beginTime最大时间跨度为1天
     * @return \WxSDK\core\common\Ret data:
     * {
            * "list": [
                * {
                * "ref_date": "2014-12-01",
                * "ref_hour": 0,
                * "callback_count": 331, //通过服务器配置地址获得消息后，被动回复用户消息的次数
                * "fail_count": 18, //上述动作的失败次数
                * "total_time_cost": 167870, //总耗时，除以callback_count即为平均耗时
                * "max_time_cost": 5042 //最大耗时
                * }//后续还有不同ref_hour的数据
            * ]
        * }
     */
    public static function getSummaryHour(IApp $App, string $beginDate, string $endDate){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$interface_get_summary_hour);
            $value = ["begin_date"=>$beginDate,
                "end_date"=>$endDate];
//             $value = json_encode($value);
            return Tool::doCurl($url,$value);
        }else{
            return $ret;
        }
    }
}