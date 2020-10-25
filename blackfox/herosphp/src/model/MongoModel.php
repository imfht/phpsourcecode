<?php
/**
 * MongoDB 访问模型封装
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */

namespace herosphp\model;

use herosphp\core\Loader;
use herosphp\core\WebApplication;
use herosphp\db\DBFactory;
use herosphp\exception\UnSupportedOperationException;
use herosphp\filter\Filter;
use herosphp\string\StringUtils;

class MongoModel implements IModel {

    /**
     * 数据库连接资源
     * @var \herosphp\db\mongo\MongoDB
     */
    private $db;

    /**
     * 数据表名称
     * @var string
     */
    private $table = '';

    /**
     * 数据过滤规则
     * @var array
     */
    protected $filterMap = array();

    private $where = array();

    private $fields = array();

    private $sort = array();

    private $limit = array();

    /**
     * 初始化数据库连接
     * @param string $table 数据表
     * @param array $config 数据库配置信息
     */
    public function __construct($table, $config = null) {

        //初始化数据库配置
        if ( !$config ) {
            $congfig = Loader::config('db');
        }
        //创建数据库
        $this->db = DBFactory::createDB('mongo', $congfig['mongo']);
        $this->table = $table;
    }

    /**
     * @param $sql
     * @throws UnSupportedOperationException
     */
    public function query($sql)
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * @see IModel::add()
     */
    public function add($data)
    {
        $data = $this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        if ( !isset($data['id']) ) {
            $data['id'] = StringUtils::genGlobalUid();
        }
        $id = $this->db->insert($this->table, $data);
        if ( $id === true ) {
            $id = $data['id'];
        }
        return $id;
    }

    /**
     * @see IModel::replace()
     */
    public function replace($data)
    {
        $data = $this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        return $this->db->replace($this->table, $data);
    }

    /**
     * @see IModel::delete()
     */
    public function delete($id)
    {
        return $this->deletes($id);
    }

    /**
     * @see IModel::deletes()
     */
    public function deletes($conditions)
    {
        return $this->db->delete($this->table, $this->getConditons($conditions));
    }

    /**
     * @see IModel::update()
     */
    public function update($data, $id)
    {
        $data = $this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }

        $where = array('id' => $id);
        return $this->db->update($this->table, $data, $where);
    }

    /**
     * @see IModel::updates()
     * @param $data
     * @param $conditions
     * @return bool|mixed
     */
    public function updates($data, $conditions)
    {
        $data = $this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }

        return $this->db->update($this->table, $data, $this->getConditons($conditions));
    }

    /**
     * @see IModel::getItems()
     */
    public function getItems($conditions, $fields, $order, $limit, $group, $having)
    {
        return  $this->db->find($this->table, $conditions, $fields, $order, $limit, $group, $having);

    }

    public function find()
    {
        return $this->getItems($this->where, $this->fields, $this->sort, $this->limit);
    }

    /**
     * @see IModel::getItem()
     */
    public function getItem($condition, $fields, $order)
    {
        return $this->db->findOne($this->table, $this->getConditons($condition), $fields);
    }

    public function findOne()
    {
        return $this->getItem($this->where, $this->fields);
    }

    /**
     * @see IModel::count()
     * @param $conditions
     * @return int
     */
    public function count($conditions)
    {
        return $this->db->count($this->table, $this->getConditons($conditions));
    }

    /**
     * @see IModel::increase()
     * @param tring $field
     * @param int $offset
     * @param int $id
     * @return bool|\PDOStatement
     */
    public function increase($field, $offset, $id)
    {
        return $this->batchIncrease($field, $offset, $id);
    }

    /**
     * @see IModel::batchIncrease()
     * @param string $field
     * @param int $offset
     * @param array|string $conditions
     * @return mixed|\PDOStatement
     */
    public function batchIncrease($field, $offset, $conditions)
    {
        $data = array($field => $offset);
        return $this->db->inc($this->table, $data, $this->getConditons($conditions));
    }

    /**
     * @see IModel::reduce()
     * @param string $field
     * @param int $offset
     * @param int $id
     * @return mixed|\PDOStatement
     */
    public function reduce($field, $offset, $id)
    {
        return $this->increase($field, -$offset, $id);
    }

    /**
     * @see IModel::batchReduce()
     */
    public function batchReduce($field, $offset, $conditions)
    {
        return $this->batchIncrease($field, -$offset, $conditions);
    }

    /**
     * @see IModel::set()
     */
    public function set($field, $value, $id)
    {
        $data = array($field => $value);
        return $this->update($data, $id);
    }

    /**
     * @see IModel::sets()
     */
    public function sets($field, $value, $conditions)
    {
        $data = array($field => $value);
        return $this->updates($data, $conditions);
    }

    /**
     * 分组获取数据
     * @param $table 集合名称
     * @param array $keys 分组字段
     * @param array $initial 分组初始条件
     * @param $reduce 分组计算方式，是一个javascript函数表达式 "function (obj, prev) { prev.items.push(obj.name); }"
     * @param $conditions 分组过滤条件
     * @param $get_all_info 是否显示所有信息
     * @return array
     */
    public function findByGroup($keys, $initial, $reduce, $conditions, $get_all_info=false) {
        return $this->db->group($this->table, $keys, $initial, $reduce, $conditions, $get_all_info);
    }

    /**
     * @see IModel::beginTransaction()
     */
    public function beginTransaction()
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * @see IModel::commit()
     */
    public function commit()
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * @see IModel::rollback()
     */
    public function rollback()
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * @see IModel::inTransaction()
     */
    public function inTransaction()
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * @see IModel::writeLock()
     */
    public function writeLock()
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * @see IModel::readLock()
     */
    public function readLock()
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * @see IModel::unLock()
     */
    public function unLock()
    {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    /**
     * 获取查询条件
     * @param $conditons
     * @return MongoEntity
     */
    private function getConditons($conditions) {

        if ( !is_array($conditions) ) {
            return array('id' => $conditions);
        }

        return $conditions;

    }

    /**
     * 获取过滤后的数据
     * @param $data
     * @return mixed
     */
    protected function loadFilterData(&$data) {

        $filterMap = $this->getFilterMap();
        if ( empty($filterMap) ) {
            return $data;
        }
        $error = null;
        $_data = Filter::loadFromModel($data, $filterMap, $error);

        if ( $_data == false ) {
            WebApplication::getInstance()->getAppError()->setCode(1);
            WebApplication::getInstance()->getAppError()->setMessage($error);
        }
        return $_data;
    }

    /**
     * @param array $filter
     */
    public function setFilterMap($filter)
    {
        $this->filterMap = $filter;
    }

    /**
     * @return array
     */
    public function getFilterMap()
    {
        return $this->filterMap;
    }

    /**
     * 设置表名
     * @param $table
     */
    public function setTable($table) {
        $this->table = $table;
    }

    public function where($where) {
        $this->where = $where;
        return $this;
    }

    public function field($fields) {
        $this->fields = $fields;
        return $this;
    }

    public function limit($page, $size) {
        $this->limit = array($page, $size);
        return $this;
    }

    public function sort($sort) {
        $this->sort = $sort;
        return $this;
    }

    public function group($group) {
        throw new UnSupportedOperationException();
    }

    public function having($group) {
        throw new UnSupportedOperationException("暂时不支持此操作.");
    }

    public function getDB() {
        return $this->db->getDB();
    }

}
