<?php
namespace workerbase\classs\datalevels;

/**
 * 关系型数据库Dao实现
 * @author fuakaiyao
 */
abstract class BaseCrudRdbDao extends BaseRdbDao implements IBaseCrudListDao
{
    /**
     * {@inheritDoc}
     * @see IBaseCrudListDao::add()
     */
    public function add($info)
    {
        if (empty($info)) {
            return false;
        }
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
     * @see IBaseCrudListDao::deleteById()
     */
    public function deleteById($id)
    {
        if (empty($id)) {
            return false;
        }
        $where[$this->_pk] = $id;
        $statement = $this->getDb()->delete($this->_tableName, $where);
        if ($statement->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IBaseCrudListDao::deleteByIds()
     */
    public function deleteByIds($ids)
    {
        if (empty($ids) || (!is_string($ids) && !is_array($ids))) {
            return false;
        }

        if(is_string($ids)){
            $ids = explode(',', trim($ids, ','));
        }

        $where[$this->_pk] = $ids;
        $statement = $this->getDb()->delete($this->_tableName, $where);
        if ($statement->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IBaseCrudListDao::getInfoById()
     */
    public function getInfoById($id, $fields = null, $isLock = false)
    {
        if (empty($id)) {
            return false;
        }
        $fields = empty($fields) ? "*" : $fields;

        if (is_string($fields) && $fields !== '*') {
            $fields = explode(',', trim($fields, ','));
        }

        $where[$this->_pk] = $id;
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
     * @see IBaseCrudListDao::getInfoByIds()
     */
    public function getInfoByIds($ids, $fields = null, $isLock = false)
    {
        if (empty($ids) || (!is_string($ids) && !is_array($ids))) {
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
     * @see IBaseCrudListDao::updateById()
     */
    public function updateById($id, $info)
    {
        if (empty($id) || empty($info)) {
            return false;
        }

        $where[$this->_pk] = $id;
        $statement = $this->getDb()->update($this->_tableName, $info, $where);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IBaseCrudListDao::updateByIds()
     */
    public function updateByIds($ids, $info)
    {
        if (empty($ids) || empty($info)) {
            return false;
        }

        if(is_string($ids)){
            $ids = explode(',', trim($ids, ','));
        }

        $where[$this->_pk] = $ids;
        $this->getDb()->update($this->_tableName, $info, $where);
        return true;
    }


    /**
     * {@inheritDoc}
     * @see IBaseCrudListDao::getCountByParams()
     */
    public function getCountByParams($params)
    {
        if(!is_array($params)){
            return false;
        }

        return $this->getDb()->count($this->_tableName, $params);
    }

    /**
     * {@inheritDoc}
     * @see IBaseCrudListDao::getByParams()
     */
    public function getByParams($params, $fields=null, $page=false, $pageSize=false, array $order=[], array $group=[])
    {
        if(!is_array($params)){
            return false;
        }

        $where = $params;

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
     * @see IBaseCrudListDao::getOneByParams()
     */
    public function getOneByParams($params, $fields=null, array $order=[], $isLock=false, array $group = [])
    {
        if(!is_array($params)){
            return false;
        }

        $where = $params;

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
     * @see IBaseCrudListDao::updateByParams()
     */
    public function updateByParams($params, $info)
    {
        if(!is_array($params) || empty($params)){
            return false;
        }

        if(!is_array($info) || empty($info)){
            return false;
        }

        $statement = $this->getDb()->update($this->_tableName, $info, $params);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see IBaseCrudListDao::incByParams()
     */
    public function incByParams($params, $field, $num = 1)
    {
        if(!is_array($params) || empty($params)){
            return false;
        }

        if(empty($field)){
            return false;
        }

        $statement = $this->getDb()->update($this->_tableName, [$field.'[+]' => $num], $params);
        return true;
    }

}