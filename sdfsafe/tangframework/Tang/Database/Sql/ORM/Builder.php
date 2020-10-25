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
namespace Tang\Database\Sql\ORM;
use Tang\Database\Sql\ORM\Relations\Factory;
use Tang\Database\Sql\Query\Builder as QueryBuilder;
use Tang\Database\Sql\Model;

/**
 * ORM构建器
 * @package Tang\Database\Sql\ORM
 */
class Builder
{
	/**
	 * 查询构建对象
	 * @var QueryBuilder
	 */
	protected $query;
	/**
	 * 模型
	 * @var Model
	 */
	protected $model;
	/**
	 * @var array
	 */
	protected $passthru = array(
			'toSql', 'lists', 'insert', 'insertGetId', 'pluck', 'count',
			'min', 'max', 'avg', 'sum', 'exists', 'getBindings','update'
	);
	/**
	 * 延迟加载的关联名称
	 * @var array
	 */
	protected $eagerLoadRelations = array();
	/**
	 * 初始化对象，传入查询构建对象
	 * @param QueryBuilder $query
	 */
	public function __construct(QueryBuilder $query)
	{
		$this->query = $query;
	}

	/**
	 * 设置模型
	 * @param Model $model
	 */
	public function setModel(Model $model)
	{
		$this->model = $model;
		$this->query->setTable($model->getTableName());
	}

	/**
	 * 获取模块
	 * @return Model
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * 根据主键查询模块
	 * @param mixed $id
	 * @return \Tang\Util\Collection
	 */
	public function find($id)
	{
		if (is_array($id))
		{
			return $this->findMany($id);
		}
		$this->query->where($this->model->getPrimaryKey(), '=', $id);
		return $this->first();
	}

	/**
	 * 根据一些主键值查询
	 * @param array $id
	 * @return \Tang\Util\Collection
	 */
	public function findMany($id)
	{
		$this->query->whereIn($this->model->getPrimaryKey(), $id);
		return $this->get();
	}

	/**
	 * 获取查询结果的第一个结果
	 * @return Model
	 */
	public function first()
	{
		return $this->take(1)->get()->first();
	}

	/**
	 * 获得查询结果
	 * @return \Tang\Util\Collection
	 */
	public function get()
	{
        $results = $this->query->get();
		return $this->getModels($results);
	}

    /**
     * 根据查询结果获取模型集合
     * @param array $results
     * @return \Tang\Util\Collection
     */
    public function getModels(array $results)
	{
        if(!$results)
        {
            return $this->model->newCollection();
        }
		$models = array();
		foreach ($results as $result)
		{
			$models[] = $this->model->cloneModel($result);
		}

        if(count($models) > 0 && $this->eagerLoadRelations)
        {
            $models = $this->eagerLoadRelations($models);
        }
        return $this->model->newCollection($models);
	}

    /**
     * 获取分页信息
     * 返回数组
     * result 结果信息  pages 分页结果 maxPage 最大页数  count总数 page 当前页数
     * @param $page
     * @param int $listRows
     * @return array
     */
    public function getPagination($page,$listRows=20)
    {
        $result = $this->query->getPagination($page,$listRows);
        $result['result'] = $this->getModels($result['result']);
        return $result;
    }

	/**
	 * 设置预先加载约束
	 * @param array $relations
	 * @return $this
	 */
	public function setEagerLoadRelations(array $relations)
	{
		$this->eagerLoadRelations = array_merge($this->eagerLoadRelations,$relations);
		return $this;
	}

	/**
	 * 加载关联
	 * @param array $models
	 * @return array
	 */
	public function eagerLoadRelations(array $models)
	{
		if(!$this->eagerLoadRelations || !($relationRules = $this->model->getRelationRules()))
		{
			return $models;
		}
		foreach($this->eagerLoadRelations as $relation)
		{
			if(isset($relationRules[$relation]))
			{
				$models = $this->loadRelation($models,$relation,$relationRules[$relation]);
			}
		}
		return $models;
	}

    /**
     * 加载$name关联
     * @param array $models
     * @param $name
     * @param $config
     * @return mixed
     */
    public function loadRelation(array $models,$name,$config)
	{
		$relation = Factory::create($this->model,$config);
		if(isset($config['with']) && $config['with'])
		{
			$relation->with($config['with']);
		}
		if(isset($config['constraint']) && is_callable($config['constraint']))
		{
			call_user_func($config['constraint'],$relation);
		}
		$relation->addConstraints($models);
		$results = $relation->get();
		return $relation->match($models,$results,$name);
	}

    /**
     * @param $method
     * @param $parameters
     * @return mixed|Builder
     */
    public function __call($method,$parameters)
	{
		if(isset($this->macros[$method]))
		{
			array_unshift($parameters, $this);
			return call_user_func_array($this->macros[$method], $parameters);
		}
		elseif (method_exists($this->model, $scope = 'scope'.ucfirst($method)))
		{
			return $this->callScope($scope, $parameters);
		}
		else
		{
			$result = call_user_func_array(array($this->query,$method), $parameters);
		}
		return in_array($method,$this->passthru) ? $result : $this;
	}
	
}