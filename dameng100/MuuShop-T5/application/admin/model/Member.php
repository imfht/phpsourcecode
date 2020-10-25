<?php
namespace app\admin\model;

use think\Model;

/**
 * 用户模型
 */

class Member extends Model {


    public function lists($status = 1, $order = 'uid DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
        if(!$user || 1 != $user['status']) {
            $this->error = lang('_USERS_DO_NOT_EXIST_OR_HAVE_BEEN_DISABLED_WITH_EXCLAMATION_'); //应用级别禁用
            return false;
        }

        //记录行为
        action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($user);
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
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user['uid'],
            'last_login_time' => time(),
            'last_login_ip'   => request()->ip(1),
        );
        $this->save($data,['uid'=>$user['uid']]);
        $this->where(['uid'=>$user['uid']])->setInc('login');
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => $user['nickname'],
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }

    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->value('nickname');
    }

    public function nicknameLength($nickname)
    {
        if(mb_strlen($nickname,'utf-8')<modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG')||mb_strlen($nickname,'utf-8')<modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')){
            $this->error=lang('_NICKNAME_LENGTH_MUST_BE_IN_').modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').lang('_POSITION_WITH_EXCLAMATION_');
        }
        return true;
    }

}
