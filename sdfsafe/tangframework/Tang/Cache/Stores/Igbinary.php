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
namespace Tang\Cache\Stores;
/**
 * Igbinary缓存驱动类
 * 需要安装igbinary扩展
 * Class Igbinary
 * @package Tang\Cache\Stores
 */
class Igbinary extends FileStore
{
    /**
     * (non-PHPdoc)
     * @see IStore::set()
     */
	public function set($key,$value,$expire = 0)
	{
		$expire = $this->getExpire($expire);
		$this->write($key,igbinary_serialize(compact('value','expire')));
	}

    /**
     * (non-PHPdoc)
     * @see FileStore::getType()
     */
    public function getType()
	{
		return 'Igbinary';
	}

    /**
     * (non-PHPdoc)
     * @see FileStore::serializeHandler()
     */
	protected function serializeHandler($content)
	{
		$data = igbinary_unserialize($content);
		return $data;
	}

}