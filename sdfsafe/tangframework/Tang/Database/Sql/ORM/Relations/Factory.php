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
namespace Tang\Database\Sql\ORM\Relations;
use Tang\Database\Sql\Model;
use Tang\Exception\SystemException;

/**
 * 创建关联对象工厂类
 * Class Factory
 * @package Tang\Database\Sql\ORM\Relations
 */
class Factory
{
    /**
     * 一对一关系
     */
    const HAS_ONE = 1;
    /**
     * 一对多关系
     */
    const HAS_MANY = 2;
    /**
     * 属于关系
     */
    const BELONGS_TO = 3;
    /**
     * 多对多关系
     */
    const MANY_TO_MANY = 4;
	/**
	 * 关联的对象模型数组
	 * @var array
	 */
	private static $relations = array();

	/**
	 * 创建关联对象
     * <code>
     * Factory::create($model,$config);
     * </code>
	 * @param Model $model
	 * @param $config
	 * @return Relation
	 */
	public static function create(Model $model,array $config)
	{
		$object = null;
		$objectName = get_class($model).'--'.$config['modelName'].'_'.$config['type'];
		//判断是否包含该类型的关联对象，以免再次初始化
		if(isset(static::$relations[$objectName]))
		{
			$object = static::$relations[$objectName];
			$object->setParentModel($model);
			$object->getQuery()->clean();
		}else
		{
			$className = static::getRelationClassName($config['type']);
			$relationModel = Model::loadModel($config['modelName']);
			$object = static::$relations[$objectName] = new $className($model,$relationModel,$config);
		}
		return $object;
	}

    /**
     * 根据对应关系获取类名
     * @param $type
     * @return string
     * @throws \Tang\Exception\SystemException
     */
    protected static function getRelationClassName($type)
	{
		static $classNames = array(
			Factory::HAS_ONE => 'HasOne',
			Factory::HAS_MANY => 'HasMany',
			Factory::BELONGS_TO => 'BelongsTo',
			Factory::MANY_TO_MANY => 'ManyToMany'
		);
		if(!isset($classNames[$type]))
		{
			throw new SystemException('Not found relation class name');
		}
		return '\Tang\Database\Sql\ORM\Relations\\'.$classNames[$type];
	}
}