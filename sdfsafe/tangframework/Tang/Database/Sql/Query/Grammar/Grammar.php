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
namespace Tang\Database\Sql\Query\Grammar;
use Tang\Database\Sql\Query\Builder;
use Tang\Database\Sql\Grammar as BaseGrammar;

/**
 * 查询解析器
 * Class Grammar
 * @package Tang\Database\Sql\Query\Grammar
 */
class Grammar extends BaseGrammar 
{
    /**
     * 查询语句组件
     * @var array
     */
    protected $selectComponents = array(
			'aggregate',
			'columns',
			'tableName',
			'joins',
			'wheres',
			'groups',
			'havings',
			'orders',
			'limit',
			'offset',
			'unions',
			'lock',
	);

    /**
     * 生成查询语句
     * @param Builder $query
     * @return string
     */
    public function compileSelect(Builder $query)
	{
		return trim($this->concatenate($this->compileComponents($query)));
	}

    /**
     * 生成Insert语句
     * @param Builder $query
     * @param array $values
     * @return string
     */
    public function compileInsert(Builder $query, array $values)
	{
		$table = $this->wrapTable($query->getProperty('tableName'));
		if ( ! is_array(reset($values)))
		{
			$values = array($values);
		}
		$columns = $this->columnize(array_keys(reset($values)));
		$parameters = $this->parameterize(reset($values));
		$value = array_fill(0, count($values), '('.$parameters.')');
		$parameters = implode(', ', $value);
		return 'insert into '.$table.' ('.$columns.') values '.$parameters;
	}

    /**
     * 生成get insert id语句
     * @param Builder $query
     * @param $values
     * @param $sequence
     * @return string
     */
    public function compileInsertGetId(Builder $query, $values, $sequence)
	{
		return $this->compileInsert($query, $values);
	}

    /**
     * 生成update语句
     * @param Builder $query
     * @param $values
     * @return string
     */
    public function compileUpdate(Builder $query, $values)
	{
		$table = $this->wrapTable($query->getProperty('tableName'));
		$columns = array();
		foreach ($values as $key => $value)
		{
			$columns[] = $this->wrap($key).' = ?';
		}
		$columns = implode(', ', $columns);
		$joins = $query->getProperty('joins');
		if ($joins)
		{
			$joins = ' '.$this->compileJoins($query, $joins);
		}
		else
		{
			$joins = '';
		}
		return 'update '.$table.' '.$joins.' set  '.$columns.' '.$this->compileWheres($query);
	}

    /**
     * 生成删除语句
     * @param Builder $query
     * @return string
     */
    public function compileDelete(Builder $query)
	{
		$table = $this->wrapTable($query->getProperty('tableName'));
		$joins = $query->getProperty('joins');
		if ($joins)
		{
			$joins = ' '.$this->compileJoins($query, $joins);
		}
		else
		{
			$joins = '';
		}
		return trim('delete from '.$table.' '.$joins.$this->compileWheres($query));
	}

    /**
     * 生成聚合函数语句
     * @param Builder $query
     * @param $aggregate
     * @return string
     */
    protected function compileAggregate(Builder $query, $aggregate)
	{
		$column = $this->columnize($aggregate['columns']);
		if ($query->getProperty('distinct') && $column !== '*')
		{
			$column = 'distinct '.$column;
		}
		return 'select '.$aggregate['name'].'('.$column.') as aggregate';
	}

	/**
     * 查询语句组语句生成
	 * @param Builder $query
	 * @return array
	 */
	protected function compileComponents(Builder $query)
	{
		$sql = array();
		foreach ($this->selectComponents as $component)
		{
			$componentContent = $query->getProperty($component);
			if ($componentContent)
			{
				$method = 'compile'.ucfirst($component);
				$sql[$component] = $this->$method($query, $componentContent);
			}
		}
		return $sql;
	}

    /**
     * 生成查询字段语句
     * @param Builder $query
     * @param $columns
     * @return string
     */
    protected function compileColumns(Builder $query, $columns)
	{
		if ($query->getProperty('aggregate')) return;
		$select = $query->getProperty('distinct') ? 'select distinct ' : 'select ';
		return $select.$this->columnize($columns);
	}

    /**
     * 生成from表
     * @param Builder $query
     * @param $table
     * @return string
     */
    protected function compileTableName(Builder $query, $table)
	{
		return 'from '.$this->wrapTable($table);
	}

    /**
     * 生成join
     * @param Builder $query
     * @param $joins
     * @return string
     */
    protected function compileJoins(Builder $query, $joins)
	{
		$sql = array();
		
		foreach ($joins as $join)
		{
			$table = $this->wrapTable($join->getTableName());
			$clauses = array();
			$joinClauses = $join->getClauses();
			foreach ($joinClauses as $clause)
			{
				$clauses[] = $this->compileJoinConstraint($clause);
			}
			$clauses[0] = $this->removeLeadingBoolean($clauses[0]);
			$clauses = implode(' ', $clauses);
			$type = $join->getType();
			$sql[] = $type.' join '.$table.' on '.$clauses;
		}
		return implode(' ', $sql);
	}

    /**
     * 生成join on
     * @param array $clause
     * @return string
     */
    protected function compileJoinConstraint(array $clause)
	{
		$fromColumn = $this->wrap($clause['fromColumn']);
		$joinColumnOrValue = $clause['isWhere'] ? '?' : $this->wrap($clause['joinColumnOrValue']);
		return $clause['connector'] .' '.$fromColumn. ' '.$clause['operator'].' '.$joinColumnOrValue;
	}

    /**
     * 生成where
     * @param Builder $query
     * @return string
     */
    protected function compileWheres(Builder $query)
	{
		$wheres = $query->getProperty('wheres');
		if (!$wheres) return '';
		$sql = '';
		foreach ($wheres as $where)
		{
			$method = "where{$where['type']}";
			$sql .= ' '. $where['connector'].' '.$this->$method($query, $where);
		}
		if ($sql)
		{
			return 'where '.$this->removeLeadingBoolean($sql);
		}
		return '';
	}

    /**
     * 生成where嵌套
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereNested(Builder $query, $where)
	{
		return '('.substr($this->compileWheres($where['query']), 6).')';
	}

    /**
     * 生成where子语句
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereSub(Builder $query, $where)
	{
		$select = $this->compileSelect($where['query']);
		return $this->wrap($where['column']).' '.$where['operator']." ($select)";
	}

    /**
     * 生成基础where
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereBasic(Builder $query, $where)
	{
		return $this->wrap($where['column']).' '.$where['operator'].' ?';
	}

    /**
     * 生成where between
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereBetween(Builder $query, $where)
	{
		return $this->wrap($where['column']).' '.$this->getNot($where['isNot']).' between ? and ?';
	}

    /**
     * 生成where exists
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereExists(Builder $query, $where)
	{
		return $this->getNot($where['isNot']).'exists ('.$this->compileSelect($where['query']).')';
	}

    /**
     * 生成where in
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereIn(Builder $query, $where)
	{
		$values = $this->parameterize($where['values']);
		return $this->wrap($where['column']).' '.$this->getNot($where['isNot']).'in ('.$values.')';
	}

    /**
     * 生成where in (子查询)
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereInSub(Builder $query, $where)
	{
		$select = $this->compileSelect($where['query']);
		return $this->wrap($where['column']).' '.$this->getNot($where['isNot']).' in ('.$select.')';
	}

    /**
     * 生成where null
     * @param Builder $query
     * @param $where
     * @return string
     */
    protected function whereNull(Builder $query, $where)
	{
		return $this->wrap($where['column']).' is '.$this->getNot($where['isNot']).'null';
	}

    /**
     * 生成 where原生sql
     * @param Builder $query
     * @param $where
     * @return mixed
     */
    protected function whereSql(Builder $query, $where)
	{
		return $where['sql'];
	}

    /**
     * 生成group by
     * @param Builder $query
     * @param $groups
     * @return string
     */
    protected function compileGroups(Builder $query, $groups)
	{
		return 'group by '.$this->columnize($groups);
	}

    /**
     * 生成多个having
     * @param Builder $query
     * @param $havings
     * @return string
     */
    protected function compileHavings(Builder $query,$havings)
	{
		$sql = implode(' ', array_map(array($this, 'compileHaving'), $havings));
		return 'having '.preg_replace('/and /', '', $sql, 1);
	}

    /**
     * 生成having
     * @param array $having
     * @return string
     */
    protected function compileHaving(array $having)
	{
		if ($having['type'] === 'sql')
		{
			return $having['connector'].' '.$having['sql'];
		}
		return $this->compileBasicHaving($having);
	}
	
	/**
	 * 生成基础having
	 * @param  array   $having
	 * @return string
	 */
	protected function compileBasicHaving($having)
	{
		$column = $this->wrap($having['column']);
		return $having['connector'].' '.$column.' '.$having['operator'].' ?';
	}

    /**
     * 生成order
     * @param Builder $query
     * @param $orders
     * @return string
     */
    protected function compileOrders(Builder $query, $orders)
	{
		return 'order by '.implode(', ', array_map(function($order)
		{
			return $this->wrap($order['column']).' '.$order['direction'];
		},$orders));
	}

    /**
     * 生成limit
     * @param Builder $query
     * @param $limit
     * @return string
     */
    protected function compileLimit(Builder $query, $limit)
	{
		return 'limit '.(int) $limit;
	}

    /**
     * 生成offset
     * @param Builder $query
     * @param $offset
     * @return string
     */
    protected function compileOffset(Builder $query, $offset)
	{
		return 'offset '.(int) $offset;
	}

    /**
     * 生成多个union
     * @param Builder $query
     * @return string
     */
    protected function compileUnions(Builder $query)
	{
		$sql = '';
        $unions = $query->getProperty('unions');
		foreach ($unions as $union)
		{
			$sql .= $this->compileUnion($union);
		}
		return $sql;
	}
    /**
     * 生成union
     * @param array $union
     * @return string
     */
	protected function compileUnion(array $union)
	{
		$joiner = $union['all'] ? ' union all ' : ' union ';
		return $joiner.$union['query']->toSql();
	}

    /**
     * not
     * @param $isNot
     * @return string
     */
    protected function getNot($isNot)
	{
		return $isNot ? ' not ':' ';
	}

    /**
     * 过滤
     * @param $segments
     * @return string
     */
    protected function concatenate($segments)
	{
		return implode(' ', array_filter($segments, function($value)
		{
			return (string) $value !== '';
		}));
	}

    /**
     * 替换adn or
     * @param $value
     * @return mixed
     */
    protected function removeLeadingBoolean($value)
	{
		return preg_replace('/and |or /', '', $value, 1);
	}
}