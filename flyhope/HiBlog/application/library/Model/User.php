<?php

/**
 * 用户Model
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model;
class User extends Abs {
    
    /**
     * 用户数据表名
     * 
     * @var string
     */
    protected static $_table = 'user';

    /**
     * 根据用户UID获取一条数据
     * 
     * @param int $id 用户UID，不传则为当前登录用户
     * 
     * @return \array
     */
    static public function show($id = false) {
        $id || $id = \Yaf_Registry::get('current_uid');
        $sdata_key = sprintf('user_%s', $id);
        return \Comm\Sdata::getValue($sdata_key, function() use ($id) {
            return parent::show($id);
        });
    }
    
    /**
     * 创建一个用户
     * 
     * @param int    $id                  UID
     * @param string $github_access_token Github的AccessToken
     * @param array  $metadata            元数据
     * 
     * @return int
     */
    static public function create($id, $github_access_token, array $metadata = array()) {
        $metadata || $metadata = new \stdClass();
        $data = array(
            'id' => $id,
            'github_access_token' => $github_access_token,
            'metadata'            => \Comm\Json::encode($metadata),
            'create_time'         => date('Y-m-d H:i:s'),
        );
        self::db()->insert($data, true);
        return $id;
    }
    
    /**
     * 更新一个用户信息
     * 
     * @param  int   $id   UID
     * @param  array $data 数据
     * 
     * @return \int
     */
    static public function update($id, array $data) {
        return self::db()->wAnd(['id'=>$id])->upadte($data, true);
    }
    
    /**
     * 验证权限
     * 
     * @param int $validate_uid 要检查的UID
     * @param int $current_uid  当前用户UID
     * 
     * @throws \Exception\Msg
     */
    static public function validateAuth($validate_uid, $current_uid = false) {
        $current_uid || $current_uid = \Yaf_Registry::get('current_uid');
        if(!$validate_uid || $validate_uid != $current_uid) {
            throw new \Exception\Msg('权限不足', 100403);
        }
    }
    
    /**
     * 判断用户是否登录
     * @throws \Exception\Nologin
     * 
     * @return \int
     */
    static public function validateLogin() {
        $uid = \Yaf_Registry::get('current_uid');
        if(!$uid) {
            throw new \Exception\Nologin();
        }
        return $uid;
    }
    
    /**
     * 更新最后一次登录状态
     * 
     * @param int    $uid                 GITHUB的UID
     * @param string $github_access_token GITHUB的AccessToken
     * @param array  $metadata            同步更新元数据
     * 
     * @return int
     */
    static public function updateLogin($uid, $github_access_token, array $metadata = null) {
        $result = false;
        if($github_access_token || $metadata) {
            $update_data = array('login_time' => date('Y-m-d H:i:s'));
            if($github_access_token) {
                $update_data['github_access_token'] = $github_access_token;
            }
            if($metadata) {
                $update_data['metadata'] = \Comm\Json::encode($metadata);
            }
            
            $user = self::show($uid);
            if(!$user) {
                $result = self::create($uid, $github_access_token, $metadata);
            }
            if($user || empty($result)) {
                $result = self::update($uid, $update_data);
            }
        }
        return $result;
    }    
    
    
}
