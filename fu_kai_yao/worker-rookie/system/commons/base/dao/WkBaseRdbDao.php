<?php
namespace system\commons\base\dao;

use workerbase\classs\datalevels\BaseRdbDao;

/**
 * saas系统，rdb基础接口, 主要用于商户数据隔离，每个接口必须携带zId
 * @author fukaiyao
 *
 */
abstract class WkBaseRdbDao extends BaseRdbDao  implements IWkBaseRdbDao
{
    /**
     * 默认商户管理员id key
     */
    private $_adminKey = "zid";

    /**
     * {@inheritDoc}
     * @see BaseRdbDao::__construct()
     */
    public function __construct()
    {
        $adminKey = $this->adminKey();
        if (!empty($adminKey)) {
            $this->_adminKey = $adminKey;
        }
        parent::__construct();
    }

    /**
     * 配置管理id key
     * @return string
     */
    protected function adminKey()
    {
        return 'zid';
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::add()
     */
    public function add($zId, $info)
    {
        if (empty($info)) {
            return false;
        }
        $info[$this->_adminKey] = $zId;
        $statement = $this->getDb()->insert($this->_tableName, $info);
        $rowCount = $statement->rowCount();
        if ($rowCount == 1) {
            return $this->getDb()->id();
        } elseif ($rowCount > 1) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::deleteById()
     */
    public function deleteById($zId, $id)
    {
        if (empty($id) || empty($zId)) {
            return false;
        }
        $where[$this->_pk] = $id;
        $where[$this->_adminKey] = $zId;

        $statement = $this->getDb()->delete($this->_tableName, $where);
        if ($statement->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::deleteByIds()
     */
    public function deleteByIds($zId, $ids)
    {
        if (empty($ids) || empty($zId) || (!is_string($ids) && !is_array($ids))) {
            return false;
        }

        if(is_string($ids)){
            $ids = explode(',', trim($ids, ','));
        }

        $where[$this->_pk] = $ids;
        $where[$this->_adminKey] = $zId;

        $statement = $this->getDb()->delete($this->_tableName, $where);
        if ($statement->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::getInfoById()
     */
    public function getInfoById($zId, $id, $fields = null, $isLock = false)
    {
        if (empty($id) || empty($zId)) {
            return false;
        }
        $fields = empty($fields) ? "*" : $fields;

        if (is_string($fields) && $fields !== '*') {
            $fields = explode(',', trim($fields, ','));
        }

        $where[$this->_pk] = $id;
        $where[$this->_adminKey] = $zId;
        $where['LIMIT'] = 1;

        if ($isLock) {
            $sql =  $this->getDb()->getSql($this->_tableName, $fields, $where) . " FOR UPDATE";
            return $this->queryRowBySql($sql, []);
        }
        else {
            $res =  $this->getDb()->select($this->_tableName, $fields, $where);
            if ($res) {
                return $res[0];
            } else {
                return false;
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::getInfoByIds()
     */
    public function getInfoByIds($zId, $ids, $fields = null, $isLock = false)
    {
        if (empty($ids) || empty($zId) || (!is_string($ids) && !is_array($ids))) {
            return false;
        }

        if(is_string($ids)){
            $ids = explode(',', trim($ids, ','));
        }

        $fields = empty($fields) ? "*" : $fields;
        if (is_string($fields) && $fields !== '*') {
            $fields = explode(',', trim($fields, ','));
        }

        $where[$this->_pk] = $ids;
        $where[$this->_adminKey] = $zId;

        if ($isLock) {
            $sql = $this->getDb()->getSql($this->_tableName, $fields, $where) . " FOR UPDATE";
            return $this->queryAllBySql($sql, []);
        }
        else {
            return $this->getDb()->select($this->_tableName, $fields, $where);
        }
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::updateById()
     */
    public function updateById($zId, $id, array $info)
    {
        if (empty($zId) || empty($id) || empty($info)) {
            return false;
        }

        $where[$this->_pk] = $id;
        $where[$this->_adminKey] = $zId;

        $statement = $this->getDb()->update($this->_tableName, $info, $where);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::updateByIds()
     */
    public function updateByIds($zId, $ids, $info)
    {
        if (empty($zId) || empty($ids) || empty($info)) {
            return false;
        }

        if(is_string($ids)){
            $ids = explode(',', trim($ids, ','));
        }

        $where[$this->_pk] = $ids;
        $where[$this->_adminKey] = $zId;

        $this->getDb()->update($this->_tableName, $info, $where);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::getByZid()
     */
    public function getByZid($zId, $fields=null, array $order=[])
    {
        $zId = intval($zId);
        if ($zId < 1)  return false;

        $where[$this->_adminKey] = $zId;

        $fields = empty($fields) ? "*" : $fields;
        $order  = empty($order)?['id' => 'DESC']:$order;

        if (is_string($fields) && $fields !== '*') {
            $fields = explode(',', trim($fields, ','));
        }

        if (!empty($order)) {
            foreach ($order as $ok => $ov) {
                $order[$ok] = strtoupper($ov);
            }
            $where['ORDER'] = $order;
        }

        return $this->getDb()->select($this->_tableName, $fields, $where);
    }


    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::getCountByParams()
     */
    public function getCountByParams($zId, $params)
    {
        if(!is_array($params)){
            return false;
        }

        $where = array_merge([$this->_adminKey => $zId], $params);
        return $this->getDb()->count($this->_tableName, $where);
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::getByParams()
     */
    public function getByParams($zId, $params, $fields=null, $page=false, $pageSize=false, array $order=[], array $group=[])
    {
        if(!is_array($params)){
            return false;
        }

        $where = array_merge([$this->_adminKey => $zId], $params);

        $fields = empty($fields) ? "*" : $fields;
//        $order  = empty($order)?['id' => 'DESC']:$order;

        if (is_string($fields) && $fields !== '*') {
            $fields = explode(',', trim($fields, ','));
        }

        if (!empty($order)) {
            foreach ($order as $ok => $ov) {
                $order[$ok] = strtoupper($ov);
            }
            $where['ORDER'] = $order;
        }

        if (!empty($group)) {
            $where['GROUP'] = $group;
        }

        //页数为0，则不分页
        if ($page) {
            if (!$pageSize) {
                $pageSize = 20;
            }
            $where['LIMIT'] = [($page - 1) * $pageSize, $pageSize];
        }
        elseif ($pageSize) {
            //条数不为零则取相对应的条数
            $where['LIMIT'] = $pageSize;
        }

        return $this->getDb()->select($this->_tableName, $fields, $where);
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::getOneByParams()
     */
    public function getOneByParams($zId, $params, $fields=null, array $order=[], $isLock=false, array $group = [])
    {
        if(!is_array($params)){
            return false;
        }

        $where = array_merge([$this->_adminKey => $zId], $params);

        $fields = empty($fields) ? "*" : $fields;
//        $order  = empty($order)?['id' => 'DESC']:$order;

        if (is_string($fields) && $fields !== '*') {
            $fields = explode(',', trim($fields, ','));
        }

        if (!empty($order)) {
            foreach ($order as $ok => $ov) {
                $order[$ok] = strtoupper($ov);
            }
            $where['ORDER'] = $order;
        }

        if (!empty($group)) {
            $where['GROUP'] = $group;
        }

        $where['LIMIT'] = 1;

        if ($isLock) {
            $sql =  $this->getDb()->getSql($this->_tableName, $fields, $where) . " FOR UPDATE";
            return $this->queryRowBySql($sql, []);
        }
        else {
            $res =  $this->getDb()->select($this->_tableName, $fields, $where);
            if ($res) {
                return $res[0];
            } else {
                return false;
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::updateByParams()
     */
    public function updateByParams($zId, $params, $info)
    {
        if(!is_array($params) || empty($params)){
            return false;
        }

        if(!is_array($info) || empty($info)){
            return false;
        }

        $where = array_merge([$this->_adminKey => $zId], $params);
        $statement = $this->getDb()->update($this->_tableName, $info, $where);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IWkBaseRdbDao::incByParams()
     */
    public function incByParams($zId, $params, $field, $num = 1)
    {
        if(!is_array($params) || empty($params)){
            return false;
        }

        if(empty($field)){
            return false;
        }

        $where = array_merge([$this->_adminKey => $zId], $params);
        $statement = $this->getDb()->update($this->_tableName, [$field.'[+]' => $num], $where);
        return true;
    }

}