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
namespace Tang\Web\Session\Stores;
use Tang\IO\Interfaces\IDirectory;
use Tang\Services\DirectoryService;

/**
 * 使用文件缓存session
 * Class FileCache
 * @package Tang\Web\Session\Stores
 */
class FileCache extends Cache
{
	public function gc($lifetime)
	{
		$this->deleteFile($this->cache->getDirectory(),$lifetime,time());
		return true;
	}
	protected function deleteFile($directory,$lifetime,$nowTime)
	{
		$that = $this;
		DirectoryService::getService()->scan($directory,function(IDirectory $directoryInstances,$file) use ($that,$lifetime,$nowTime)
		{
			$path = $file->getPathname();
			if($file->isDir())
			{
				$that->deleteFile($path,$lifetime,$nowTime);
			} else if($file->isFile() && ($file->getMTime() + $lifetime) < $nowTime)
			{
				unlink($path);
			}
		});
	}
}