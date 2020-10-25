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
/**
 * Interface IClientIp
 * @package Tang\Web\Ip
 */
interface IClientIp
{
    /**
     * 获取IP地址
     * @return string
     */
    public function getIp();

    /**
     * 获取ip2long
     * @return int
     */
    public function getLong();

    /**
     * 返回IP定位
     * @return Location\IpLocation
     */
    public function getLocation();
}