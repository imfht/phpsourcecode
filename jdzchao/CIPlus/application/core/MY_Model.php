<?php defined('BASEPATH') or exit ('No direct script access allowed');

abstract class MY_Model extends CI_Model {

    public function __construct($database = 'default') {
        parent::__construct();
        $this->db = $this->load->database($database, TRUE);
    }

    /**
     * 替换数据
     * @param $table
     * @param array $dataArr
     * @return mixed
     */
    protected function replace($table, array $dataArr) {
        $this->cache_clear();
        return $this->db->replace($table, $dataArr);
    }

    /**
     * 添加数据
     * @param $table
     * @param array $dataArr
     * @return mixed 插入的id
     */
    protected function insert($table, array $dataArr) {
        $this->cache_clear();
        $this->db->insert($table, $dataArr);
        return $this->db->insert_id();
    }

    /**
     * 修改数据
     * @param $table
     * @param $dataArr
     * @param $whereArr
     * @return mixed 影响的行数
     */
    protected function update($table, $dataArr, $whereArr = array()) {
        $this->cache_clear();
        $this->db->where($whereArr);
        $this->db->update($table, $dataArr);
        return $this->db->affected_rows();
    }

    /**
     * 删除数据
     * @param $table
     * @param $whereArr
     * @return mixed 影响的行数
     */
    protected function delete($table, $whereArr = array()) {
        $this->cache_clear();
        $this->db->where($whereArr);
        $this->db->delete($table);
        return $this->db->affected_rows();
    }

    /**
     * 查询并返回一条数据
     * @param $table
     * @param array $whereArr
     * @return mixed 数据数组
     */
    protected function row($table, $whereArr = array()) {
        $this->db->where($whereArr);
        $query = $this->db->get($table);
        return $query->row_array();
    }

    /**
     * 查询并返回多条数据
     * @param $table
     * @param int $page
     * @param int $num
     * @param array $whereArr
     * @return mixed
     */
    protected function result($table, $page = 1, $num = 10, $whereArr = array()) {
        $this->db->where($whereArr);
        if ($page <= 0) $page = 1;
        $offset = ($page - 1) * $num;
        $query = $this->db->get($table, $num, $offset);
        return $query->result_array();
    }

    /**
     * 查询并返回全部数据
     * @param $table
     * @param array $whereArr
     * @return mixed
     */
    protected function result_all($table, $whereArr = array()) {
        $this->db->where($whereArr);
        $query = $this->db->get($table);
        return $query->result_array();
    }

    /**
     * 查询数据总数
     * @param $table
     * @param array $whereArr
     * @return mixed
     */
    protected function count($table, $whereArr = array()) {
        $this->db->where($whereArr);
        return $this->db->count_all_results($table);
    }

    /**
     * 清除数据缓存
     */
    public function cache_clear() {
        $this->db->cache_delete();
    }

    /**
     * 重置查询条件缓存
     */
    public function query_reset() {
        $this->db->reset_query();
        $this->db->flush_cache();
    }

}
