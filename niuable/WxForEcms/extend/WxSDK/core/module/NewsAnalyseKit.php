<?php

namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class NewsAnalyseKit
{
    /**
     * 获取图文群发每日数据:获取某天所有被阅读过的文章（仅包括群发的文章）在当天的阅读次数等数据
     * @param IApp $App
     * @param string $beginData
     * @param string $endData 最大时间跨度为1天
     * @return \WxSDK\core\common\Ret data:
     * {
     * "list": [
     * {
     * "ref_date": "2014-12-08",
     * "msgid": "10000050_1",
     * "title": "12月27日 DiLi日报",
     * "int_page_read_user": 23676,
     * "int_page_read_count": 25615,
     * "ori_page_read_user": 29,
     * "ori_page_read_count": 34,
     * "share_user": 122,
     * "share_count": 994,
     * "add_to_fav_user": 1,
     * "add_to_fav_count": 3
     * }
     * //后续会列出该日期内所有被阅读过的文章（仅包括群发的文章）在当天的阅读次数等数据
     * ]
     * }
     */
    public static function getArticleSummary(IApp $App, string $beginData, string $endData)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$news_get_article_summary);
            $data = array(
                "begin_date" => $beginData,
                "end_date" => $endData,
            );
//             $data = json_encode($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 获取图文群发总数据:某天群发的文章，从群发日起到接口调用日（但最多统计发表日后7天数据），每天的到当天的总等数据。
     * @param IApp $App
     * @param string $beginData
     * @param string $endData 最大时间跨度为1天
     * @return \WxSDK\core\common\Ret data:
     * {
     * "list": [
     * {
     * "ref_date": "2014-12-14",
     * "msgid": "202457380_1",
     * "title": "马航丢画记",
     * "details": [
     * {
     * "stat_date": "2014-12-14",
     * "target_user": 261917,
     * "int_page_read_user": 23676,
     * "int_page_read_count": 25615,
     * "ori_page_read_user": 29,
     * "ori_page_read_count": 34,
     * "share_user": 122,
     * "share_count": 994,
     * "add_to_fav_user": 1,
     * "add_to_fav_count": 3,
     * "int_page_from_session_read_user": 657283,
     * "int_page_from_session_read_count": 753486,
     * "int_page_from_hist_msg_read_user": 1669,
     * "int_page_from_hist_msg_read_count": 1920,
     * "int_page_from_feed_read_user": 367308,
     * "int_page_from_feed_read_count": 433422,
     * "int_page_from_friends_read_user": 15428,
     * "int_page_from_friends_read_count": 19645,
     * "int_page_from_other_read_user": 477,
     * "int_page_from_other_read_count": 703,
     * "feed_share_from_session_user": 63925,
     * "feed_share_from_session_cnt": 66489,
     * "feed_share_from_feed_user": 18249,
     * "feed_share_from_feed_cnt": 19319,
     * "feed_share_from_other_user": 731,
     * "feed_share_from_other_cnt": 775
     * }, //后续还会列出所有stat_date符合“ref_date（群发的日期）到接口调用日期”（但最多只统计7天）的数据
     * ]
     * },//后续还有ref_date（群发的日期）在begin_date和end_date之间的群发文章的数据
     * ]
     * }
     */
    public static function getArticleTotal(IApp $App, string $beginData, string $endData)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$news_get_article_total);
            $data = array(
                "begin_date" => $beginData,
                "end_date" => $endData,
            );
//             $data = json_encode($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 获取图文统计数据
     * @param IApp $App
     * @param string $beginData
     * @param string $endData 最大时间跨度为3天
     * @return \WxSDK\core\common\Ret data:
     * {
    * "list": [
    * {
    * "ref_date": "2014-12-07",
    * "int_page_read_user": 45524,
    * "int_page_read_count": 48796,
    * "ori_page_read_user": 11,
    * "ori_page_read_count": 35,
    * "share_user": 11,
    * "share_count": 276,
    * "add_to_fav_user": 5,
    * "add_to_fav_count": 15
    * }, //后续还有ref_date在begin_date和end_date之间的数据
    * ]
    * }
     */
    public static function getUserRead(IApp $App, string $beginData, string $endData)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$news_get_user_read);
            $data = array(
                "begin_date" => $beginData,
                "end_date" => $endData,
            );
//             $data = json_encode($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 获取图文统计分时数据
     * @param IApp $App
     * @param string $beginData
     * @param string $endData 最大时间跨度为1天
     * @return \WxSDK\core\common\Ret data:
     * {
    * {
    * "list": [
    * {
    * "ref_date": "2015-07-14",
    * "ref_hour": 0,
    * "user_source": 0,
    * "int_page_read_user": 6391,
    * "int_page_read_count": 7836,
    * "ori_page_read_user": 375,
    * "ori_page_read_count": 440,
    * "share_user": 2,
    * "share_count": 2,
    * "add_to_fav_user": 0,
    * "add_to_fav_count": 0
    * },
    * {
    * "ref_date": "2015-07-14",
    * "ref_hour": 0,
    * "user_source": 1,
    * "int_page_read_user": 1,
    * "int_page_read_count": 1,
    * "ori_page_read_user": 0,
    * "ori_page_read_count": 0,
    * "share_user": 0,
    * "share_count": 0,
    * "add_to_fav_user": 0,
    * "add_to_fav_count": 0
    * },
    * {
    * "ref_date": "2015-07-14",
    * "ref_hour": 0,
    * "user_source": 2,
    * "int_page_read_user": 3,
    * "int_page_read_count": 3,
    * "ori_page_read_user": 0,
    * "ori_page_read_count": 0,
    * "share_user": 0,
    * "share_count": 0,
    * "add_to_fav_user": 0,
    * "add_to_fav_count": 0
    * },
    * {
    * "ref_date": "2015-07-14",
    * "ref_hour": 0,
    * "user_source": 4,
    * "int_page_read_user": 42,
    * "int_page_read_count": 100,
    * "ori_page_read_user": 0,
    * "ori_page_read_count": 0,
    * "share_user": 0,
    * "share_count": 0,
    * "add_to_fav_user": 0,
     * "add_to_fav_count": 0
    * }
    * //后续还有ref_hour逐渐增大,以列举1天24小时的数据
    * ]
    * }
     */
    public static function getUserReadHour(IApp $App, string $beginData, string $endData)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$news_get_user_read_hour);
            $data = array(
                "begin_date" => $beginData,
                "end_date" => $endData,
            );
//             $data = json_encode($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 获取图文分享转发数据
     * @param IApp $App
     * @param string $beginData
     * @param string $endData 最大时间跨度为7天
     * @return \WxSDK\core\common\Ret data:
     * {
    * "list": [
    * {
    * "ref_date": "2014-12-07",
    * "share_scene": 1,
    * "share_count": 207,
    * "share_user": 11
    * },
    * {
    * "ref_date": "2014-12-07",
    * "share_scene": 5,
    * "share_count": 23,
     * "share_user": 11
     * }//后续还有不同share_scene（分享场景）的数据，以及ref_date在begin_date和end_date之间的数据
    * ]
    * }
     */
    public static function getUserShare(IApp $App, string $beginData, string $endData)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$news_get_user_share);
            $data = array(
                "begin_date" => $beginData,
                "end_date" => $endData,
            );
//             $data = json_encode($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }

    /**
     * 获取图文分享转发分时数据
     * @param IApp $App
     * @param string $beginData
     * @param string $endData 最大时间跨度为1天
     * @return \WxSDK\core\common\Ret data:
     * {
    * "list": [
    * {
    * "ref_date": "2014-12-07",
    * "ref_hour": 1200,
    * "share_scene": 1,
     * "share_count": 72,
     * "share_user": 4
    * }//后续还有不同share_scene的数据，以及ref_hour逐渐增大的数据。由于最大时间跨度为1，所以ref_date此处固定
    * ]
    * }
     */
    public static function getUserShareHour(IApp $App, string $beginData, string $endData)
    {
        $ret = $App->getAccessToken();
        if ($ret->ok()) {
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$news_get_user_share_hour);
            $data = array(
                "begin_date" => $beginData,
                "end_date" => $endData,
            );
//             $data = json_encode($data);
            return Tool::doCurl($url, $data);
        } else {
            return $ret;
        }
    }
}