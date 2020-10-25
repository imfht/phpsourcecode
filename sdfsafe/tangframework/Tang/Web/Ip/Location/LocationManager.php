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
use Tang\Manager\Manager;
use Tang\ThirdParty\ThirdPartyService;
use Tang\Util\WebClient;
use Tang\Web\Ip\Location\Drivers\QQWry;
use Tang\Web\Ip\Location\Drivers\Sina;
use Tang\Web\Ip\Location\Drivers\Taobao;

class LocationManager extends Manager implements ILocationManager
{
    public function createTaobaoDriver()
    {
        return new Taobao(new WebClient());
    }
    public function createSinaDriver()
    {
        return new Sina(new WebClient());
    }
    public function createQQWryDriver()
    {
        ThirdPartyService::getService()->import('QQWry.QQWry');
        return new QQWry(new \QQWry($this->config['QQWryDataPath']));
    }

    /**
     * 根据$name获取驱动
     * @param string $name
     * @return \Tang\Web\Ip\Location\ILocationDriver
     */
    public function driver($name = '')
    {
        return parent::driver($name);
    }
    /**
     * 获取IpLocation
     * @param $ip
     * @return IpLocation
     */
    public function getLocation($ip)
    {
        return $this->driver()->getLocation($ip);
    }
    protected function getIntreface()
    {
        return '\Tang\Web\Ip\Location\ILocationDriver';
    }
}