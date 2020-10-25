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
 * 缓存存储基类
 * Class Store
 * @package Tang\Cache\Stores
 */
abstract class Store implements IStore
{
    /**
     * (non-PHPdoc)
     * @see IStore::get()
     */
    public function get($key,callable $callback = null)
	{
		$data = $this->getHandler($key);
		if(!$data && $callback && is_callable($callback))
		{
			$data = $callback($this,$key);
		}
		return $data;
	}

    /**
     * (non-PHPdoc)
     * @see IStore::set()
     */
    public abstract function set($key,$value,$expire=0);
    /**
     * (non-PHPdoc)
     * @see IStore::delete()
     */
	public abstract function delete($key);
    /**
     * (non-PHPdoc)
     * @see IStore::clean()
     */
	public abstract function clean();

    /**
     * 读取缓存内容处理
     * @param $key
     * @return mixed
     */
    protected abstract function getHandler($key);
}