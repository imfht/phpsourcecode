<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Web\Ip;
use Tang\Web\Ip\Location\LocationService;

class ClientIp implements IClientIp
{
    /**
     * IP地址
     * @var string
     */
    protected $ip;
    /**
     * IPV4数字地址
     * @var int
     */
    protected $long;
    /**
     * @var Location\IpLocation
     */
    protected $ipLocation;
    public function __construct()
    {
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif(isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['REMOTE_ADDR']))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else
        {
            $ip = '0.0.0.0';
        }
        $this->long = sprintf("%u",ip2long($ip));
        $this->ip = $this->long ? $ip : '0.0.0.0';
    }

    /**
     * 获取字符串IP地址
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * 返回IPV4地址数字
     * @return int
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * 获取IP位置
     * @return Location\IpLocation
     */
    public function getLocation()
    {
        if(!$this->ipLocation)
        {
            $this->ipLocation = LocationService::getService()->getLocation($this->ip);
        }
        return $this->ipLocation;
    }
}