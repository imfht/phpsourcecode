<?php
/**
 * @package     PdoQuery.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月10日
 */

namespace SlimCustom\Libs\Model\Query;

use SlimCustom\Libs\Contracts\Model\Query\Query as QueryInterface;
use Slim\PDO\Statement\SelectStatement;
use Slim\PDO\Statement\InsertStatement;
use Slim\PDO\Statement\UpdateStatement;
use Slim\PDO\Statement\DeleteStatement;

/**
 * PdoQuery
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class PdoQuery implements QueryInterface
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
     * @return \SlimCustom\Libs\Model\Query
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * 查询
     * 
     * @param array $columns   
     * @return \Slim\PDO\Statement\SelectStatement         
     */
    public function select($columns = ['*'], $options = [])
    {
        $statements = database()->select($columns)->from($this->table);
        if (isset($options['limit'])) {
            $statements->limit($options['limit'], 0);
        }
        return $statements;
    }

    /**
     * 插入
     * 
     * @param array $pairs            
     * @return \Slim\PDO\Statement\InsertStatement
     */
    public function insert($pairs)
    {
        return database()->insert($pairs)->into($this->table);
    }

    /**
     * 更新
     * 
     * @param array $pairs     
     * @return \Slim\PDO\Statement\UpdateStatement       
     */
    public function update($pairs)
    {
        return database()->update($pairs)->table($this->table);
    }

    /**
     * 删除
     * 
     * @param string $table            
     * @return \Slim\PDO\Statement\DeleteStatement
     */
    public function delete($table = null)
    {
        return database()->delete($table)->from($this->table);
    }
    
    /**
     * 查询申明
     *
     * @param Statement $statement
     * @throws \BadMethodCallException
     * @return \Slim\PDO\Statement
     */
    public function statementResolve($statement = null)
    {
        $statement ?: $statement = database()->selectCollection($this->table);
        if ($this->statements) {
            foreach ($this->statements as $closure) {
                $result = $closure($statement);
                if (is_object($result)) {
                    $statement = $result;
                }
            }
        }
        
        return $statement->execute();
    }
    
    /**
     * 关闭资源
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Model\Query\Query::close()
     */
    public function close()
    {
        $this->table = null;
        $this->statements = [];
        return true;
    }
}