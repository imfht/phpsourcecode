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
use Tang\Database\Sql\ORM\Builder;
use Tang\Database\Sql\Model;
use Tang\Util\Collection;

/**
 * 所有关联基类
 * Class Relation
 * @package Tang\Database\Sql\ORM\Relations
 */
abstract class Relation
{
	/**
	 * @var Builder
	 */
	protected $query;
	/**
	 * @var Model
	 */
	protected $parentModel;
	/**
	 * @var Model
	 */
	protected $relatedModel;
	/**
	 * config
	 * @var array
	 */
	protected $config;
    /**
     * 外键
     * @var string
     */
    protected $foreignKey;
    /**
     * 主键
     * @var mixed
     */
    protected $localKey;

    /**
     * @param Model $parentModel 模型
     * @param Model $relatedModel 关联模型
     * @param array $config 配置
     */
    public function __construct(Model $parentModel,Model $relatedModel,array $config)
	{
		$this->parentModel = $parentModel;
		$this->relatedModel = $relatedModel;
		$this->query = $relatedModel->newQuery();
		$this->config = $config;
		if(!isset($config['foreignKey']))
		{
			$config['foreignKey'] = $this->parentModel->getTableName().'Id';
		}
		if(!isset($config['localKey']))
		{
			$config['localKey'] = $this->parentModel->getPrimaryKey();
		}
		$this->foreignKey = $config['foreignKey'];
		$this->localKey = $config['localKey'];
		$this->configHandler();
	}

	/**
	 * 设置上级模型
	 * @param Model $parentModel
	 */
	public function setParentModel(Model $parentModel)
	{
		$this->parentModel = $parentModel;
	}

	/**
	 * 获取上级模型
	 * @return Model
	 */
	public function getParentModel()
	{
		return $this->parentModel;
	}

    /**
     * 获取关联模型查询构建对象
     * @return Builder
     */
    public function getQuery()
	{
		return $this->query;
	}

    /**
     * 获取关联模型
     * @return Model
     */
    public function getRelatedModel()
	{
		return $this->relatedModel;
	}

    /**
     * 根据模型设置关联模型外键条件
     * @param array $models
     */
    public function addConstraints(array $models)
	{
		$ids = $this->getKeys($models,$this->localKey);
		if(count($ids) > 1)
		{
			$this->query->whereIn($this->foreignKey,$ids);
		} else
		{
			$this->query->where($this->foreignKey,'=',$ids[0]);
		}
	}

    /**
     * 根据模型来获取模型主键值
     * @param array $models
     * @param $key
     * @return array
     */
    public function getKeys(array $models,$key)
	{
		return array_unique(array_values(array_map(function($value) use ($key)
		{
			return $value->getAttribute($key);
		},$models)));
	}

	/**
	 * 子关联
	 */
	public function with($relation = false)
	{
		$parameter = is_array($relation) ? $relation : array($relation);
		$this->query = call_user_func_array(array($this->relatedModel,'with'),$parameter);
	}

    /**
     * 关联插入
     * @param array $attributes
     * @return $this
     */
    public function insert(array $attributes)
	{
		$attributes[$this->foreignKey] = $this->parentModel->getAttribute($this->localKey);
		return $this->relatedModel->insert($attributes);
	}

    /**
     * 关联更新
     * @param array $attributes
     * @return Model
     */
    public function update(array $attributes)
	{
		$attributes[$this->foreignKey] = $this->parentModel->getAttribute($this->localKey);
		$instance = $this->relatedModel->cloneModel($attributes);
		$instance->update();
		return $instance;
	}

    /**
     * 关联删除
     */
    public function delete()
	{
		$this->relatedModel->newQuery()->where($this->foreignKey,'=',$this->parentModel->getAttribute($this->localKey))->delete();
	}

    /**
     * 当没有调用的函数时，转入查询构建对象
     * @param $method
     * @param $parameters
     * @return mixed|Relation
     */
    public function __call($method,$parameters)
	{
		$result = call_user_func_array(array($this->query, $method), $parameters);
		return $result === $this->query ? $this : $result;
	}

    /**
     * 根据模型数组匹配结果
     * @param array $models
     * @param Collection $results
     * @param $relation
     * @param $type
     * @return array
     */
    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
	{
		$dictionary = $this->buildDictionary($results);
		foreach ($models as $model)
		{
			$key = $model->getAttribute($this->localKey);
			if (isset($dictionary[$key]))
			{
				$value = $dictionary[$key];
				$model->setRelation($relation,$type == 'one' ? reset($value) : $this->relatedModel->newCollection($value));
			} else
			{
				$model->setRelation($relation,$this->getDefaultValue());
			}
		}
		return $models;
	}

    /**
     * 根据查询结果匹配关联结果。
     * @param Collection $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
	{
		$dictionary = array();
		$foreign = $this->foreignKey;
		if(isset($this->config['index']) && $this->config['index'])
		{
			$index = $this->config['index'];
			$func = function($result) use($index)
			{
				return $result->getAttribute($index);
			};
		} else if(isset($this->config['indexCallback']) && is_callable($this->config['indexCallback']))
		{
			$func = $this->config['indexCallback'];
		}
		foreach ($results as $result)
		{
			if(isset($func) && ($key = $func($result)))
			{
				$dictionary[$result->{$foreign}][$key] = $result;
			} else
			{
				$dictionary[$result->{$foreign}][] = $result;
			}
		}

		return $dictionary;
	}

    /**
     * 配置处理
     * @return mixed
     */
    protected abstract function configHandler();

    /**
     * 默认结果
     * @return null
     */
    protected function getDefaultValue()
	{
		return null;
	}
}