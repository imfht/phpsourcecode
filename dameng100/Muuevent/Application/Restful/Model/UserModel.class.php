<?php
/**
 * @author 大蒙<59262424@qq.com>
 */

namespace Restful\Model;


use Think\Model;

class UserModel extends Model{
    protected $tableName = 'member'; 
    /**
     * 用户授权Token登录验证
     * @return [type] [description]
     */
    public function _checkToken($token){
        //验证用户授权TOKEN
        $uid = $this->getTokenUid($token); //根据token获取uid

        if ($uid || 0 < $uid) { //UC登陆成功
            //判断是否已经登陆
            if(is_login()==$uid){
                return $uid;
            }else{
                /* 登陆用户 */
                $rs = $this->login($uid,1); //登陆
                if ($rs) { //登陆用户
                     return $uid;
                } else {
                     return false;
                }
            }
        }
        return false;
    }

    /**
     * 通过token获取uid
     * 在memberModel模型移植过来，原是cookie机制
     * @param  string $token 通过登陆获取到的token
     * @return int 用户id     
     */
    public function getTokenUid($token)
    {
        //if(is_login()){
        //    return is_login();
        //}
        if(!$token){
            return false;
        }
        $token = explode("|", think_decrypt($token));
        //dump($token);exit;
        $map['uid'] = $token[0];
        $user = D('user_token')->where($map)->find();
        $token_uid = ($token[1] != $user['token']) ? false : $token[0];

        $token_uid = $user['time'] - time() >= 3600 * 24 * 7 ? false : $token_uid;//过期时间7天
        return $token_uid;
    }

    /**
     * 根据UID获取用户授权Token
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getToken($uid){
        $map['uid'] = $uid;
        $open_id = M('user_token')->field('token')->where($map)->find();
        $token = think_encrypt($uid.'|'.$open_id['token']);//加密token,每次使用token验证都需要解密操作

        return $token;
    }

    /**
     * 移植过来的方法 根据UID登录指定用户
     * @param  integer $uid 用户ID
     * @param bool $remember
     * @param int $role_id 有值代表强制登录这个角色
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid, $remember = 1, $role_id = 0){
        /* 检测是否在当前应用注册 */
        $user = M('member')->find($uid);
        if ($role_id != 0) {
            $user['last_login_role'] = $role_id;
        } else {
            if (!intval($user['last_login_role'])) {
                $user['last_login_role'] = $user['show_role'];
            }
        }
        session('temp_login_uid', $uid);
        session('temp_login_role_id', $user['last_login_role']);
        //用户未激活返回激活页面
        if ($user['status'] == 3 /*判断是否激活*/) {
            
            $data['status'] = 1;
            $data['url'] = U('Ucenter/Member/activate');

            $this->response($data,'json');exit;
        }
        //用户状态被禁用或删除直接返回错误
        if (1 != $user['status']) {
            return false;
        }
        //用户注册步骤设置，移动端跳过步骤设置，直接设置完成（暂定）
        $userRoleModel = D('UserRole');
        $step = $userRoleModel->where(array('uid' => $uid, 'role_id' => $user['last_login_role']))->getField('step');
        if (!empty($step) && $step != 'finish') {
            $userRoleModel->where($map)->setField('step', 'finish');
        }

        /* 登录用户 */
        $this->autoLogin($user, $remember, $role_id);

        session('temp_login_uid', null);
        session('temp_login_role_id', null);
        //记录行为
        action_log('user_login', 'member', $uid, $uid);
        return true;
    }

    /**
     * 在通用MemberModel移植过来，去掉cookie部分自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user, $remember = false, $role_id = 0)
    {

        /* 更新登录信息 */
        $data = array(
            'uid' => $user['uid'],
            'login' => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
            'last_login_role' => $user['last_login_role'],
        );
        $this->save($data);
        //判断角色用户是否审核
        $map['uid'] = $user['uid'];
        $map['role_id'] = $user['last_login_role'];
        $audit = D('UserRole')->where($map)->getField('status');
        //判断角色用户是否审核 end

        /* 记录登录SESSION */
        $auth = array(
            'uid' => $user['uid'],
            'username' => get_username($user['uid']),
            'last_login_time' => $user['last_login_time'],
            'role_id' => $user['last_login_role'],
            'audit' => $audit,
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        if ($remember) {
            $user1 = D('user_token')->where('uid=' . $user['uid'])->find();
            $token = $user1['token'];
            if ($user1 == null) {
                $token = build_auth_key();
                $data['token'] = $token;
                $data['time'] = time();
                $data['uid'] = $user['uid'];
                D('user_token')->add($data);
            }
        }
    }

}


