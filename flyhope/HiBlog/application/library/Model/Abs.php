<?php

/**
 * Model抽象类
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model;
abstract class Abs {
    
    /**
     * 继承后设置表名可直接通过self::db获取数据库操作对象
     * 
     * @var string
     */
    protected static $_table = '';
    
    
    /**
     * 主键ID
     * 
     * @var string
     */
    protected static $_primary_key = 'id';
    
    /**
     * 获取数据库操作对象
     * 
     * @return \Comm\Db\Simple
     */
    static public function db() {
        return new \Comm\Db\Simple(static::$_table);
    }
    
    /**
     * 禁止实例化该类，只能是静态调用
     */
    protected function __construct() {
    
    }
    
    /**
     * 禁止克隆
     * 
     * @return boolean
     */
    protected function __clone() {
        return false;
    }
    
    /**
     * 根据主键ID获取一条或多条数据（多条传入一维数组）
     *
     * @param mixed $id 主键ID
     *
     * @return \array
     */
    static public function show($id) {
        $key = sprintf('%s_%s', static::$_table, $id);
        $result = \Comm\Sdata::getValue($key, function() use ($id) {
            $where = array(static::$_primary_key => $id);
            $result = self::db()->wAnd($where)->fetchRow();
            if(!empty($result['metadata'])) {
                $result['metadata'] = \json_decode($result['metadata'], true);
            }
            return $result;
        });

        return $result;
    }
    
    /**
     * 根据主键ID删除一条或多条数据（多条传入一维数组）
     * 
     * @param mixed $id
     * 
     * @return int
     */
    static public function destory($id) {
        $where = array(static::$_primary_key => $id);
        return self::db()->wAnd($where)->delete(true);
    }
    
}
