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
use Tang\Util\Collection;

/**
 * 多对多
 * Class ManyToMany
 * @package Tang\Database\Sql\ORM\Relations
 */
class ManyToMany extends Manys
{
    /**
     * 外键别名
     */
    const AS_NAME = 'tangFrameworkManyToManyForgerKey';
	/**
	 * 多对多的中间关联表名称
	 * @var string
	 */
	protected $relationTable = '';
    /**
     * 中间表外键
     * @var string
     */
    protected $relationForeignKey = '';
    /**
     * 中间表主键
     * @var string
     */
    protected $relationLocalKey = '';
	protected function configHandler()
	{
		parent::configHandler();
		if(!isset($this->config['relationTable']) || !$this->config['relationTable'])
		{
			$this->config['relationTable'] = $this->parentModel->getTableName().'_'.$this->relatedModel->getTableName();
		}
		if(!isset($this->config['relationForeignKey']) || !$this->config['relationForeignKey'])
		{
			$this->config['relationForeignKey'] = $this->relatedModel->getTableName().'Id';
		}
		if(!isset($this->config['relationLocalKey']) || !$this->config['relationLocalKey'])
		{
			$this->config['relationLocalKey'] = $this->parentModel->getTableName().'Id';
		}
		$this->relationTable = $this->config['relationTable'];
		$this->relationForeignKey = $this->config['relationForeignKey'];
		$this->relationLocalKey = $this->config['relationLocalKey'];
	}
	public function insert(array $attributes)
	{
        /**
		if(!is_array(reset($attributes)))
		{
			$attributes = array($attributes);
		}
		$relationAttributes = array();
		$key = $this->parentModel->getAttribute($this->localKey);
		foreach($attributes as $attribute)
		{
			$instance = $this->relatedModel->insert($attribute);
			$relationAttributes[] = array($this->relationForeignKey=>$instance->getAttribute($this->foreignKey),
										 $this->relationLocalKey => $key);
		}
		$this->relatedModel->getConnection()->table($this->relationTable)->insert($relationAttributes);**/
        $this->update($attributes);
	}

    public function update(array $attributes)
    {
        if(!is_array(reset($attributes)))
        {
            $attributes = array($attributes);
        }
        $keys = array();
        $insertAttributes = array();
        $updateAttributes = array();
        foreach($attributes as $key => $attribute)
        {
            //没有主键的 需要插入
            if(!isset($attribute[$this->relationForeignKey]) || !$attribute[$this->relationForeignKey])
            {
                $insertAttributes[] = $attribute;
            } else
            {
                $keys[] = $attribute[$this->relationForeignKey];
                $updateAttributes[] = $attribute;
            }
        }
        $relatedQuery = $this->relatedModel->getConnection()->table($this->relationTable);
        $id = $this->parentModel->getAttribute($this->localKey);
        if($insertAttributes) foreach($insertAttributes as $attribute)
        {
            $temp = $this->relatedModel->insert($attribute);
            $keys[] = $temp->getAttribute($this->foreignKey);
        }
        if($updateAttributes) foreach($updateAttributes as $attribute)
        {
			unset($attribute[$this->relationForeignKey]);
			if($attribute)
			{
				$this->relatedModel->where($this->foreignKey,'=',$attribute[$this->relationForeignKey])->update($attribute);
			}
        }
        if($keys)
        {
            $relatedQuery->where($this->relationLocalKey,'=',$id)->whereIn($this->relationForeignKey,$keys)->delete();
            $insertAttributes = array();
            foreach($keys as $key)
            {
                $insertAttributes[] = array($this->relationLocalKey=>$id,$this->relationForeignKey=>$key);
            }
            $relatedQuery->clean()->insert($insertAttributes);
        }
    }

	public function delete()
	{
		//只删除关联表数据
        $relatedQuery = $this->relatedModel->getConnection()->table($this->relationTable);
        $id = $this->parentModel->getAttribute($this->localKey);
        $relatedQuery->where($this->relationLocalKey,'=',$id)->delete();
	}

	public function addConstraints(array $models,$type = 'left')
	{
		$joinTable = $this->relatedModel->getTableName();
		$relationTable = $this->relationTable;
		$this->query->setTable($this->relationTable);
		$this->query->join($joinTable,$relationTable.'.'.$this->relationForeignKey,'=',$joinTable.'.'.$this->foreignKey,$type);
		$this->query->select(array($joinTable.'.*',$relationTable.'.'.$this->relationLocalKey.' as '.ManyToMany::AS_NAME));
		$ids = $this->getKeys($models,$this->localKey);
		if(count($ids) > 1)
		{
			$this->query->whereIn($relationTable.'.'.$this->relationLocalKey,$ids);
		} else
		{
			$this->query->where($relationTable.'.'.$this->relationLocalKey,'=',$ids[0]);
		}
		$this->limit();
	}
	protected function buildDictionary(Collection $results)
	{
		$dictionary = array();
		$foreign = $this->localKey;
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
				$dictionary[$result->getAttribute(ManyToMany::AS_NAME)][$key] = $result;
			} else{
				$dictionary[$result->getAttribute(ManyToMany::AS_NAME)][] = $result;
			}

		}
		return $dictionary;
	}
}