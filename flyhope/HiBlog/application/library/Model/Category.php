<?php

/**
 * 分类管理
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model;
class Category extends Abs {
    
    /**
     * 分类最多个数
     * 
     * @var int
     */
    const COUNT_MAX = 100;
    
    /**
     * 数据表名
     * 
     * @var string
     */
    protected static $_table = 'category';
    
    /**
     * 根据UID和分类别名获取一条数据
     * 
     * @param int    $uid   
     * @param string $alias
     * 
     * @return array
     */
    static public function showByUidAlias($uid, $alias) {
        return self::db()->wAnd(['uid'=>$uid, 'alias'=>$alias])->fetchRow();
    }
    
    /**
     * 根据UID和分类名获取一条数据
     *
     * @param int    $uid
     * @param string $name
     *
     * @return array
     */
    static public function showByUidName($uid, $name) {
        return self::db()->wAnd(['uid'=>$uid, 'name'=>$name])->fetchRow();
    }
    
    /**
     * 统计一个用户的分类数量
     * 
     * @param int $uid
     * 
     * @return int
     */
    static public function countByUid($uid) {
        return self::db()->wAnd(['uid'=>$uid])->fetchOne('COUNT(*)');
    }


    /**
     * 获取一个用户的所有分类
     *
     * @param int    $uid            用户UID，不传则获取当前用户数据
     * @param int    $append_default 是否自动追加默认的项
     * @param string $use_master     是否强制使用主库（默认否）
     *
     * @return array
     */
    static public function showUserAll($uid = false, $append_default = true, $use_master = false) {
        $uid || $uid = \Yaf_Registry::get('current_uid');
        
        $uid_params = ($append_default ?  [$uid, 0] : $uid);
        
        $where = ['uid'=>$uid_params];
        $order = [['sort', SORT_ASC], ['id', SORT_ASC]];
        $db = self::db()->wAnd($where)->order($order);
        $result = $db->fetchAll('*', $use_master);
        return $result;
    }
    
    /**
     * 创建分类
     * 
     * @param int    $uid
     * @param string $name
     * @param string $alias
     * @param int    $uid
     * 
     * @return \int
     */
    static public function create($name, $alias, $uid = false) {
        $uid === false && $uid = \Yaf_Registry::get('current_uid');
        if(!$uid) {
            throw new \Exception\Msg('系统异常，无法获取分类创建人');
        }
        
        if(!$name || !$alias) {
            throw new \Exception\Msg('分类名称和别名必填');
        }
        
        if(self::countByUid($uid) >= self::COUNT_MAX) {
            throw new \Exception\Msg('超出最大分类个数限制');
        }
        
        $data = self::showByUidAlias($uid, $alias);
        if($data) {
            throw new \Exception\Msg('分类别名已存在');
        }
        
        $data = self::showByUidName($uid, $name);
        if($data) {
            throw new \Exception\Msg('分类名称已存在');
        }        
        
        $data = array(
            'uid'       => $uid,
            'name'      => $name,
            'alias'     => $alias,
            'sort'      => '120',
        );
        
        $db = self::db();
        $db->insert($data, true);
        $id = $db->lastId();
        
        if(!$id) {
            throw new \Exception\Msg('创建分类失败');
        }
        
        Publish::sidebar(true);
        
        return $id;
    }
    
    /**
     * 更新一条数据
     * 
     * @param array $data
     * @param array $new_data
     * @param int   $validate_auth
     * 
     * @throws \Exception\Program
     * @return \mixed
     */
    static public function update(array $data, array $new_data, $validate_auth = true) {
        if(!$data || empty($data['uid']) || empty($data['id'])) {
            throw new \Exception\Program('分类原始数据异常');
        }
        $validate_auth && User::validateAuth($data['uid']);
        $result = self::db()->wAnd(['id'=>$data['id']])->upadte($new_data, true);
        Publish::sidebar(true);
        return $result;
    }

    /**
     * 更新排序
     * 
     * @param array $data 排好序的分类ID（一维数组）
     * 
     * @param string $uid
     * @param string $validate_auth
     * 
     * @return int 修改的数据条数
     */
    static public function updateSort(array $data, $uid = false, $validate_auth = true) {
        $uid === false && $uid = \Yaf_Registry::get('current_uid');
        $validate_auth && User::validateAuth($uid);
        
        $db = self::db();
        $sort = 0;
        foreach($data as $id) {
            $db->wAnd(['id'=>$id, 'uid'=>$uid])->upadte(['sort'=>++$sort]);
            $db->clean();
        }
        
        //发布至Github
        Publish::sidebar(true);
        
        return $sort;
    }
    
    
    /**
     * 根据主键ID删除用户的一个或多个分类
     *
     * @param mixed  $id   ID或ID集
     * @param string $uid  用户UID
     *
     * @return \int
     */
    static public function destroyByUserBatch($ids, $uid = false) {
        $uid || $uid = \Yaf_Registry::get('current_uid');
    
        $where = array(static::$_primary_key => $ids, 'uid' => $uid);
        $result = self::db()->wAnd($where)->delete(true);
        
        Publish::sidebar(true);
        
        return $result;
    }
 
} 