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
namespace Tang\Crypt;
use Tang\Crypt\Drivers\Aes;
use Tang\Crypt\Drivers\BlowFish;
use Tang\Crypt\Drivers\Gost;
use Tang\Crypt\Drivers\Loki97;
use Tang\Crypt\Drivers\ThirdDes;
use Tang\Crypt\Drivers\Twofish;
use Tang\Crypt\Drivers\XTea;
use Tang\Manager\Manager;

/**
 * 加密管理器
 * Class CryptManager
 * @package Tang\Crypt
 */
class CryptManager extends Manager
{
    public function createXTeaDriver()
    {
        return new XTea($this->config['key'],$this->config['iv']);
    }
    public function createAesDriver()
    {
        return new Aes($this->config['key'],$this->config['iv']);
    }
    public function createGostDriver()
    {
        return new Gost($this->config['key'],$this->config['iv']);
    }
    public function createBlowFishDriver()
    {
        return new BlowFish($this->config['key'],$this->config['iv']);
    }
    public function createLoki97Driver()
    {
        return new Loki97($this->config['key'],$this->config['iv']);
    }
    public function createThirdDesDriver()
    {
        return new ThirdDes($this->config['key'],$this->config['iv']);
    }
    public function createTwoFishDriver()
    {
        return new TwoFish($this->config['key'],$this->config['iv']);
    }
    /**
     * @param string $name
     * @return \Tang\Crypt\Drivers\ICryptDriver
     */
    public function driver($name = '')
    {
        return parent::driver($name);
    }
    protected function getIntreface()
    {
        return '\Tang\Crypt\Drivers\ICryptDriver';
    }
}