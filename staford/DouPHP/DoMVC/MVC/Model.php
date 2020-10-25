<?php
/**
 * 模型类文件
 * @abstract 一表一模型，一表一对象，操作无非增删改查。
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Model
{
    private static $DBH; //存储数据库对象
    private $table; //存储表名称

    public function __construct($table)
    {
        if (!empty($table)) {
            $table = trim($table);
            $this->table = $table;
            return true;
        } else {
            return false;
        }
    }

    public static function init($db_type, $db_host, $db_user, $db_pass, $db_name, $db_charset =
        'utf8', $db_pconnect = true)
    {
        $db = new Database($db_type, $db_host, $db_user, $db_pass, $db_name, $db_charset,
            $db_pconnect);
        if (is_object($db)) {
            self::$DBH = $db;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @abstract 增
     * @param array $key_val 插入的数据按 "字段名"=>"值" 的形式组合数组
     */
    public function insert($key_val = array())
    {
        return self::$DBH->insert($this->table, $key_val);
    }

    /**
     * @abstract 删
     * @param array $key_val 删除数据所需的条件，按 "字段名"=>"值" 的形式组合数组
     */
    public function delete($key_val = array())
    {
        return self::$DBH->delete($this->table, $key_val);
    }

    /**
     * @abstract 改
     * @param array $key_val 新的数据按 "字段名"=>"值" 的形式组合数组
     * @param array $conditions 更新数据需要的条件，按 "字段名"=>"值" 的形式组合数组
     */
    public function update($key_val = array(), $conditions = array())
    {
        return self::$DBH->update($this->table, $key_val, $conditions);
    }

    /**
     * @abstract 查单条，一维数组
     */
    public function fetch($fields = '', $conditions = array(), $mode = PDO::
        FETCH_ASSOC)
    {
        if (empty($fields)) {
            $fields = '*'; //如果目标字段为空，默认获取全部字段
        }
        if (!empty($condition)) {
            $condition_tmp = array();
            foreach ($condition as $k => $v) {
                $condition_tmp[] = "{$k}='{$v}'";
            }
            $condition_tmp = ' where ' . implode(',', $condition_tmp);
        } else {
            $condition_tmp = '';
        }

        $sql = "select {$fields} from " . $this->table . $condition_tmp;
        return self::$DBH->getOneRecord($sql, $mode);
    }

    /**
     * @abstract 查全部，多维数组
     */
    public function fetchAll($fields = '', $conditions = array(), $mode = PDO::
        FETCH_ASSOC)
    {
        if (empty($fields)) {
            $fields = '*'; //如果目标字段为空，默认获取全部字段
        }
        if (!empty($condition)) {
            $condition_tmp = array();
            foreach ($condition as $k => $v) {
                $condition_tmp[] = "{$k}='{$v}'";
            }
            $condition_tmp = ' where ' . implode(',', $condition_tmp);
        } else {
            $condition_tmp = '';
        }

        $sql = "select {$fields} from " . $this->table . $condition_tmp;
        return self::$DBH->getAllRecord($sql, $mode);
    }
}


?>