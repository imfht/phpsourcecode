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
namespace Tang\Web\Ip\Location\Drivers;
use Tang\Util\WebClient;
use Tang\Web\Ip\Location\IpLocation;
use Tang\Web\Ip\Location\LocationFailureException;

class Sina
{
    /**
     * 请求对象
     * @var WebClient
     */
    protected $webClient;

    /**
     * @param WebClient $webClient
     */
    public function __construct(WebClient $webClient)
    {
        $this->webClient = $webClient;
    }

    /**
     * @param $ip
     * @return IpLocation
     * @throws \Tang\Web\Ip\Location\LocationFailureException
     */
    public function getLocation($ip)
    {
        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip;
        $string = $this->webClient->downloadString($url);
        $json = json_decode($string,true);
        if($json && $json['ret'] == 1)
        {
            return new IpLocation($ip,$json['country'].$json['province'],$json['isp']);
        } else
        {
            throw new LocationFailureException($ip);
        }
    }
}