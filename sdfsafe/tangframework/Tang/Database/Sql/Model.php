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
namespace Tang\Database\Sql;
use Tang\Cache\CacheService;
use Tang\Cache\Stores\IStore;
use Tang\Database\Sql\Connections\Connection;
use Tang\Database\Sql\ORM\Relations\Factory;
use Tang\Database\Sql\Schema\Columns;
use Tang\Services\EventService;
use Tang\Database\Sql\ORM\Builder;
use Tang\Exception\SystemException;
use Tang\Services\I18nService;
use Tang\Util\Collection;
use JsonSerializable;
use ArrayAccess;

/**
 * 模型
 * @package Tang\Database\Sql
 */
class Model implements JsonSerializable,ArrayAccess
{
	/**
	 * 数据库表名
	 * @var string
	 */
	protected $tableName;
	/**
	 * 数据库
	 * @var Connection
	 */
	protected $connection;
	/**
	 * 数据源
	 * @var string
	 */
	protected $dbSource = '';
	/**
	 * 数据属性
	 * @var array
	 */
	protected $attributes = array();
	/**
	 * 是否包含数据
	 * @var bool
	 */
	protected $exists;
	/**
	 * 字段信息
	 * @var Columns
	 */
	protected $columns;
	/**
	 * 事件对象
	 * @var ModelEvent
	 */
	protected $event;
	/**
	 * 允许操作的字段
	 * @var array
	 */
	protected $allowColumns = array();
	/**
	 * 验证信息
	 * @var array
	 */
	protected $validates = array();
	/**
	 * 字段映射
	 * @var array
	 */
	protected $maps = array();
	/**
	 * 关联模型配置
	 * @var array
	 */
	protected $relationRules = array();
    /**
     * 关联值
     * @var array
     */
    protected $relationValues = array();
    /**
     * dml关联(insert update delete)
     * @var array
     */
    protected $dmlRelations = array();
	/**
	 * 已经加载了的模型
	 * @var array
	 */
	private static $models = array();

    /**
     * 如果有$connection 则数据库使用$connection
     * 否则使用$dbSource连接数据库
     * @param Connection $connection 连接
     * @param null $dbSource 数据源
     */
    public function __construct(Connection $connection=null,$dbSource=null)
	{
		if(!$this->tableName)
		{
			$this->tableName = lcfirst(end(explode('\\',get_class($this))));
		}
		if($dbSource)
		{
			$this->dbSource = $dbSource;
		}
		$this->connection = $connection ? $connection : DB::get($this->dbSource);
		$this->event = $this->createEvent();
		$this->initColumns();
		$this->init();
	}

    /**
     * 设置关联模型值
     * @param $name
     * @param $value
     */
    public function setRelation($name,$value)
	{
		$this->relationValues[$name] = $value;
	}

	/**
	 * 获取表名
	 * @return string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}

    /**
     * 获取Columns
     * @return Columns
     */
    public function getColumns()
    {
        return $this->columns;
    }

	/**
	 * 设置数据源
	 * @param string $dbSource
	 */
	public function setDbSource($dbSource = '')
	{
		$this->dbSource = $dbSource;
		$this->connection = DB::get($this->dbSource);
	}

    /**
     * 获取数据源
     * @return null|string
     */
    public function getDbSource()
	{
		return $this->dbSource;
	}

    /**
     * 设置数据库连接
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

	/**
	 * 获取数据库连接
	 * @return Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

    /**
     * 插入数据
     * @param $attributes
     * @param string $marking 操作的标示值 用于过滤数据
     * @return $this
     */
    public function insert($attributes,$marking='')
	{
		if($this->exists)
		{
			$instance = $this->cloneModel();
			return $instance->insert($attributes);
		}
		$validateAttributes = $this->validates($attributes,$marking);
		$this->event->attachByParameters('beforInsert',array($this,$marking,&$validateAttributes));
		$this->newQuery()->insert($validateAttributes);
		if($this->columns->getIncrementing())
		{
			$id = $this->connection->getWritePdo()->lastInsertId($this->columns->getSequence());
			$key = $this->columns->getPrimaryKey();
			$attributes[$key] = $validateAttributes[$key] = $id;
		}
		$this->attributes = $validateAttributes;
        $this->exists = true;
		$this->event->attach('endInsert',$this,$marking);
		//处理DML约束
		$this->dmlRelationHandler($attributes,'insert');
		return $this;
	}

	/**
	 * 删除数据
	 * @throws \Tang\Exception\SystemException
	 */
	public function delete()
	{
		if(!$this->getPrimaryKey())
		{
			throw new SystemException('No primary key defined on model');
		}
		if($this->exists)
		{
			$this->event->attach('beforDelete', $this);
			$this->setQueryKey($this->newQuery())->delete();
			$this->event->attach('endDelete',$this);
			$this->dmlRelationHandler(array(),'delete');
			$this->exists = false;
		}
	}

	/**
	 * 更新数据
	 * @param array $attributes
     * @param string $marking 操作的标示值 用于过滤数据
	 * @return bool
	 */
	public function update(array $attributes = array(),$marking = '')
	{
		if(!$this->exists)
		{
			return $this->newQuery()->update($attributes);
		} else if(!$attributes)
        {
            $attributes = $this->attributes;
        }
        $validateAttributes = $this->validates($attributes,$marking);
		$this->event->attachByParameters('beforUpdate',array($this,$marking,&$validateAttributes));
        $updateStatus = $this->setQueryKey($this->newQuery())->update($validateAttributes);
        $this->event->attach('endUpdate',$this,$marking);
        $this->attributes = $this->fillAttributes($validateAttributes);
		$this->dmlRelationHandler($attributes,'update');
        return $updateStatus;
	}

	/**
	 * 填充属性
	 * @param array $attributes
	 * @return array
	 */
	protected function fillAttributes(array $attributes = array())
    {
		$this->exists = true;
        return array_replace_recursive($this->attributes,$attributes);
    }

	/**
	 * 获取属性
	 * @param $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{
		if(isset($this->attributes[$key]))
		{
			return $this->attributes[$key];
		}else if(isset($this->maps[$key]) && $this->maps[$key])
		{
			return $this->getAttribute($this->maps[$key]);
		}else if(isset($this->relationRules[$key]))
		{
			if(!array_key_exists($key,$this->relationValues))
			{
				$this->newQuery()->loadRelation(array($this),$key,$this->relationRules[$key]);
			}
			return $this->relationValues[$key];
		} else
		{
			return null;
		}
	}

	/**
	 * 获取主键
	 * @return mixed
	 */
	public function getPrimaryKey()
	{
		return $this->columns->getPrimaryKey();
	}

    /**
     * 预先加载约束
     * @param bool $relation
     * @return $this
     */
    public static function with($relation = false)
	{
        $model = new static;
        $instance = $model->newQuery();
        if(is_bool($relation))
        {
            if($relation)
            {
                $relations = array_keys($model->relationRules);
            } else
            {
                return $instance;
            }
        } else
        {
            $relations = func_get_args();
        }
		return $instance->setEagerLoadRelations($relations);
	}

    /**
     * dml约束 适用于insert update delete
     * @return $this
     */
    public function withDml()
	{
		$relations = func_get_args();
		$this->dmlRelations = array_merge($this->dmlRelations,$relations);
		return $this;
	}

	/**
	 * 克隆模型
	 * @param array $attributes
	 * @return Model
	 */
	public function cloneModel(array $attributes = array())
	{
		$instance = clone $this;
		$instance->attributes = $attributes;
		$instance->exists = $attributes ? true : false;
		return $instance;
	}

	/**
	 * 创建新的集合
	 * @param array $models
	 * @return Collection
	 */
	public function newCollection(array $models = array())
    {
        return new Collection($models);
    }

	/**
	 * 构建ORM Builder
	 * @return Builder
	 */
	public function newQuery()
	{
		$query = new Builder($this->connection->table($this->tableName));
		$query->setModel($this);
		return $query;
	}

    /**
     * json序列
     * @return array|mixed
     */
    public function jsonSerialize()
	{
		return array_merge($this->attributes,$this->relationValues);
	}

    /**
     * 转换成数组
     * @return array|mixed
     */
    public function toArray()
	{
		return $this->jsonSerialize();
	}

    /**
     * @see ArrayAccess::offsetExists
     */
    public function offsetExists($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * @see ArrayAccess::offsetGet
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * @see ArrayAccess::$offset
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset,$value);
    }

    /**
     * @see ArrayAccess::offsetUnset
     */
    public function offsetUnset($offset)
    {
    }
    /**
     * 获取值
     * @param $key
     * @return mixed
     */
    public function __get($key)
	{
		return $this->getAttribute($key);
	}

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function __set($key,$value)
    {
        if(isset($this->maps[$key]))
        {
            $key = $this->maps[$key];
        }
        return $this->attributes[$key] = $value;
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method,$parameters)
    {
        $instance = new static;
        return call_user_func_array(array($instance,$method),$parameters);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed|ORM\Relations\Relation
     */
    public function __call($method, $parameters)
	{
		if(isset($this->relationRules[$method]) && $this->relationRules[$method])
		{
			return Factory::create($this,$this->relationRules[$method]);
		}
		$query = $this->newQuery();
		return call_user_func_array(array($query,$method), $parameters);
	}

	/**
	 * 创建
	 * @param array $attributes
	 * @return Model
	 */
	public static function create(array $attributes,$marking='')
	{
		$model = new static();
		$model->insert($attributes,$marking);
		return $model;
	}
    /**
     * 根据主键查询
     * @param mixed|array $id 查询的主键ID
     * @return Collection|Model
     */
    public static function find($id)
	{
		if (is_array($id) && empty($id)) return new Collection;
		$instance = new static();
		return $instance->newQuery()->find($id);
	}

	/**
	 * 加载模型
	 * @param $name
	 * @return Model
	 */
	public static function loadModel($name)
	{
		if(!isset(static::$models[$name]))
		{
			$path = explode('.',$name,2);
			$className = 'Lib\\Applications\\'.ucfirst($path[0]).'\\Models\\'.ucfirst($path[1]);
			static::$models[$name] = new $className;
		}
		return static::$models[$name];
	}

    /**
     * dml约束处理程序
     * @param $attributes
     * @param $method
     */
    protected function dmlRelationHandler($attributes,$method)
	{
		if(!$this->dmlRelations)
		{
			return;
		}
		$isInsertAndUpdate = false;
		if($method == 'insert' || $method == 'update')
		{
			$isInsertAndUpdate = true;
		} else
		{
			$method = 'delete';
		}
		foreach($this->dmlRelations as $dmlRelation)
		{
			if(isset($this->relationRules[$dmlRelation]) && (!$isInsertAndUpdate || isset($attributes[$dmlRelation])))
			{
				call_user_func(array($this->{$dmlRelation}(),$method),isset($attributes[$dmlRelation]) ? $attributes[$dmlRelation]:null);
                if(isset($this->relationValues[$dmlRelation]) && $this->relationValues[$dmlRelation])
                {
                    unset($this->relationValues[$dmlRelation]);
                }
			}
		}
	}

	/**
	 * 验证数据
	 * @param $attributes
     * @param string $marking 过滤标示
	 * @return array
	 * @throws \Tang\Exception\SystemException
	 */
	protected function validates($attributes,$marking='')
	{
		$tmp = array();
		$columns = $marking && isset($this->allowColumns[$marking]) ? $this->allowColumns[$marking] : $this->columns;
		if($this->maps) foreach ($this->maps as $key=>$value)
		{
			if (isset($attributes[$key]))
			{
				$attributes[$value] = $attributes[$key];
			}
		}
		foreach ($columns as $column)
		{
			isset($attributes[$column]) && $tmp[$column] = $attributes[$column];
			if(isset($this->validates[$column]))
			{
				$this->validateHandler($column,$attributes[$column]);
			}
		}
		if (!$tmp)
		{
			throw new SystemException('not found data');
		}
		return $tmp;
	}

	/**
	 * 初始化字段信息
	 */
	protected function initColumns()
	{
        $connection = $this->connection;
        $columns = CacheService::get($this->dbSource.'_'.$this->tableName,function(IStore $store,$key) use($connection)
        {
            $columns = serialize($connection->getSchemaBuilder()->getColumns($this->tableName));
            $store->set($key,$columns,86400);
            return $columns;
        });
        $this->columns = unserialize($columns);
	}

	/**
	 * 设置查询主键
	 * @param Builder $query
	 * @return Builder
	 */
	protected function setQueryKey(Builder $query)
    {
        $primaryKeys = $this->columns->getPrimaryKeys();
		if($primaryKeys) foreach($primaryKeys as $primaryKey)
		{
			$query->where($primaryKey,'=',$this->getAttribute($primaryKey));
		}
        return $query;
    }

    /**
     * 验证字段
     * @param $column
     * @param $value
     * @return bool
     * @throws ValidateFaildException
     */
    protected function validateHandler($column,&$value)
    {
        $validate = $this->validates[$column];
        $result = false;
        if(!$value && isset($validate['isEmpty']) && $validate['isEmpty'])
        {
            $value = '';
            return true;
        }
        if($validate['type'] == 'regex')
        {
            $result = preg_match($validate['regex'], $value);
        } else
        {
            $parameters = array();
            if(isset($validate['parameters']))
            {
                $parameters = is_array($validate['parameters']) ? $validate['parameters'] : array($validate['parameters']);
            }
            array_unshift($parameters,$value);
            $value = &$parameters[0];
            $callback = $validate['type'] == 'callback' ? $validate['callback'] : array('\Tang\Util\Validate',$validate['method']);
            $result = call_user_func_array($callback,$parameters);
        }
        if (!$result)
        {
            $message = '';
            $args = array();
            if(is_array($validate['message']))
            {
                $args = isset($validate['message']['args']) ? $validate['message']['args'] : array();
                $message = $validate['message']['key'];
            } else
            {
                $message = $validate['message'];
            }
			$code = isset($validate['message']) && ($validate['message'] = (int)$validate['message']) > 0 ? $validate['message']:10451;
            throw new ValidateFaildException($message,$args,$code);
        }
        return $result;
    }

    /**
     * 给子类初始化
     * @return void
     */
    protected function init()
	{
	}

    /**
     * 获取关联规则
     * @return array
     */
    public function getRelationRules()
	{
		return $this->relationRules;
	}

    /**
     * 创建模型事件
     * @return ModelEvent
     */
    protected function createEvent()
    {
        return new ModelEvent(EventService::newService());
    }

    /**
     * 克隆
     */
    protected function __clone()
	{
		$this->attributes = $this->relationValues = array();
    }
}

class ValidateFaildException extends SystemException
{

}