<?php

/*
 *  @author myf
 *  @date 2014-11-25
 *  @Description 淘宝IP
 */

namespace Minyifei\Lib;

use Myf\Mvc\Http;

class MyfIp{
    
    /**
     * 获取ip信息
     * @return 
     */
    public static function getIpInfo(){
        $ip = getClientIP();
        if(!empty($ip)){
            $url = "http://ip.minyifei.cn/";
            $response = Http::get($url,array("ip"=>$ip));
            if($response){
                $json = json_decode($response,true);
                return $json["data"];
            }
        }
        return null;
    }
    
}