<?php
/**
 * @package     MongodbQuery.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年7月17日
 */

namespace SlimCustom\Libs\Model\Query;

use SlimCustom\Libs\Contracts\Model\Query\Query as QueryInterface;
use SlimCustom\Libs\Support\Collection;

/**
 * MongodbQuery
 * 
 * @author Jing Tang <tangjing3321@gmail.com>
 */
class MongodbQuery implements QueryInterface
{
    /**
     * 表名
     *
     * @var string
     */
    private $table;
    
    /**
     * 查询申明
     *
     * @var array
     */
    public $statements;
    
    /**
     * 初始化
     *
     * @param string $table
     */
    public function __construct($table = '')
    {
        ! $table ?: $this->table($table);
    }
    
    /**
     * 设置表名
     *
     * @param string $table
     * @return \SlimCustom\Libs\Model\Query\MongodbQuery
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }
    
    /**
     * 查询
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Model\Query\Query::select()
     * @return \SlimCustom\Libs\Support\Collection
     */
    public function select($filter = [], $options = [])
    {
        $cursor = database()->selectCollection($this->table)->find($filter, $options);
        return new Collection($cursor->toArray());
    }
    
    /**
     * 创建
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Model\Query\Query::insert()
     * @return \MongoDB\InsertManyResult
     */
    public function insert($documents, $options = [])
    {
        if (! isset($documents[0])) {
            $documents = [$documents];
        }
        return database()->selectCollection($this->table)->insertMany($documents, $options);
    }
    
    /**
     * 更新
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Model\Query\Query::update()
     * @return \MongoDB\UpdateResult
     */
    public function update($update, $filter = [], $options = [])
    {
        return database()->selectCollection($this->table)->updateMany($filter, $update, $options);
    }
    
    /**
     * 删除
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Model\Query\Query::delete()
     * @return \MongoDB\DeleteResult
     */
    public function delete($filter, $options = [])
    {
        return database()->selectCollection($this->table)->deleteMany($filter, $options);
    }
    
    /**
     * 查询申明
     *
     * @param Statement $statement
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function statementResolve($statement = null)
    {
        $statement ?: $statement = database()->selectCollection($this->table);
        if ($this->statements) {
            foreach ($this->statements as $closure) {
                $statement = $closure($statement);
            }
        }
        return $statement;
    }
    
    /**
     * 关闭资源
     *
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Model\Query\Query::close()
     * @return bool
     */
    public function close()
    {
        $this->table = null;
        $this->statements = [];
        return true;
    }
    
}