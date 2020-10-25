<?php

/**
 * 数据配置读取类
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model;
class Config extends Abs {
    /**
     * 数据表名
     *
     * @var string
     */
    protected static $_table = 'config';
    
    
    /**
     * 获取所有配置
     * 
     * @return \Comm\array
     */
    static public function showAll() {
        $data = self::db()->fetchAll();
        $result = \Comm\Arr::hashmap($data, 'k', 'v');
        return $result;
    }
    
    /**
     * 获取某一个配置
     * 
     * @param string $k
     * 
     * @return string
     */
    static public function show($k) {
        $data = self::db()->wAnd(['k'=>$k])->fetchRow();
        return isset($data['v']) ? $data['v'] : false;
    }
    
    /**
     * 批量获取属性
     * 
     * @param array $keys
     * @return \Comm\array
     */
    static public function showBatch(array $keys) {
        $data = self::db()->wAnd(['k'=>$keys])->fetchAll();
        $result = \Comm\Arr::hashmap($data, 'k', 'v');
        return $result;
    }
}

