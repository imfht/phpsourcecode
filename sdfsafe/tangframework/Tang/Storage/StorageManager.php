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
namespace Tang\Storage;
use Tang\Manager\Manager;
use Tang\Services\FileService;
use Tang\Storage\Drivers\Ftp;
use Tang\Storage\Drivers\Local;
use Tang\Storage\Drivers\Qiniu;
use Tang\Storage\Drivers\SFtp;
use Tang\Storage\Drivers\UpYun;

/**
 * 存储管理器
 * Class StorageManager
 * @package Tang\Storage
 */
class StorageManager extends Manager implements IStorageManager
{
    public function createFtpDriver()
    {
        return new Ftp();
    }
    public function createLocalDriver()
    {
        return new Local(FileService::getService());
    }
    public function createQiniuDriver()
    {
        return new Qiniu();
    }
    public function createSFtpDriver()
    {
        return new SFtp();
    }
    public function createUpYunDriver()
    {
        return new UpYun();
    }

	/**
	 * 获取URL地址
	 * @param $info
	 * @return string
	 */
	public function getUrl($info)
	{
		if(!is_array($info))
		{
			$info = json_decode($info,true);
		}
		if(!isset($info['file']) || !$info['file'] || !isset($info['bucket']) || !$info['bucket'])
		{
			return '';
		}
		$driver = $this->driver();
		$driver->setBucket($info['bucket']);
		return $driver->getUrl($info['file']);
	}
    /**
     * 获取存储驱动
     * @param string $name
     * @return \Tang\Storage\Drivers\IStorage
     */
    public function driver($name = '')
    {
        return parent::driver($name);
    }
    protected function createDriver($name)
    {
        $driver = parent::createDriver($name);
        $driver->setConfig(isset($this->config[$name]) ? $this->config[$name]:array());
        return $driver;
    }
    protected function getIntreface()
    {
        return '\Tang\Storage\Drivers\IStorage';
    }
}