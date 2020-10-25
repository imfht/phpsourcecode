<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@iloucd.com>
 * Date: 2016-12-13 10:37:09
 */

namespace Rbac\Model;

use Lib\DatabaseV5;

class Model  {

    const FORM_TYPE_NUMBER   = 'number';    //MySQL字段对应表单类型：数字类型
    const FORM_TYPE_SELECT   = 'select';    //MySQL字段对应表单类型：枚举型
    const FORM_TYPE_STRING   = 'string';   //MySQL字段对应表单类型：字符类型
    const FORM_TYPE_TEXT     = 'text';   //MySQL字段对应表单类型：文本类型
    const FORM_TYPE_DATE     = 'date';   //MySQL字段对应表单类型：日期类型
    const FORM_TYPE_DEFAULT  = 'unknown';   //MySQL字段对应表单类型：无类型
    const FORM_TYPE_FILE     = 'file';   //MySQL字段对应表单类型：文件类型
    const FORM_TYPE_CHECKBOX = 'checkbox';   //MySQL字段对应表单类型：多选类型

    static    $db_conf       = null;
    protected $db_instance   = null; //数据对象实例
    static    $instance_list = [];
    protected $db_conf_name  = 'cp';
    public    $table_name    = '';

    // 链操作方法列表
    protected $methods = ['strict', 'order', 'alias', 'having', 'group', 'lock', 'distinct', 'auto', 'filter', 'validate', 'result', 'token', 'index', 'force'];

    public function __construct($db_config = []) {
        empty(self::$db_conf) && self::$db_conf = $db_config;
        $this->db_init();
    }

    /**
     * @node_name 初始化数据库对象
     */
    public function db_init() {

        if (!isset(self::$instance_list[$this->db_conf_name])
            || FALSE == (self::$instance_list[$this->db_conf_name] instanceof DatabaseV5)
        ) {
            self::$instance_list[$this->db_conf_name] = new DatabaseV5(self::$db_conf);
        }
        $this->db_instance = self::$instance_list[$this->db_conf_name];

    }

    /**
     * 根据条件查询数据列表
     * @param array  $where
     * @param int    $offset
     * @param int    $limit
     * @param array  $order
     * @param array  $fields
     * @param string $group
     * @return mixed
     */
    public function getListByWhere($where = [], $offset = 0, $limit = 999999, $order = [], $fields = [], $group = '') {
        $sql_where = is_array($where) ? $this->db_instance->parseWhere($where) : $where;
        $offset    = intval($offset);
        $limit     = intval($limit);
        $sql_limit = "{$offset},{$limit}";
        $group     = trim($group);

        if (is_array($order)) {
            $arr_order = [];
            foreach ($order as $key => $val) {
                $arr_order[] = " {$key} {$val} ";
            }
            $sql_order = implode(' , ', $arr_order);
        } else {
            $sql_order = $order;
        }
        if (is_array($fields)) {
            $sql_field = implode(' , ', $fields);
        } else {
            $sql_field = $fields;
        }
        if ($sql_field) {
            if (empty($order)) {
                $list = $this->db_instance->table($this->table_name)->where($sql_where)->limit($sql_limit)->field($sql_field)->group($group)->select();
            } else {
                $list = $this->db_instance->table($this->table_name)->where($sql_where)->limit($sql_limit)->order($sql_order)->field($sql_field)->group($group)->select();
            }
        } else {
            if (empty($order)) {
                $list = $this->db_instance->table($this->table_name)->where($sql_where)->limit($sql_limit)->group($group)->select();
            } else {
                $list = $this->db_instance->table($this->table_name)->where($sql_where)->limit($sql_limit)->order($sql_order)->group($group)->select();
            }
        }

        return $list;
    }


    /**
     * 根据条件查询一条记录
     * @param array  $where
     * @param array  $fields
     * @param string $group
     * @return mixed
     */
    public function getOneByWhere($where = [], $fields = [], $group = '') {
        $sql_where = is_array($where) ? $this->db_instance->parseWhere($where) : $where;
        $group     = trim($group);
        if (is_array($fields)) {
            $sql_field = implode(' , ', $fields);
        } else {
            $sql_field = $fields;
        }
        if ($sql_field) {
            $data = $this->db_instance->table($this->table_name)->where($sql_where)->field($sql_field)->group($group)->fetch();
        } else {
            $data = $this->db_instance->table($this->table_name)->where($sql_where)->group($group)->fetch();
        }

        return $data;
    }

    /**
     * 获取单个字段的值
     * @param array  $where
     * @param string $field
     * @param string $group
     * @return bool
     */
    public function getOneFieldByWhere($where = [], $field = '', $group = '') {
        $res = false;
        if ($field) {
            $info = $this->getOneByWhere($where, '', $group);
            isset($info[$field]) && $res = $info[$field];
        }
        return $res;
    }

    /**
     * 根据条件查询数据列表总数
     * @param array  $where
     * @param string $group
     * @return mixed
     */
    public function getListCountByWhere($where = [], $group = '') {
        $sql_where = is_array($where) ? $this->db_instance->parseWhere($where) : $where;
        $group     = trim($group);
        return $this->db_instance->table($this->table_name)->where($sql_where)->group($group)->count();

    }

    /**
     * 以主键更新数据
     * @param int   $id
     * @param array $data
     * @return bool
     */
    public function updateById($id = 0, $data = []) {
        $res = false;
        if ($id && is_array($data) && !empty($data)) {
            if (in_array('id', array_keys($data))) {
                unset($data['id']);
            }
            $res = $this->db_instance->table($this->table_name)->where(" id = {$id} ")->update($data);
        }
        return $res;
    }

    /**
     * 根据where条件更新数据
     * @param array $where
     * @param array $data
     * @return bool
     */
    public function updateByWhere($where = [], $data = []) {
        $res = false;
        if (is_array($data) && !empty($data)) {
            $sql_where = is_array($where) ? $this->db_instance->parseWhere($where) : $where;
            if (in_array('id', array_keys($data))) {
                unset($data['id']);
            }
            $res = $this->db_instance->table($this->table_name)->where($sql_where)->update($data);
        }
        return $res;
    }

    public function deleteByWhere($where = []) {
        $sql_where = is_array($where) ? $this->db_instance->parseWhere($where) : $where;
        $res       = $this->db_instance->table($this->table_name)->where($sql_where)->delete();
        return $res;
    }

    /**
     * 复杂更新，直接指向SQL语句
     * @param string $update_sql
     * @param array  $where
     * @return bool
     */
    public function updateFromSqlByWhere($update_sql = '', $where = []) {
        $ret       = false;
        $sql_where = is_array($where) ? $this->db_instance->parseWhere($where) : $where;
        if ($sql_where && $update_sql) {
            $query_sql = "{$update_sql} WHERE {$sql_where}";
            $res       = $this->db_instance->query($query_sql);
            $res !== false && $ret = true;
        }
        return $ret;
    }

    /**
     * 根据条件查询数据列表
     * @param array $where
     * @param array $fields
     * @return mixed
     */
    public function find($where = [], $fields = []) {
        $sql_where = is_array($where) ? $this->db_instance->parseWhere($where) : $where;
        if (is_array($fields) && !empty($fields)) {
            $sql_field = implode(' , ', $fields);
        } else {
            $sql_field = $fields ? $fields : '*';
        }
        return $this->db_instance->table($this->table_name)->where($sql_where)->field($sql_field)->fetch();

    }

    public function add($data = []) {
        $res = false;
        if (is_array($data) && !empty($data)) {
            $res = $this->db_instance->table($this->table_name)->insert($data);
        }
        return $res;
    }

    /**
     * @node_name 批量新增
     * @param array $data
     * @return bool
     */
    public function batchAdd($data = []) {
        $res = false;
        if (is_array($data) && !empty($data)) {
            $res = $this->db_instance->table($this->table_name)->batchInsert($data);
        }
        return $res;
    }

    public function selectBySql($sql = '') {
        return $this->db_instance->table($this->table_name)->select($sql);
    }

    public function getLastSql() {
        return $this->db_instance->table($this->table_name)->getLastSql();
    }

    public function getTableFields($table_name = ''){
        empty($table_name) && $table_name = $this->table_name;
        $sql = " SHOW FULL COLUMNS FROM {$table_name} ";
        try{
            $list = $this->selectBySql($sql);
            $fields = [];
            if(!empty($list)){
                foreach($list as $field){
                    $field = array_change_key_case($field, CASE_LOWER);
                    $mysql_field = $field['type'];
                    $pattern = '/\([^()]*\)/';
                    $field['type'] = preg_replace($pattern, '', $field['type']);
                    $field['form_type'] = $this->getFormType($field['type']);
                    $matches = $new_values =  [];
                    if(self::FORM_TYPE_SELECT == $field['form_type']){
                        preg_match($pattern, $mysql_field, $matches);

                        if (false !== stripos($field['type'], 'enum')){

                            $default_value = explode(',', str_replace(['(', ')', '\''], '', array_pop($matches)));
                            foreach($default_value as $key => $val){
                                if(!empty($val)){
                                    array_push($new_values, "{$val}|{$val}");
                                }
                            }
                            $field['default_value'] = implode(',', $new_values);
                        }else{
                            $comment_string = str_replace(['：', '-', '，'], [':', '|', ','], $field['comment']);
                            $comment_arr = explode(':', $comment_string);
                            $field['comment'] = $comment_arr[0] ;
                            $field['default_value'] = count($comment_arr) > 1 ? $comment_arr[1] : '';
                        }

                    }

                    array_push($fields, $field);
                }
            }
            return $fields;
        }catch(Exception $e){
            return [];
        }

    }

    /**
     * @node_name 查询字段类型对应的表单类型
     * @param string $field_type
     * @return string
     */
    private function getFormType($field_type = '') {

        if (false !== stripos($field_type, 'tinyint')
        ) {
            return self::FORM_TYPE_SELECT;
        }

        if (false !== stripos($field_type, 'int')
            || false !== stripos($field_type, 'float')
            || false !== stripos($field_type, 'double')
            || false !== stripos($field_type, 'decimal')
        ) {
            return self::FORM_TYPE_NUMBER;
        }

        if (false !== stripos($field_type, 'char')
            || false !== stripos($field_type, 'tinytext')
            || false !== stripos($field_type, 'mediumblob')
            || false !== stripos($field_type, 'mediumtext')
        ) {
            return self::FORM_TYPE_STRING;
        }

        if (false !== stripos($field_type, 'blob')
            || false !== stripos($field_type, 'text')
            || false !== stripos($field_type, 'binary')
        ) {
            return self::FORM_TYPE_TEXT;
        }

        if (false !== stripos($field_type, 'date')
            || false !== stripos($field_type, 'time')
            || false !== stripos($field_type, 'year')
        ) {
            return self::FORM_TYPE_DATE;
        }

        return self::FORM_TYPE_DEFAULT;

    }


    /**
     * @node_name 验证数据库中是否有此表
     * @param string $db_name
     * @return array|bool
     */
    public function existsTableNameByDB($db_name ) {
        empty($table_name) && $table_name = $this->table_name;
        if(!empty($db_name)){
            $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema='{$db_name}' AND table_type='base table' ";
            try {

                $list   = $this->selectBySql($sql);
                $tables = [];
                if (!empty($list)) {
                    foreach ($list as $vo) {
                        $tables[] = $vo['table_name'];
                    }

                    return $tables;
                } else {
                    return false;
                }

            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @node_name 开启事务
     */
    public function beginTransaction(){
        if (isset($this->db_instance) && ($this->db_instance instanceof DatabaseV5)){
            $this->db_instance->beginTransaction();
        }
    }

    /**
     * @node_name 回滚事务
     */
    public function rollBack(){
        if (isset($this->db_instance) && ($this->db_instance instanceof DatabaseV5)){
            $this->db_instance->rollBack();
        }

    }

    /**
     * @node_name 提交事务
     */
    public function commit (){
        if (isset($this->db_instance) && ($this->db_instance instanceof DatabaseV5)){
            $this->db_instance->commit();
        }
    }
}