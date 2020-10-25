<?php

namespace App\Plugin;

use Ixudra\Curl\Facades\Curl;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 21:21
 */
class ExpressHelp
{
    /**
     * 自动识别哪个快递的
     * @param $code
     * @return bool
     */
    public static function getComCode($code)
    {
        $format = 'http://www.kuaidi100.com/autonumber/autoComNum?resultv2=1&text=%s';
        $json   = Curl::to(sprintf($format, $code))->get();
        if (json_decode($json)) {
            $autoName = json_decode($json);
            if (isset($autoName->auto)) {
                return $autoName->auto[0]->comCode;
            }
        }
        return false;
    }

    /**
     * 获取订单信息
     * @param $comCode
     * @param $code
     * @return mixed
     */
    public static function getInfo($comCode, $code)
    {
        $format = 'http://www.kuaidi100.com/query?type=%s&postid=%s&temp=' . time();
        $url    = sprintf($format, $comCode, $code);
        $json   = Curl::to($url)->get();
        $data   = json_decode($json, true);
        if (isset($data['status']) && $data['status'] == 200) {
            return $data;
        }
        return $data['message'];
    }
}