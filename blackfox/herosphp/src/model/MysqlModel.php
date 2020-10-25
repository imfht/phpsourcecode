<?php
/**
 * mysql模型数据库访问封装
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */

namespace herosphp\model;

use herosphp\core\Loader;
use herosphp\core\WebApplication;
use herosphp\db\DBFactory;
use herosphp\db\driver\Mysql;
use herosphp\db\utils\MysqlQueryBuilder;
use herosphp\filter\Filter;
use herosphp\string\StringUtils;

class MysqlModel {

    /**
     * 数据库连接资源
     * @var Mysql
     */
    protected $db;

    //数据表主键
    protected $primaryKey = 'id';
    //是否自动产生ID，如果没有传入的ID的话
    protected $autoPrimary = false;
    //数据表名称
    protected $table = '';
    //数据过滤规则
    protected $filterMap = array();
    /**
     * SQL sqlBuilder
     * @var MysqlQueryBuilder
     */
    protected $sqlBuilder;

    /**
     * 初始化数据库连接
     * @param string $table 数据表
     * @param array $config 数据库配置信息
     */
    public function __construct($table, $config = null) {

        //初始化数据库配置
        if ( !$config ) {
            //根据不同环境来连接不同的数据库
            if( defined('ENV_CFG') ){
                $dbConfigs = Loader::config('db', 'env.'.ENV_CFG);
            }else{
                $dbConfigs = Loader::config('db');
            }
            $db_config = $dbConfigs['mysql'];
            $this->table = $table;
            if ( DB_ACCESS == DB_ACCESS_SINGLE ) {  //单台服务器
                $config = $db_config[0];
            } else if ( DB_ACCESS == DB_ACCESS_CLUSTERS ) { //多台服务器
                $config = $db_config;
            }

        }
        //创建数据库连接对象
        $this->db = DBFactory::createDB(DB_ACCESS, $config);
        $this->sqlBuilder = new MysqlQueryBuilder($this->table);
    }

    /**
     * 获取查询结果集
     * @param string $sql
     * @return mixed|\PDOStatement
     */
    public function getList($sql)
    {
        return $this->db->getList($sql);
    }

    /**
     * 添加数据
     * @param array $data
     * @return bool|mixed
     */
    public function add(array $data)
    {
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        if ( !isset($data[$this->primaryKey]) && $this->autoPrimary ) {
            $data[$this->primaryKey] = StringUtils::genGlobalUid();
        }

        $result = $this->db->insert($this->table, $data);
        if ( $result === true ) { //非自增ID
            $result = $data[$this->primaryKey];
        }

        return $result;
    }

    /**
     * 替换数据
     * @param array $data
     * @return bool
     */
    public function replace(array $data)
    {
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        if ( !isset($data[$this->primaryKey]) && $this->autoPrimary ) {
            $data[$this->primaryKey] = StringUtils::genGlobalUid();
        }
        $result = $this->db->replace($this->table, $data);

        return $result;
    }

    /**
     * 删除指定的一条数据
     * @param $id
     * @return bool|int
     */
    public function delete($id)
    {
        $this->sqlBuilder->addWhere($this->primaryKey, $id);
        return $this->deletes();
    }

    /**
     * @return bool|int
     */
    public function deletes()
    {
        $conditions = $this->sqlBuilder->buildCondition();
        return $this->db->delete($this->table, $conditions);
    }

    /**
     * 更新一条数据
     * @param array $data
     * @param $id
     * @return bool|int
     */
    public function update(array $data, $id)
    {
        $this->sqlBuilder->addWhere($this->primaryKey, $id);
        return $this->updates($data);
    }

    /**
     * 更新多条数据
     * @param array $data
     * @return bool|int
     */
    public function updates($data)
    {
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        $conditions = $this->sqlBuilder->buildCondition();
        return $this->db->update($this->table, $data, $conditions);
    }

    /**
     * 查找数据列表
     * @return array
     */
    public function &find()
    {
        $sql = $this->sqlBuilder->buildQueryString();
        return $this->db->getList($sql);
    }

    /**
     * 查找单条记录
     * @return array
     */
    public function &findOne()
    {
        $sql = $this->sqlBuilder->buildQueryString();
        return $this->db->getOneRow($sql);
    }

    /**
     * 通过ID查找
     * @param $id
     * @return array
     */
    public function findById($id) {
        $this->sqlBuilder->addWhere($this->primaryKey, $id);
        $sql = $this->sqlBuilder->buildQueryString();
        return $this->db->getOneRow($sql);
    }

    /**
     * 统计记录行数
     * @return int
     */
    public function count()
    {
        $sql = $this->sqlBuilder->buildCountSql();
        return $this->db->count($sql);
    }

    /**
     * 增加某一字段的值
     * @param $field
     * @param $offset
     * @param $id
     * @return int
     */
    public function increase($field, $offset, $id)
    {
        $this->sqlBuilder->addWhere($this->primaryKey, $id);
       return $this->batchIncrease($field, $offset);
    }

    /**
     * 批量 increase
     * @param $field
     * @param $offset
     * @return int
     */
    public function batchIncrease($field, $offset)
    {
        $conditions = $this->sqlBuilder->buildCondition();
        $update_str = '';
        if ( is_array($field) && is_array($offset) && count($field) == count($offset) ) {
            foreach ( $field as $key => $value ) {
                $updateUnit = "{$value}=CONCAT({$value}, '{$offset[$key]}')";
                if ( is_numeric($offset[$key]) ) {
                    $updateUnit = "{$value}={$value} + {$offset[$key]}";
                }
                $update_str .= $update_str == '' ? $updateUnit : ','.$updateUnit;
            }
        } else {
            if ( is_numeric($offset) ) {
                $update_str .= "{$field}={$field} + {$offset}";
            } else {
                $update_str .= "{$field}=CONCAT({$field}, '{$offset}')";
            }
        }

        $query = "UPDATE {$this->table} SET {$update_str} {$conditions}";
        $result = $this->db->execute($query);
        return ($result->rowCount());
    }

    /**
     * 减少某一字段的值
     * @param string $field
     * @param int $offset
     * @param $id
     * @return int
     */
    public function reduce($field, $offset, $id)
    {
        $this->sqlBuilder->addWhere($this->primaryKey, $id);
        return $this->batchReduce($field, $offset);
    }

    /**
     * 皮脸 reduce
     * @param $field
     * @param $offset
     * @return int
     */
    public function batchReduce($field, $offset)
    {
        $conditions = $this->sqlBuilder->buildCondition();
        $update_str = '';
        if ( is_array($field) && is_array($offset) && count($field) == count($offset) ) {
            foreach ( $field as $key => $value ) {
                $updateUnit = "{$value}=REPLACE({$value}, '{$offset[$key]}', '')";
                if ( is_numeric($offset[$key]) ) {
                    $updateUnit = "{$value}={$value} - {$offset[$key]}";
                }
                $update_str .= $update_str == '' ? $updateUnit : ','.$updateUnit;
            }
        } else {
            if ( is_numeric($offset) ) {
                $update_str .= "{$field}={$field} - {$offset}";
            } else {
                $update_str .= "{$field}=REPLACE({$field}, '{$offset}', '')";
            }
        }
        $query = "UPDATE {$this->table} SET {$update_str} {$conditions}";
        $result = $this->db->execute($query);
        return ($result->rowCount());
    }

    /**
     * @param $field
     * @param $value
     * @param $id
     * @return bool|int
     */
    public function set($field, $value, $id)
    {
        $data = array($field => $value);
        return $this->update($data, $id);
    }

    /**
     * @param $field
     * @param $value
     * @return bool|int
     */
    public function sets($field, $value)
    {
        $data = array($field => $value);
        return $this->updates($data);
    }

    /**
     * 开始事务
     */
    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    /**
     * 事务提交
     */
    public function commit()
    {
        $this->db->commit();
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {
        $this->db->rollBack();
    }

    /**
     * 返回当前连接是否有未提交的事务
     * @return bool
     */
    public function inTransaction()
    {
        return $this->db->inTransaction();
    }

    /**
     * 写锁定
     * @return boolean
     */
    public function writeLock(){
        return $this->db->execute("lock tables {$this->table} write");
    }

    /**
     * 读锁定
     * @return boolean
     */
    public function readLock(){
        return $this->db->execute("lock tables {$this->table} read");
    }

    /**
     * 解锁
     * @return boolean
     */
    public function unLock(){
        return $this->db->execute("unlock tables");
    }


    /**
     * 获取过滤后的数据
     * @param $data
     * @return mixed
     */
    protected function &loadFilterData(&$data) {

        if ( empty($this->filterMap) ) {
            return $data;
        }
        $error = null;
        $data = Filter::loadFromModel($data, $this->filterMap, $error);

        if ( $data == false ) {
            WebApplication::getInstance()->getAppError()->setCode(1);
            WebApplication::getInstance()->getAppError()->setMessage($error);
        }
        return $data;
    }

    /**
     * 获取数据连接对象
     * @return Mysql
     */
    public function getDB() {
        return $this->db;
    }

    /**
     * 添加逻辑 AND 查询条件
     * @param mixed $field
     * @param string $opt
     * @param string|array $value
     * @return $this
     */
    public function where($field, $opt=null, $value=null) {
        //如果是复杂的组合查询比如 (id=1 AND name='xxx') OR (sex='M' AND add='Beijing')
        if ( $field instanceof \Closure ) {
            $this->sqlBuilder->sqlAppend(" AND (");
            $this->sqlBuilder->enterClosure();
            call_user_func($field);
            $this->sqlBuilder->addWhere(") ");
        } else {
            $this->sqlBuilder->addWhere($field, $opt, $value, 'AND');
        }
        return $this;
    }

    /**
     * 添加逻辑 OR 查询条件
     * @param mixed $field
     * @param string $opt
     * @param string|array $value
     * @return $this
     */
    public function whereOr($field, $opt=null, $value=null) {
        if ( $field instanceof \Closure ) {
            $this->sqlBuilder->sqlAppend(" OR (");
            $this->sqlBuilder->enterClosure();
            call_user_func($field);
            $this->sqlBuilder->addWhere(") ");
        } else {
            $this->sqlBuilder->addWhere($field, $opt, $value, 'OR');
        }
        return $this;
    }

    /**
     * 设置查询字段
     * @param $fields
     * @return $this
     */
    public function fields($fields) {
        $this->sqlBuilder->fields($fields);
        return $this;
    }

    /**
     * 设置数据表别名
     * @param $alias
     * @return $this
     */
    public function alias($alias) {
        $this->sqlBuilder->alias($alias);
        return $this;
    }

    /**
     * 设置分页查询
     * @param $page
     * @param $size
     * @return $this
     */
    public function page($page, $size) {
        if ( $page <= 0 ) $page = 1;
        $offset = ($page-1) * $size;
        return $this->offset($offset, $size);
    }

    /**
     * 偏移量查询
     * @param $offset
     * @param $size
     * @return $this
     */
    public function offset($offset, $size) {
        $this->sqlBuilder->limit("{$offset}, {$size}");
        return $this;
    }

    /**
     * 设置排序方式
     * @param $order
     * @return $this
     */
    public function order($order) {
        $this->sqlBuilder->order($order);
        return $this;
    }

    /**
     * 设置分组方式
     * @param $group
     * @return $this
     */
    public function group($group) {
        $this->sqlBuilder->group($group);
        return $this;
    }

    /**
     * 添加 AND 分组查询条件
     * @param string $field
     * @param string $opt
     * @param string|array $value
     * @return $this
     */
    public function having($field, $opt=null, $value) {

        if ( $field instanceof \Closure ) {    //处理闭包
            $this->sqlBuilder->sqlAppend(" AND (");
            $this->sqlBuilder->enterClosure();
            call_user_func($field);
            $this->sqlBuilder->addHaving(") ");
        } else {
            $this->sqlBuilder->addHaving($field, $opt, $value, 'AND');
        }
        return $this;
    }

    /**
     * 添加 OR 分组查询条件
     * @param string $field
     * @param string $opt
     * @param string|array $value
     * @return $this
     */
    public function havingOr($field, $opt=null, $value) {

        if ( $field instanceof \Closure ) {    //处理闭包
            $this->sqlBuilder->sqlAppend(" OR (");
            $this->sqlBuilder->enterClosure();
            call_user_func($field);
            $this->sqlBuilder->addHaving(") ");
        } else {
            $this->sqlBuilder->addHaving($field, $opt, $value, 'OR');
        }
        return $this;
    }

    /**
     * 设置连接方式
     * @param $table
     * @param string $joinType
     * @return $this
     */
    public function join($table, $joinType=MYSQL_JOIN_LEFT) {
        $this->sqlBuilder->join($table, $joinType);
        return $this;
    }

    /**
     * 设置连接查询条件
     * @param $joinCondition
     * @return $this
     */
    public function on($joinCondition) {
        $this->sqlBuilder->on($joinCondition);
        return $this;
    }

    /**
     * @return MysqlQueryBuilder
     */
    public function getSqlBuilder()
    {
        return $this->sqlBuilder;
    }

    /**
     * @param MysqlQueryBuilder $sqlBuilder
     */
    public function setSqlBuilder($sqlBuilder)
    {
        $this->sqlBuilder = $sqlBuilder;
    }

}
