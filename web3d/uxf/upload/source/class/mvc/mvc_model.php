<?php
/**
 * 模型类，Db模型
 * dz的table类主要侧重单表以主键为基础的操作，故在此类中扩展一些常用的方法
 */
class Mvc_Model extends Discuz_Table {
    /**
     * 将任意的查询语句转换成count预计
     * @param string $sql
     * @return string
     */
    public function buildCountSql($sql){
        $sql = preg_replace("#SELECT[ \r\n\t](.*)[ \r\n\t]FROM#is", 'SELECT COUNT(*) AS COUNT FROM', $sql);
        $sql = preg_replace("#ORDER[ \r\n\t]{1,}BY(.*)#is", '', $sql);
        return $sql;
    }

    /**
     * 获取一条数据
     * @param     string|array    $condition
     * @param     string    $field
     * @return    array
     */
    public function __getOne($condition = '1=1', $field = '*') {
        $row = DB::fetch_first("SELECT $field FROM `".DB::table($this->_table)."` WHERE " . $this->_buildWhereStr($condition));

        return $row;
    }

    /**
     * 返回指定条件的记录数
     * @param string $condition
     * @return int
     */
    public function __count($condition = '1=1') {
        $row = DB::result_first("SELECT count(*) as count FROM `".DB::table($this->_table)."` WHERE " . $this->_buildWhereStr($condition));

        return (int)$row;
    }

    /**
     * 获取全部数据，适用于少量数据
     * @param string|array $condition 条件
     * @param string $field 字段
     * @param string $start 起始
     * @param string $limit 条数
     * @param string $orderby 排序
     * @param string $keyfield 用作键名的字符串
     * @return array
     */
    public function __getAll($condition = '1=1', $field = '*', $start =0, $limit = 500, $orderby = '', $keyfield = '') {

        $limit = DB::limit($start, $limit);
        $sql = "SELECT $field FROM `".DB::table($this->_table)."` WHERE " . $this->_buildWhereStr($condition) ." $orderby $limit";
        $data = DB::fetch_all($sql, null, $keyfield);

        return $data;
    }

    /**
     * 根据条件更新数据
     * @param string|array $condition
     * @param array $data 可以数组也可以字符串形式，数组必须成对
     * @return boolean|int 成功则返回影响的条数
     */
    public function __update($condition = "", $data = array()) {
        if (empty($data)) {
            return false;
        }

        $ret = DB::update($this->_table, $data, $condition);

        return $ret;
    }

    /**
     * 删除指定条件下的记录
     * @param string|array $condition
     * @return int 返回影响条数
     */
    public function __delete($condition = "1") {
        $ret = DB::delete($this->_table, $condition);

        return $ret;
    }
    
    /**
     * 构造where条件的语句，从database类中提取出来的语句
     * @param mixed $condition
    * $condition 支持两种格式：
    * 第一种：
    * $id = 500;
    * $sids = array(4,5,6);
    * $condition = array(
    *      'where' => ' id < %d and sid in ($n)',
    *      'arg' => array($id, $sids)
    * );
    * 第二种：
    * $id = 500;
    * $sid = 6;
    * $condition = array(
    *      'id' => $id,
    *      'sid' => $sid
    * );
     * @return string
     */
    private function _buildWhereStr($condition = '') {
        if (is_array($condition)) {
            if (count($condition) == 2 && isset($condition['where']) && isset($condition['arg'])) {
                $where = DB::format($condition['where'], $condition['arg']);
            } else {
                $where = DB::implode_field_value($condition, ' AND ');
            }
        } else {
            $where = $condition;
        }
        return $where;
    }
}