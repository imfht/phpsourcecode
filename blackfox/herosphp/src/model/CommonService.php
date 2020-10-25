<?php
/**
 * 通用Model服务抽象类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v3.0.0
 */
namespace herosphp\model;

use herosphp\core\Loader;
use herosphp\db\utils\MysqlQueryBuilder;

abstract class CommonService {

    /**
     * 数据模型操作DAO
     * @var MysqlModel
     */
    protected $modelDao;

    /**
     * 模型类名称
     * @var string
     */
    protected $modelClassName;

    /**
     * 构造函数，初始化modelDao
     * @param $model
     */
    public function __construct() {
        $this->modelDao = Loader::model($this->modelClassName);
    }

    /**
     * @see MysqlModel::add()
     */
    public function add($data)
    {
        return $this->modelDao->add($data);
    }

    /**
     * @see MysqlModel::replace()
     */
    public function replace($data)
    {
        return $this->modelDao->replace($data);
    }

    /**
     * @see MysqlModel::delete()
     */
    public function delete($id)
    {
        return $this->modelDao->delete($id);
    }

    /**
     * @see MysqlModel::deletes()
     */
    public function deletes()
    {
        return $this->modelDao->deletes();
    }

    /**
     * @see MysqlModel::find()
     */
    public function find()
    {
        return $this->modelDao->find();

    }

    /**
     * @see MysqlModel::getList()
     */
    public function getList($sql) {
        return $this->modelDao->getList($sql);
    }

    /**
     * @see MysqlModel::findOne()
     */
    public function findOne()
    {
        return $this->modelDao->findOne();
    }

    /**
     * @see MysqlModel::findById()
     */
    public function findById($id)
    {
        $this->getSqlBuilder()->clear();
        return $this->modelDao->findById($id);
    }

    /**
     * @see MysqlModel::update()
     */
    public function update($data, $id)
    {
        // check if there is exists the record
        $item = $this->modelDao->findById($id);
        if (empty($item)) {
            return false;
        }
        $this->getSqlBuilder()->clear();
        return $this->modelDao->update($data, $id);
    }

    /**
     * @see MysqlModel::updates()
     */
    public function updates($data)
    {
        $sqlBuilder = clone $this->modelDao->getSqlBuilder();
        $items = $this->modelDao->find();
        if (empty($items)) {
            return false;
        }
        $this->modelDao->setSqlBuilder($sqlBuilder);
        return $this->modelDao->updates($data);
    }

    /**
     * @see MysqlModel::count()
     */
    public function count()
    {
        return $this->modelDao->count();
    }

    /**
     * @see MysqlModel::increase()
     */
    public function increase($field, $offset, $id)
    {
        // check if there is exists the record
        $item = $this->modelDao->findById($id);
        if (empty($item)) {
            return false;
        }
        $this->getSqlBuilder()->clear();
        return $this->modelDao->increase($field, $offset, $id);
    }

    /**
     * @see MysqlModel::batchIncrease()
     */
    public function batchIncrease($field, $offset)
    {
        $sqlBuilder = clone $this->modelDao->getSqlBuilder();
        $items = $this->modelDao->find();
        if (empty($items)) {
            return false;
        }
        $this->modelDao->setSqlBuilder($sqlBuilder);
        return $this->modelDao->batchIncrease($field, $offset);
    }

    /**
     * @see MysqlModel::reduce()
     */
    public function reduce($field, $offset, $id)
    {
        // check if there is exists the record
        $item = $this->modelDao->findById($id);
        if (empty($item)) {
            return false;
        }
        $this->getSqlBuilder()->clear();
        return $this->modelDao->reduce($field, $offset, $id);
    }

    /**
     * @see MysqlModel::batchReduce()
     */
    public function batchReduce($field, $offset)
    {
        $sqlBuilder = clone $this->modelDao->getSqlBuilder();
        $items = $this->modelDao->find();
        if (empty($items)) {
            return false;
        }
        $this->modelDao->setSqlBuilder($sqlBuilder);
        return $this->modelDao->batchReduce($field, $offset);
    }

    /**
     * @see MysqlModel::set()
     */
    public function set($field, $value, $id)
    {
        // check if there is exists the record
        $item = $this->modelDao->findById($id);
        if (empty($item)) {
            return false;
        }
        $this->getSqlBuilder()->clear();
        return $this->modelDao->set($field, $value, $id);
    }

    /**
     * @see MysqlModel::sets()
     */
    public function sets($field, $value)
    {
        $sqlBuilder = clone $this->modelDao->getSqlBuilder();
        $items = $this->modelDao->find();
        if (empty($items)) {
            return false;
        }
        $this->modelDao->setSqlBuilder($sqlBuilder);
        return $this->modelDao->sets($field, $value);
    }

    /**
     * @see MysqlModel::beginTransaction()
     */
    public function beginTransaction()
    {
        $this->modelDao->beginTransaction();
    }

    /**
     * @see MysqlModel::commit()
     */
    public function commit()
    {
        $this->modelDao->commit();
    }

    /**
     * @see MysqlModel::rollback()
     */
    public function rollback()
    {
        $this->modelDao->rollback();
    }

    /**
     * @see MysqlModel::inTransaction()
     */
    public function inTransaction()
    {
        return $this->modelDao->inTransaction();
    }

    /**
     * @see MysqlModel::getDB()
     */
    public function getDB()
    {
        return $this->modelDao->getDB();
    }

    /**
     * @see MysqlModel::where()
     */
    public function where($field, $opt=null, $value=null) {
        $this->modelDao->where($field, $opt, $value);
        return $this;
    }

    /**
     * @see MysqlModel::whereOr()
     */
    public function whereOr($field, $opt=null, $value=null) {
        $this->modelDao->whereOr($field, $opt, $value);
        return $this;
    }

    /**
     * @see MysqlModel::fields()
     */
    public function fields($fields) {
        $this->modelDao->fields($fields);
        return $this;
    }

    /**
     * @see MysqlModel::page()
     */
    public function page($page, $size) {
        $this->modelDao->page($page, $size);
        return $this;
    }

    /**
     * @see MysqlModel::offset()
     */
    public function offset($offset, $size) {
        $this->modelDao->offset($offset, $size);
        return $this;
    }

    /**
     * @see MysqlModel::order()
     */
    public function order($order) {
        $this->modelDao->order($order);
        return $this;
    }

    /**
     * @see MysqlModel::group()
     */
    public function group($group) {
        $this->modelDao->group($group);
        return $this;
    }

    /**
     * @see MysqlModel::having()
     */
    public function having($field, $opt=null, $value) {
        $this->modelDao->having($field, $opt, $value);
        return $this;
    }

    /**
     * @see MysqlModel::havingOr()
     */
    public function havingOr($field, $opt=null, $value) {
        $this->modelDao->havingOr($field, $opt, $value);
        return $this;
    }

    /**
     * @see MysqlModel::alias()
     */
    public function alias($alias) {
        $this->modelDao->alias($alias);
        return $this;
    }

    /**
     * @see MysqlModel::join()
     */
    public function join($table, $joinType=MYSQL_JOIN_LEFT) {
        $this->modelDao->join($table, $joinType);
        return $this;
    }

    /**
     * @see MysqlModel::on()
     */
    public function on($joinCondition) {
        $this->modelDao->on($joinCondition);
        return $this;
    }

    /**
     * @return MysqlQueryBuilder
     */
    public function getSqlBuilder()
    {
        return $this->modelDao->getSqlBuilder();
    }

    /**
     * @param MysqlQueryBuilder $sqlBuilder
     * @return $this
     */
    public function setSqlBuilder($sqlBuilder)
    {
       $this->modelDao->setSqlBuilder($sqlBuilder);
        return $this;
    }

    /**
     * @return MysqlModel
     */
    public function getModelDao()
    {
        return $this->modelDao;
    }

    /**
     * @param MysqlModel $modelDao
     */
    public function setModelDao($modelDao)
    {
        $this->modelDao = $modelDao;
    }
}
