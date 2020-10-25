<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Model;
use app\user\model\UserRole;

class Member extends Model
{
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'registe_time';
    protected $updateTime = 'last_login_time';

    //
    /* 用户模型自动完成 */
    protected $insert = ['login'=> 1,
                           'registe_ip',
                           'last_login_ip'=> 0,
                           'last_login_time'=> 0,
                           'status'=>1,
                           'score1'=> 0,
                           'score2'=> 0,
                           'score3'=> 0,
                           'score4'=> 0,
                           'pos_province'=> 0,
                           'pos_city'=> 0,
                           'pos_district'=> 0,
                           'pos_community'=> 0];

    //字段修改器
    public function setRegIpAttr($value)
    {
        return get_client_ip($value);
    }
    public function getStatusAttr($value)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $status[$value];
    }

    public function getExtAttr($value,$data)
    {
        $user = query_user(array('username','mobile','email'),$data['uid']);
        return $user;
    }

    public function registerMember($nickname = '')
    {
        /* 在当前应用中注册用户 */
        if ($user = $this->create(array('nickname' => $nickname, 'status' => 1))) {
            $uid = $this->save($user);
            if (!$uid) {
                $this->error = lang('_THE_FOREGROUND_USER_REGISTRATION_FAILED_PLEASE_TRY_AGAIN_WITH_EXCLAMATION_');
                return false;
            }
            $this->initFollow($uid);
            return $uid;
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @param bool $remember
     * @param int $role_id 有值代表强制登录这个角色
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid, $remember = false, $role_id = 0)
    {
        /* 检测是否在当前应用注册 */
        $user = $this->get($uid);
        if ($role_id != 0) {
            $user['last_login_role'] = $role_id;
        } else {
            if (!intval($user['last_login_role'])) {
                $user['last_login_role'] = $user['show_role'];
            }
        }
        session('temp_login_uid', $uid);
        session('temp_login_role_id', $user['last_login_role']);

        if ($user->getData('status') == 3 /*判断是否激活*/) {  //取得表中原始数据
            header('Content-Type:application/json; charset=utf-8');
            $data['status'] = 1;
            $data['url'] = url('ucenter/Member/activate');

            if (request()->isAjax()) {
                return $data;
            } else {
                redirect($data['url']);
            }
            return false;
        }

        if (1 != $user->getData('status')) { //取得表中原始数据
            $this->error = lang('_USERS_ARE_NOT_ACTIVATED_OR_DISABLED_WITH_EXCLAMATION_'); //应用级别禁用
            return false;
        }

        $step = db('UserRole')->where(array('uid' => $uid, 'role_id' => $user['last_login_role']))->value('step');
        if (!empty($step) && $step != 'finish') {
            header('Content-Type:application/json; charset=utf-8');
            $data['status'] = 1;
            //执行步骤在start的时候执行下一步，否则执行此步骤
            $go = $step == 'start' ? get_next_step($step) : check_step($step);
            $data['url'] = url('Ucenter/Member/step', array('step' => $go));
            if (request()->isAjax()) {
                return $data;
            } else {
                redirect($data['url']);
            }
            return false;
        }
        /* 登录用户 */
        $this->autoLogin($user, $remember);

        session('temp_login_uid', null);
        session('temp_login_role_id', null);
        //记录行为
     //   action_log('user_login', 'member', $uid, $uid);
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout()
    {
        session('_AUTH_LIST_' . get_uid() . '1', null);
        session('_AUTH_LIST_' . get_uid() . '2', null);
        session('_AUTH_LIST_' . get_uid() . 'in,1,2', null);
        session('user_auth', null);
        session('user_auth_sign', null);
        cookie('OX_LOGGED_USER', NULL);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user, $remember = false, $role_id = 0)
    {

        /* 更新登录信息 */
        $data = array(
            'uid' => $user['uid'],
            'login' => array('exp', '`login`+1'),
            'last_login_time' => $_SERVER['REQUEST_TIME'],
            'last_login_ip' => get_client_ip(1),
            'last_login_role' => $user['last_login_role'],
        );
        $this->update($data);
        //判断角色用户是否审核
        $map['uid'] = $user['uid'];
        $map['role_id'] = $user['last_login_role'];
        $audit = db('UserRole')->where($map)->value('status');
        //判断角色用户是否审核 end
        $umap ['uid'] = $user['uid'];
        $umap ['public_id'] = $user['mp_token'];
        $info = db ( 'MemberPublic' )->where ( $umap )->find ();
        set_mpid($info ['mp_id']);                                               //设置当前下上文mp_id
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid' => $user['uid'],
            'username' => get_nickname($user['uid']),
            'last_login_time' => $user['last_login_time'],
            'role_id' => $user['last_login_role'],
            'audit' => $audit,
            'mp_id'=>$info ['mp_id'],
            'mp_token'=>$info['public_id'],
            'public_name'=>$info['public_name'],
        );
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        if ($remember) {
            $user1 = db('user_token')->where('uid' , $user['uid'])->find();
            $token = $user1['token'];
            if ($user1 == null) {
                $token = build_auth_key();
                $data['token'] = $token;
                $data['time'] = time();
                $data['uid'] = $user['uid'];
                db('user_token')->where('uid' , $user['uid'])->update($data);
            }
        }

        if (!$this->getCookieUid() && $remember) {
            $expire = 3600 * 24 * 7;
            cookie('OX_LOGGED_USER', $this->jiami($this->change() . ".{$user['uid']}.{$token}"), $expire);
        }
    }

    public function need_login()
    {
        if (!is_login()) {
            if ($uid = $this->getCookieUid()) {
                $this->login($uid);
                return true;
            }
        }

    }

    public function getCookieUid()
    {

        static $cookie_uid = null;
        if (isset($cookie_uid) && $cookie_uid !== null) {
            return $cookie_uid;
        }
        $cookie = cookie('OX_LOGGED_USER');
        $cookie = explode(".", $this->jiemi($cookie));
        $key = array_search(max($cookie),$cookie);
        if($key >= 2){
            $map['uid'] = $cookie[1];
            $user = db('user_token')->where($map)->find();
            $cookie_uid = ($cookie[0] != $this->change()) || ($cookie[2] != $user['mp_token']) ? false : $cookie[1];
            $cookie_uid = $user['time'] - time() >= 3600 * 24 * 7 ? false : $cookie_uid;
        } else {
            $cookie_uid = 0;
        }

        return $cookie_uid;
    }


    /**
     * 加密函数
     * @param string $txt 需加密的字符串
     * @param string $key 加密密钥，默认读取SECURE_CODE配置
     * @return string 加密后的字符串
     */
    private function jiami($txt, $key = null)
    {
        empty($key) && $key = $this->change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt [$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return $ch . $tmp;
    }

    /**
     * 解密函数
     * @param string $txt 待解密的字符串
     * @param string $key 解密密钥，默认读取SECURE_CODE配置
     * @return string 解密后的字符串
     */
    private function jiemi($txt, $key = null)
    {
        empty($key) && $key = $this->change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $ch = $txt[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = substr($txt, 1);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) {
                $j += 64;
            }
            $tmp .= $chars[$j];
        }

        return base64_decode($tmp);
    }

    private function change()
    {
        preg_match_all('/\w/', config('DATA_AUTH_KEY'), $sss);
        $str1 = '';
        foreach ($sss[0] as $v) {
            $str1 .= $v;
        }
        return $str1;
    }


    /**
     * 设置角色用户默认基本信息
     * @param $role_id
     * @param $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function initUserRoleInfo($role_id, $uid)
    {
        $roleModel = model('Role');
        $roleConfigModel = model('RoleConfig');
        $authGroupAccessModel = model('AuthGroupAccess');
        model('UserRole')->where(array('role_id' => $role_id, 'uid' => $uid))->setField('init', 1);
        //默认权限组设置
        $role = $roleModel->where(array('id' => $role_id))->find();
        if ($role['user_groups'] != '') {
            $role = explode(',', $role['user_groups']);

            //查询已拥有权限组
            $have_user_group_ids = $authGroupAccessModel->where(array('uid' => $uid))->select();
            $have_user_group_ids = array_column($have_user_group_ids, 'group_id');
            //查询已拥有权限组 end

            $authGroupAccess['uid'] = $uid;
            $authGroupAccess_list = array();
            foreach ($role as $val) {
                if ($val != '' && !in_array($val, $have_user_group_ids)) { //去除已拥有权限组
                    $authGroupAccess['group_id'] = $val;
                    $authGroupAccess_list[] = $authGroupAccess;
                }
            }
            unset($val);
            $authGroupAccessModel->saveAll($authGroupAccess_list);
        }
        //默认权限组设置 end

        $map['role_id'] = $role_id;
        $map['name'] = array('in', array('score', 'rank'));
        $config = $roleConfigModel->where($map)->select();
        $config = array_combine(array_column($config, 'name'), $config);


        //默认积分设置
        if (isset($config['score']['value'])) {
            $value = json_decode($config['score']['value'], true);
            $data = $this->getUserScore($role_id, $uid, $value);
            $user = $this->where(array('uid' => $uid))->find();
            foreach ($data as $key => $val) {
                if ($val > 0) {
                    if (isset($user[$key])) {
                        $this->where(array('uid' => $uid))->setInc($key, $val);
                    } else {
                        $this->where(array('uid' => $uid))->setField($key, $val);
                    }
                }
            }
            unset($val);
        }
        //默认积分设置 end

        //默认头衔设置
        if (isset($config['rank']['value']) && $config['rank']['value'] != '') {
            $ranks = explode(',', $config['rank']['value']);
            if (count($ranks)) {
                //查询已拥有头衔
                $rankUserModel = model('RankUser');
                $have_rank_ids = $rankUserModel->where(array('uid' => $uid))->select();
                $have_rank_ids = array_column($have_rank_ids, 'rank_id');
                //查询已拥有头衔 end

                $reason = json_decode($config['rank']['data'], true);
                $rank_user['uid'] = $uid;
                $rank_user['create_time'] = time();
                $rank_user['status'] = 1;
                $rank_user['is_show'] = 1;
                $rank_user['reason'] = $reason['reason'];
                $rank_user_list = array();
                foreach ($ranks as $val) {
                    if ($val != '' && !in_array($val, $have_rank_ids)) { //去除已拥有头衔
                        $rank_user['rank_id'] = $val;
                        $rank_user_list[] = $rank_user;
                    }
                }
                unset($val);
                $rankUserModel->saveAll($rank_user_list);
            }
        }
        //默认头衔设置 end
    }

    //默认显示哪一个角色的个人主页设置
    public function initDefaultShowRole($role_id, $uid)
    {
        $userRoleModel = model('UserRole');

        $roles = $userRoleModel->where(array('uid' => $uid, 'status' => 1, 'role_id' => array('neq', $role_id)))->select();
        if (!count($roles)) {
            $data['show_role'] = $role_id;
            //执行member表默认值设置
            $this->save($data,['uid' => $uid]);
        }
    }

    //默认显示哪一个角色的个人主页设置 end

    /**
     * 获取用户初始化后积分值
     * @param $role_id 当前初始化角色
     * @param $uid 初始化用户
     * @param $value 初始化角色积分配置值
     * @return array
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function getUserScore($role_id, $uid, $value)
    {
        $roleConfigModel = model('RoleConfig');
        $userRoleModel = model('UserRole');

        $map['role_id'] = array('neq', $role_id);
        $map['uid'] = $uid;
        $map['init'] = 1;
        $role_list = $userRoleModel->where($map)->select();
        $role_ids = array_column($role_list, 'role_id');
        $map_config['role_id'] = array('in', $role_ids);
        $map_config['name'] = 'score';
        $config_list = $roleConfigModel->where($map_config)->field('value')->select();
        $change = array();
        foreach ($config_list as &$val) {
            $val = json_decode($val['value'], true);
        }
        unset($val);
        unset($config_list[0]['score1']);
        foreach ($value as $key => $val) {
            $config_list = list_sort_by($config_list, $key, 'desc');
            if ($val > $config_list[0][$key]) {
                $change[$key] = $val - $config_list[0][$key];
            } else {
                $change[$key] = 0;
            }
        }
        return $change;
    }

    private function initFollow($uid = 0)
    {
        if ($uid != 0) {
            $followModel = model('Common/Follow');
            $follow = modC('NEW_USER_FOLLOW', '', 'USERCONFIG');
            $fans = modC('NEW_USER_FANS', '', 'USERCONFIG');
            $friends = modC('NEW_USER_FRIENDS', '', 'USERCONFIG');
            $allFollow = $follow . "," . $friends;
            $allFans = $fans . "," . $friends;

            if($allFollow != '') {
                $allFollow = explode(',', $allFollow);
                $allFollow = array_unique($allFollow);
                foreach($allFollow as $val) {
                    if(query_user('uid', $val)) {
                        $followModel->addFollow($uid, $val);
                        D('Member')->where(array('uid' => $val))->setInc('fans', 1);
                    }
                }
            }
            if($allFans != '') {
                $allFans = explode(',', $allFans);
                $allFans = array_unique($allFans);
                foreach($allFans as $val) {
                    if(query_user('uid', $val)) {
                        $followModel->addFollow($val, $uid);
                        model('Member')->where(array('uid' => $uid))->setInc('fans', 1);
                    }
                }
            }

        }
        return true;
    }


    /**
     * addSyncData
     * @param $uid
     * @param $info
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function addSyncData($uid, $info)
    {
        //去除特殊字符。
        $data['nickname'] = preg_replace('/[^A-Za-z0-9_\x80-\xff\s\']/', '', $info['nick']);
        // 截取字数
        $data['nickname'] = mb_substr($data['nickname'], 0, 32, 'utf-8');
        // 为空则随机生成
        if (empty($data['nickname'])) {
            $data['nickname'] = $this->rand_nickname();
        } else {
            if ($this->where(array('nickname' => $data['nickname']))->select()) {
                $data['nickname'] .= '_' . $uid;
            }
        }
        $data['sex'] = $info['sex'];
        $data = $this->validate(
            array('signature', '0,100', -1, self::EXISTS_VALIDATE, 'length'),
            /* 验证昵称 */
            array('nickname', 'checkDenyNickname', -31, self::EXISTS_VALIDATE, 'callback'), //昵称禁止注册
            array('nickname', 'checkNickname', -32, self::EXISTS_VALIDATE, 'callback'),
            array('nickname', '', -30, self::EXISTS_VALIDATE, 'unique'))->create($data);
        $data['uid'] = $uid;
        $res = $this->save($data);
        return $res;
    }

    private function rand_nickname()
    {
        $nickname = create_rand(4);
        if ($this->where(array('nickname' => $nickname))->select()) {
            $this->rand_nickname();
        } else {
            return $nickname;
        }
    }

}
