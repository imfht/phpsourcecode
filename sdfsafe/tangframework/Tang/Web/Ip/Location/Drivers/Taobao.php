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

class Taobao
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
     * 获取IpLocation
     * @param $ip
     * @throws \Tang\Web\Ip\Location\LocationFailureException
     * @return IpLocation
     */
    public function getLocation($ip)
    {
        $url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
        $string = $this->webClient->downloadString($url);
        $json = json_decode($string,true);
        if($json['code'] == 0)
        {
            $data = $json['data'];
            return new IpLocation($ip,$data['country'].$data['region'],$data['isp']);
        } else
        {
            throw new LocationFailureException($ip);
        }
    }
}