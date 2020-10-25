<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

use think\Model;
use app\user\model\UserRole;
/**
 * 会员模型
 * @author Patrick <contact@uctoo.com>
 */
class Member extends Model{

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = Member::get($uid);

        if(!is_object($user) || 1 != $user['status']) {
            $this->error = lang('_USERS_DO_NOT_EXIST_OR_HAVE_BEEN_DISABLED_WITH_EXCLAMATION_'); //应用级别禁用
            return false;
        }



        //记录行为
        //action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($uid);
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($uid){
        $user = Member::get($uid);
        /* 更新登录信息 */
      //  $user -> login = $user['login']+1;
        //  $user->last_login_time = $_SERVER['REQUEST_TIME'];
        // $user->last_login_ip = get_client_ip(1);
        // $user->last_login_role = $user['last_login_role'];
        //  $this->save();

        //判断角色用户是否审核
        $map['uid'] = $user['uid'];
        $map['role_id'] = $user['last_login_role'];
        $audit = new UserRole();
        $auditData = $audit->where($map)->value('status');
        //判断角色用户是否审核 end

        $umap ['uid'] = $user['uid'];
        $umap ['public_id'] = $user['mp_token'];
        $info = model( 'MemberPublic' )->where ( $umap )->find ();
        set_mpid($info ['mp_id']);
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => $user['nickname'],
            'last_login_time' => $user['last_login_time'],
            'role_id' => $user['last_login_role'],
            'audit' => $auditData,
            'mp_id'=>$info ['mp_id'],
            'mp_token'=>$info['public_id'],
            'public_name'=>$info['public_name'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }

}
