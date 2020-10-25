<?php
/**
 * @require : none
 * @author : yu@wenlong.org
 * @date : 2015-09-02 15:53:51
 * @description : 模块基类
 */
namespace Model;

if(!defined('DIR')){
	exit('Please correct access URL.');
}
 
abstract class Model{
    public $getLastSql;

    protected $db;

    private $_where_str;
    private $_select_fields;
    private $_limit;
    private $_order_by;
    private $_group_by;
    private $_set;

    public function init($db){
        $this->db = $db;
    }

    protected function where($key, $value = ''){
        if(empty($this->_where_str)){
            $this->_where_str = '';
        }

        if(is_array($key) && !isset($value)){
            foreach($key as $kk => $vv){
                $kk_arr = explode(' ', $kk);
                if(count($kk_arr) == 1){
                    $this->_where_str .= ' AND `'.trim($key).'` = \''.addslashes(trim($vv)).'\'';
                }else {
                    $this->_where_str .= ' AND `'.trim($kk_arr[0]).'` '.trim($kk_arr[1]).' \''.addslashes(trim($vv)).'\'';
                }
            }
        }

        if(!is_array($key) && isset($value)){ 
            $kk_arr = explode(' ', $key);
            if(count($kk_arr) == 1){
                $this->_where_str .= ' AND `'.trim($key).'` = \''.addslashes(trim($value)).'\'';
            }else {
                $this->_where_str .= ' AND `'.$kk_arr[0].'` '.$kk_arr[1].' \''.addslashes(trim($value)).'\'';
            }
        }

        return $this;
    }
    
    protected function or_where($key, $value = ''){
        if(empty($this->_where_str)){
            $this->_where_str = '';
        }

        if(is_array($key) && empty($value)){
            foreach($key as $kk => $vv){
                $kk_arr = explode(' ', $kk);
                if(count($kk_arr) == 1){
                    $this->_where_str .= ' OR  `'.trim($key).'` = \''.addslashes(trim($vv)).'\'';
                }else {
                    $this->_where_str .= ' OR  `'.trim($kk_arr[0]).'` '.trim($kk_arr[1]).' \''.addslashes(trim($vv)).'\'';
                }
            }
        }

        if(!is_array($key) && !empty($value)){ 
            $kk_arr = explode(' ', $key);
            if(count($kk_arr) == 1){
                $this->_where_str .= ' OR  `'.trim($key).'` = \''.addslashes(trim($value)).'\'';
            }else {
                $this->_where_str .= ' OR  `'.$kk_arr[0].'` '.$kk_arr[1].' \''.addslashes(trim($value)).'\'';
            }
        }

        return $this;
    }

    protected function select($fields = ''){
        if(!empty($fields)){
            $fields_arr = explode(',', $fields);
            foreach($fields_arr as $fv){
                $this->_select_fields .= trim($fv).', ';
            }
        }

        $this->_select_fields = mb_substr($this->_select_fields, 0, -2);
        return $this;
    }

    protected function limit($m, $n = null){
        $limit = trim($m).', ';

        if(!empty($n)){
            $limit .= trim($n).', ';
        }

        $this->_limit = ' LIMIT '.mb_substr($limit, 0, -2);
        return $this;
    }

    protected function order_by($key, $sort = 'ASC'){
        if(empty($this->_order_by)){
            $this->_order_by = ' ORDER BY ';
        }
        $this->_order_by .= '`'.trim($key).'` '.strtoupper(trim($sort)).', ';
        return $this;
    }

    protected function group_by($key){
        if(empty($this->_group_by)){
            $this->_group_by = ' GROUP BY ';
        }
        $this->_group_by .= '`'.trim($key).'`, ';
        return $this;
    }

    protected function set($key, $value){
        if(empty($key)){
            return $this;
        }

        if(empty($this->_set)){
            $this->_set = ' SET ';
        }

        $this->_set .= '`'.$key.'` = \''.addslashes($value).'\', ';
        return $this;
    }

    /**
     * $return_way 返回方式 result_array | row_array
     */
    protected function get($table_name, $return_way = 'result_array'){
        if(empty($table_name)){
            return false;
        }

        if(empty($this->_select_fields)){
            $this->_select_fields = '*';
        }

        if(!empty($this->_where_str)){
            $this->_where_str = ' WHERE '.mb_substr($this->_where_str, 4);
        }

        if(!empty($this->_order_by)){
            $this->_order_by = mb_substr($this->_order_by, 0, -2);
        }

        if(!empty($this->_group_by)){
            $this->_group_by = mb_substr($this->_group_by, 0, -2);
        }

        $sql = 'SELECT '.$this->_select_fields.' FROM `'.$table_name.'`'.$this->_where_str.$this->_group_by.$this->_order_by.$this->_limit;
        $this->getLastSql = $sql;

        if($return_way == 'result_array'){
            $res = $this->db->fetchAll($sql);
        }else if($return_way == 'row_array'){
            $res = $this->db->fetchFirst($sql);
        }

        $this->_clear_variable();

        return $res;
    }

    function get_insert_id(){
        return $this->db->insert_id();
    }

    protected function update($table_name){
        if(empty($table_name)){
            return false;
        }

        if(!empty($this->_where_str)){
            $this->_where_str = ' WHERE '.mb_substr($this->_where_str, 4);
        }

        if(!empty($this->_set)){
            $this->_set = mb_substr($this->_set, 0, -2);
        }

        $sql = 'UPDATE `'.$table_name.'`'.$this->_set.$this->_where_str;
        $this->getLastSql = $sql;
        $res = $this->db->query($sql);

        $this->_clear_variable();
        return $res;
    }

    private function _clear_variable(){
        $this->_select_fields = '';
        $this->_where_str = '';
        $this->_order_by = '';
        $this->_group_by = '';
        $this->_limit = '';
        $this->_set = '';
    }
}
