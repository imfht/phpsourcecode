<?php
/**
 * @package     Model.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月3日
 */
namespace SlimCustom\Libs\Model;

use \Closure;
use SlimCustom\Libs\App;
use SlimCustom\Libs\Support\Collection;
use SlimCustom\Libs\Contracts\Model\Query\Query as QueryInterface;
use SlimCustom\Libs\Model\Query\MongodbQuery;
use SlimCustom\Libs\Model\Query\PdoQuery;
use SlimCustom\Libs\Exception\SlimCustomException;

/**
 * Model
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class Model
{

    /**
     * 查询对象
     *
     * @var PdoQuery|MongodbQuery
     */
    protected $query;
    
    /**
     * 表名
     *
     * @var string
     */
    protected $table;
    
    /**
     * 闭包
     * 
     * @var \Closure
     */
    protected $closures = [];
    
    /**
     * 验证规则
     * 
     * @var array
     */
    protected $rules = [];
    
    /**
     * 支持的查询构造器驱动
     *
     * @var array
     */
    protected $support = [
        'pdo' => PdoQuery::class,
        'mongodb' => MongodbQuery::class,
    ];

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->connect()->table($this->table);
    }

    /**
     * 闭包查询 Query
     *
     * @param \Closure $callable
     * @return PdoQuery|MongodbQuery
     */
    public static function query(Closure $callable = null)
    {
        $model = App::di(static::class);
        return ($callable instanceof Closure) ? call_user_func_array($callable->bindTo($model), [$model->query]) : $model->query;
    }
    
    /**
     * 查询多条 Return Rows
     * 
     * @param array $pairs 注意：mysql对应columns, mongodb对应filter
     * @param array $options 选项 注意：mysql暂时不用
     * @param unknown $fetch_style
     * @param unknown $cursor_orientation
     * @param unknown $cursor_offset
     * @return []
     */
    public function rows($pairs = [], $options = [], $fetch_style = null, $cursor_orientation = null, $cursor_offset = null)
    {
        $statement = $this->rulesResolve()->query->statementResolve($this->query->select($pairs, $options));
        $rows = [];
        while ($row = $statement->fetch($fetch_style, $cursor_orientation, $cursor_offset)) {
            $row = new Collection($row);
            if ($this->closures) {
                foreach ($this->closures as $closure) {
                    if ($closure instanceof \Closure) {
                        $closure = $closure->bindTo($row);
                        $row = $closure($row);
                    }
                }
            }
            $rows[] = $row->toArray();
        }
        return $rows;
    }
    
    /**
     * 查询一条 Return Row
     * 
     * @param array $pairs 注意：mysql对应columns, mongodb对应filter
     * @param array $options 选项 注意：mysql暂时不用
     * @param unknown $fetch_style
     * @param unknown $cursor_orientation
     * @param unknown $cursor_offset
     * @return []
     */
    public function row($pairs = [], $options = ['limit' => 1], $fetch_style = null, $cursor_orientation = null, $cursor_offset = null)
    {
        $row = $this->rows($pairs, $options, $fetch_style, $cursor_orientation, $cursor_offset);
        return isset($row[0]) ? $row[0] : [];
    }
    
    /**
     * 插入 Insert Row
     * 
     * @param array $pairs
     * @param array $options Mongodb使用
     * @param string $insertId
     * @return mix|\MongoDB\InsertManyResult
     */
    public function create($pairs, $options = [])
    {
        return $this->rulesResolve($pairs)->query->statementResolve($this->query->insert($pairs, $options));
    }
    
    /**
     * 更新 Renew Row
     * 
     * @param array $pairs
     * @param array $filter mongodb使用
     * @param array $options mongodb使用
     * @return boolean|\MongoDB\UpdateResult
     */
    public function renew($pairs, $filter = [], $options = [])
    {
        return $this->rulesResolve($pairs)->query->statementResolve($this->query->update($pairs, $filter, $options));
    }
    
    /**
     * 删除 Remove Row
     * 
     * @param array $filter mongodb使用
     * @param array $options mongodb使用
     * @return boolean|\MongoDB\DeleteResult
     */
    public function remove($filter = [], $options = [])
    {
        return $this->rulesResolve()->query->statementResolve($this->query->delete($filter, $options));
    }
    
    /**
     * 绑定闭包
     * 
     * @param \Closure $closure
     * @return \SlimCustom\Libs\Model\Model|\Slim\PDO\Statement\SelectStatement|\Slim\PDO\Statement\InsertStatement|\Slim\PDO\Statement\UpdateStatement|\Slim\PDO\Statement\DeleteStatement|\MongoDB\Collection
     */
    public function bind(\Closure $closure)
    {
        array_unshift($this->closures, $closure);
        return $this;
    }
    
    /**
     * 设置表名
     *
     * @param string $table
     * @return \SlimCustom\Libs\Model\Model
     */
    public function table($table)
    {
        $this->table = $table;
        $this->query->table(config('database.prefix', '') . $this->table);
        return $this;
    }
    
    /**
     * 设置验证规则
     * 
     * @param array $rules
     * @return \SlimCustom\Libs\Model\Model
     */
    public function rules($rules = [], $pairs = [])
    {
        $this->rules = [
            $rules,
            $pairs
        ];
        return $this;
    }
    
    /**
     * 解析验证规则
     * 
     * @param array $pairs
     * @throws SlimCustomException
     * @return \SlimCustom\Libs\Model\Model
     */
    private function rulesResolve($pairs = [])
    {
        if ($this->rules) {
            if ($pairs) {
                $this->rules[1] = $pairs;
            }
            $validator = validator($this->rules[1], $this->rules[0]);
            if ($validator->fails()) {
                foreach ($validator->messagesInfo() as $msg) {
                    throw new SlimCustomException($msg[0], 1001);
                }
            }
        }
        return $this;
    }
    
    /**
     * 链接Query
     *
     * @return \SlimCustom\Libs\Model\Model
     */
    private function connect()
    {
        $this->query = App::di($this->support[strtolower(App::$instance->getContainer()['settings']['database']['default'])]);
        return $this;
    }
    
    /**
     * 魔术方法 __call
     *
     * @param string $method            
     * @param array $args            
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args = [])
    {
        if ($this->query instanceof QueryInterface && ! is_callable([$this->query, $method])) {
            $this->query->statements[] = function ($statement) use ($method, $args) {
                if (! is_callable([$statement, $method])) {
                    throw new \BadMethodCallException("Invalid Statement Method '{$method}' Called");
                }
                return call_user_func_array([$statement, $method], $args);
            };
            return $this;
        }
        
        return call_user_func_array([
            $this->query,
            $method
        ], $args);
    }

    /**
     * 魔术方法 __callstatic
     *
     * @param string $method
     * @param array $args            
     * @return mixed
     */
    public static function __callstatic($method, $args = [])
    {
        return call_user_func_array([
            App::di(static::class),
            $method
        ], $args);
    }

    /**
     * 关闭资源
     */
    public function __destruct()
    {
        if ($this->query) {
            return $this->query->close();
        }
    }
}