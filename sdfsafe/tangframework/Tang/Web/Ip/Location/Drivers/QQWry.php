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
use Tang\Web\Ip\Location\IpLocation;

/**
 * QQ纯真IP库
 * Class QQWry
 * @package Tang\Web\Ip\Location\Drivers
 */
class QQWry
{
    /**
     * 纯真对象
     * @var \QQWry
     */
    private $qqWry;
    public function __construct(\QQWry $qqWry)
    {
        $this->qqWry = $qqWry;
    }

    /**
     * 获取IpLocation
     * @param $ip
     * @return IpLocation
     */
    public function getLocation($ip)
    {
        $result = $this->qqWry->getLocation($ip);
        if($result)
        {
            return new IpLocation($ip,$result['country'],$result['area']);
        }
    }
}