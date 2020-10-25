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
namespace Tang\Web\Ip\Location;

use Tang\Interfaces\IToArray;

class IpLocation implements \JsonSerializable,IToArray
{
    /**
     * ip地址
     * @var string
     */
    private $ip;
    /**
     * 地址信息
     * @var string
     */
    private $address;
    /**
     * ISP运营商
     * @var string
     */
    private $isp;

    /**
     * @param $ip
     * @param $address
     * @param $isp
     */
    public function __construct($ip,$address,$isp)
    {
        $this->ip = $ip;
        $this->address = $address;
        $this->isp = $isp;
    }

    /**
     * 获取IP
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * 获取地址
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * 获取IP
     * @return string
     */
    public function getIsp()
    {
        return $this->isp;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
    public function toArray()
    {
        return array('ip' => $this->ip,'isp'=>$this->isp,'address'=>$this->address);
    }
    public function __toString()
    {
        return json_encode($this);
    }
}