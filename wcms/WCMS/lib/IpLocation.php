<?php

/**
 * IP 地理位置查询类  *  
 * @author wolf
 * @email 11631131@qq.com
 **/
class IpLocation
{

    function getlocation ($ip)
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ip = json_decode(file_get_contents($url));
        if ((string) $ip->code == '1') {
            return false;
        }
        return (array) $ip->data;
    }

    /**
     * 获取客户端ip
     *
     * @author wolf
     * @return string $ip
     */
    function getIP ()
    {
        if (@$_SERVER["HTTP_X_FORWARDED_FOR"])
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else 
            if (@$_SERVER["HTTP_CLIENT_IP"])
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            else 
                if (@$_SERVER["REMOTE_ADDR"])
                    $ip = $_SERVER["REMOTE_ADDR"];
                else 
                    if (@getenv("HTTP_X_FORWARDED_FOR"))
                        $ip = getenv("HTTP_X_FORWARDED_FOR");
                    else 
                        if (@getenv("HTTP_CLIENT_IP"))
                            $ip = getenv("HTTP_CLIENT_IP");
                        else 
                            if (@getenv("REMOTE_ADDR"))
                                $ip = getenv("REMOTE_ADDR");
                            else
                                $ip = "Unknown";
        return $ip;
    }
}
?>  
