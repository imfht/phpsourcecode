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
use Tang\Database\Sql\Query\Builder;

/**
 * 数据库缓存类
 * Class Database
 * @package Tang\Cache\Stores
 */
class Database extends Store
{
    /**
     * Query查询对象
     * @var \Tang\Database\Sql\Query\Builder
     */
    protected $query;

    /**
     * 构造函数 传入查询对象
     * @param Builder $query
     */
    public function __construct(Builder $query)
	{
		$this->query = $query;
	}

    /**
     * 获取查询对象
     * @return Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * (non-PHPdoc)
     * @see IStore::set()
     */
	public function set($key,$value,$expire = 0)
	{
		$this->delete($key);
		$value = is_numeric($value)?$value:json_encode($value);
		$this->query->insert(array('key'=>$this->getKey($key),'value'=>$value,'expire'=>$expire>0?$expire+time():0));
		$this->query->clean();
	}

    /**
     * (non-PHPdoc)
     * @see IStore::clean()
     */
	public function clean()
	{
		$this->query->delete();
		$this->query->clean();
	}

    /**
     * (non-PHPdoc)
     * @see IStore::delete()
     */
	public function delete($key)
	{
		$this->whereKey($key)->delete();
		$this->query->clean();
	}
	protected function getHandler($key)
	{
		$data = $this->whereKey($key)->take(1)->first();
		if($data && isset($data['expire']) && ($data['expire'] == 0 || time() < $data['expire']))
		{
			return is_numeric($data['value'])?$data['value']:json_decode($data['value'],true);
		}
		return;
	}

    /**
     * 构建where
     * @param $key
     * @return Builder
     */
    protected function whereKey($key)
	{
		$this->query->where('key','=',$this->getKey($key));
		return $this->query;
	}

    /**
     * 构建缓存key
     * @param $key
     * @return string
     */
    protected function getKey($key)
	{
		return sha1($key);
	}
}