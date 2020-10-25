<?php

/**
 * 博客配置
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model;
class Blog extends Abs {

    /**
     * 主键ID
     *
     * @var string
     */
    protected static $_primary_key = 'uid';
    
    /**
     * 数据表
     *
     * @var string
     */
    protected static $_table = 'blog';
    
    /**
     * 写入一个博客数据
     * 
     * @param int   $uid  用户UID
     * @param array $data 博客配置
     * 
     * @return \int
     */
    static public function create($uid, array $data) {
        $data = self::encodeData($data);
        return self::db()->insert(['uid' => $uid, 'data' => $data, 'update_time' => date('Y-m-d H:i:s')], true, true);
    }
    
    
    /**
     * 更新一条数据
     * 
     * @param int   $uid  用户UID
     * @param array $data 博客配置
     * 
     * @return \int
     */
    static public function update($uid, array $data) {
        $data = self::encodeData($data);
        return self::db()->wAnd(['uid' => $uid])->upadte(['data' => $data, 'update_time' => date('Y-m-d H:i:s')], true);
    }
    
    /**
     * 保存数据
     * 
     * @param array $data 博客配置
     * @param int   $uid  用户UID（不传则使用当前登录用户UID）
     * 
     * @return \int
     */
    static public function save($data, $uid = false) {
    	$uid || $uid = \Yaf_Registry::get('current_uid');
    	if(!$uid) {
    	    throw new \Exception\Nologin();
    	}
    	
        $db_data = self::show($uid);
        if(!$db_data) {
            $result = self::create($uid, $data);
        } else {
            $data = array_merge($db_data['data'], $data);
            $result = self::update($uid, $data);
        }
        return $result;
    }
    
    /**
     * 获取一条数据
     * 
     * @param int   $uid  用户UID，默认为当前登录用户UID
     * 
     * @return array
     */
    static public function show($uid = false) {
        !$uid && $uid = \Yaf_Registry::get('current_uid');
        $result = parent::show($uid);
        isset($result['data']) && $result['data'] = self::decodeData($result['data']);
        return $result;
    }
    
    /**
     * 编码数据
     * 
     * @param array $data
     * 
     * @return string
     */
    static public function encodeData(array $data) {
        empty($data) && $data = new \stdClass();
        return \Comm\Json::encode($data);
    }
    
    /**
     * 解码数据
     * 
     * @param string $data
     * 
     * @return \array
     */
    static public function decodeData($data) {
        if($data) {
            $result = \json_decode($data, true);
        } else {
            $result = array();
        }
        return $result;
    }
    
    
    
} 