<?php

/**
 * 计数器抽象类
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model\Counter;
abstract class Abs extends \Model\Abs {

    
    /**
     * 计数器唯一键字段（一维数组，允许有多个）
     * 
     * @var array
     */
    protected static $_field_keys = ['id'];
    
    /**
     * 计数器值字段
     * 
     * @var string
     */
    protected static $_fiels_value = 'total_number';
    
    
    /**
     * 计数器+x（默认为1）
     * 
     * @param array  $key    键配置
     * @param number $offset 计数器加的数
     * 
     * @return \int
     */
    public static function increment(array $key, $offset = 1) {
        $table = self::db()->showTable();
        $field_value = static::$_fiels_value;
        
        $sql_params = $key;
        $sql_params['offset'] = $offset;
        $sql = "INSERT INTO {$table} SET total_number = 1, ";
        foreach(static::$_field_keys as $field) {
            $sql .= "{$field} = :{$field},";
        }
        $sql = rtrim($sql, ',');
        $sql .= " ON DUPLICATE KEY UPDATE {$field_value} = {$field_value} + :offset";

        $mysql = new \Comm\Db\Mysql();
        return $mysql->exec($sql, $sql_params);
    }
    
    /**
     * 计数器-x（默认为1）
     *
     * @param array  $key    键配置
     * @param number $offset 计数器减的数
     *
     * @return \int
     */
    public static function decrement(array $key, $offset = 1) {
        $table = self::db()->showTable();
        $field_value = static::$_fiels_value;
        
        $sql_params = $key;
        $sql_params['offset'] = $offset;
        $sql = "UPDATE {$table} SET {$field_value} = {$field_value} - :offset WHERE ";
        
        $where = array();
        foreach(static::$_field_keys as $field) {
            $where[] = "{$field} = :{$field}";
        }
        $sql .= implode(' AND ', $where);

        $mysql = new \Comm\Db\Mysql();
        return $mysql->exec($sql, $sql_params);
    }
    
    /**
     * 设置计数数器
     * 
     * @param array $key          键配置
     * @param int   $total_number 计数器值
     * 
     * @return \int|\boolean
     */
    public static function setValue(array $key, $total_number) {
        $has_old_total_number = \is_numeric(self::get($key));
        if(!$has_old_total_number) {
            $data = $key;
            $data[static::$_field_keys] = $total_number;
            $result = self::db()->insert($data, true, true);
        }
        
        if($has_old_total_number || empty($result)) {
            $result = self::db()->wAnd($key)->upadte([static::$_field_keys => $total_number]);
        }
        
        return $result;
    }
    
    /**
     * 获取计数器中的一个值
     * 
     * @param array $key 键配置
     * 
     * @return \int|\false
     */
    public static function getValue(array $key) {
        return self::db()->wAnd($key)->fetchOne(static::$_fiels_value);
    }
    
    
}


