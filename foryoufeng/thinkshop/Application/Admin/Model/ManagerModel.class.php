<?php
/**
 * Created by PhpStorm.
 * User: wuqiang
 * Date: 2015/11/24
 * Time: 22:15
 */
namespace Admin\Model;
use Common\Model\UserModel;
class ManagerModel extends UserModel{
    /**
     * 后台管理员登录
     * @param $username 用户名
     * @param $password 密码
     * @param $map   查找条件
     * @return bool
     */
    public function login($username, $password, $map){
        //去除前后空格
        $username = trim($username);
        //匹配登录方式
        $map['name'] = array('eq', $username); //用户名登陆
        $map['is_lock'] = array('neq', 1);
        $user = $this->where($map)->find(); //查找用户
        if(!$user){
            $this->error = '1';//用户不存在或被禁用
        }else{
            if(user_md5($password) !== $user['password']){
                $this->error = '2';//密码错误
            }else{
                //更新登录信息
                $data = array(
                    'id'              => $user['id'],
                    'last_login' => NOW_TIME,
                    'last_ip'   => get_client_ip(),
                );
                $this->save($data);
                $this->autoLogin($user);
                return $user['id'];
            }
        }
        return false;
    }
}