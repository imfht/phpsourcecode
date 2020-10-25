<?php

/**
 * 文章计数器
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model\Counter;
class Article extends Abs {
    
    /**
     * 要操作的数据表名
     * 
     * @var string
     */
    protected static $_table = 'counter_article'; 
    
    
    /**
     * 计数器唯一键字段
     *
     * @var array
     */
    protected static $_field_keys = ['uid', 'category_id'];
    
    /**
     * 计数器+x（默认为1）
     * 
     * @param int    $category_id 分类ID
     * @param int    $uid         用户UID（不传则为当前登录用户）
     * @param number $offset      计数器加的数
     * 
     * @return \int
     */
    public static function incr($category_id, $uid = null, $offset = 1) {
        return parent::increment(self::_showKey($uid, $category_id), $offset);
    }
    
    /**
     * 计数器-x（默认为1）
     *
     * @param int    $category_id 分类ID
     * @param int    $uid         用户UID（不传则为当前登录用户）
     * @param number $offset      计数器减的数
     *
     * @return \int
     */
    public static function decr($category_id, $uid = null, $offset = 1) {
        return parent::decrement(self::_showKey($uid, $category_id), $offset);
    }
    
    /**
     * 设置计数数器
     * 
     * @param int   $category_id 分类ID
     * @param int   $uid         用户UID（不传则为当前登录用户）
     * @param int   $total_number 计数器值
     * 
     * @return \int|\boolean
     */
    public static function set($category_id, $uid = null,  $total_number) {
        return parent::setValue(self::_showKey($uid, $category_id), $total_number);
    }
    
    /**
     * 获取计数器中的一个值
     * 
     * @param int    $category_id 分类ID
     * @param int    $uid         用户UID（不传则为当前登录用户）
     * 
     * @return \int|\false
     */
    public static function get($category_id, $uid = null) {
        $key = self::_showKey($uid, $category_id);
        return self::db()->wAnd($key)->fetchOne(static::$_fiels_value);
    }
    

    /**
     * 获取计数器唯一字段
     *
     * @param int    $category_id 分类ID
     * @param int    $uid         用户UID（不传则为当前登录用户）
     *
     * @return \array
     */
    protected static function _showKey($uid, $category_id) {
        $uid || $uid = \Yaf_Registry::get('current_uid');
        return ['uid' => $uid, 'category_id' => $category_id];
    }
    
} 
