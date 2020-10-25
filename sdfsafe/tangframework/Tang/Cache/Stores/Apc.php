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
 * APC缓存
 * Class Apc
 * @package Tang\Cache\Stores
 */
class Apc extends Store
{
    /**
     * (non-PHPdoc)
     * @see IStore::set()
     */
    public function set($key,$value,$expire=0)
	{
		apc_store($key,$value,$expire);
	}

    /**
     * (non-PHPdoc)
     * @see IStore::clean()
     */
    public function clean()
	{
		apc_clear_cache('user');
	}

    /**
     * (non-PHPdoc)
     * @see IStore::delete()
     */
	public function delete($key)
	{
		apc_delete($key);
	}

    /**
     * (non-PHPdoc)
     * @see Store::getHandler()
     */
    protected function getHandler($key)
	{
		return apc_fetch($key);
	}
}