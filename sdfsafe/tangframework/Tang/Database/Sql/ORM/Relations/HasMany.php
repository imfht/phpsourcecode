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
/**
 * 一对多关系
 * Class HasMany
 * @package Tang\Database\Sql\ORM\Relations
 */
class HasMany extends Manys
{
	public function addConstraints(array $models)
	{
		parent::addConstraints($models);
		$this->limit();
	}

    /**
     * 插入
     * @param array $attributes
     * @return $this|void
     */
    public function insert(array $attributes)
	{
		foreach($attributes as $key => $tmp)
		{
			$attributes[$key][$this->foreignKey] = $this->parentModel->getAttribute($this->localKey);
		}
		return $this->relatedModel->newQuery()->insert($attributes);
	}

    /**
     * 更新
     * @param array $attributes
     * @return \Tang\Database\Sql\Model|void
     */
    public function update(array $attributes)
    {
        //判断多个主键值是否有数据
        $id = $this->parentModel->getAttribute($this->localKey);
        $insertArray = array();
        $columns = $this->relatedModel->getColumns();
        $primaryKeys = $columns->getPrimaryKeys();
        if (!is_array(reset($attributes)))
        {
            $attributes = array($attributes);
        }
        //循环判断有没主键
        foreach($attributes as $attribute)
        {
            $this->query->clean();
            if(!isset($attribute[$this->foreignKey]) || !$attribute[$this->foreignKey])
            {
                $attribute[$this->foreignKey] = $id;
            }
            if($primaryKeys)foreach($primaryKeys as $primaryKey)
            {
                if(!isset($attribute[$primaryKey]) && !$attribute[$primaryKey])
                {
                    throw new \Exception("miss primaryKey value");
                }
                $this->query->where($primaryKey,'=',$attribute[$primaryKey]);
            }
            if($this->query->first())
            {
                $this->query->update($attribute);
            } else
            {
                $insertArray[] = $attribute;
            }
        }
        if($insertArray)
        {
            $this->query->clean()->insert($insertArray);
        }
    }
}