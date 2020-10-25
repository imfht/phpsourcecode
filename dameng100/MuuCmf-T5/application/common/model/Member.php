<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 用户基础模型
 */
class Member extends Model
{
    /* 用户模型自动完成 */
    protected $insert = [
        'login'              => 0,
        'reg_ip',
        'reg_time',
        'last_login_ip'      => 0,
        'last_login_time'    => 0,
        'status'             => 1,
        'score1'             => 0,
        'score2'             => 0,
        'score3'             => 0,
        'score4'             => 0,
        'pos_province'       => 0,
        'pos_city'           => 0,
        'pos_district'       => 0,
        'pos_community'      => 0,
    ];
    //修改器
    protected function setRegIpAttr()
    {
        return request()->ip(1);
    }
    protected function setRegTimeAttr()
    {
        return time();
    }
    protected $rule = [
    	'nickname' => ['require','max'=>30,'checkNickname'=>''],
    ];
        /*
        array('signature', '0,100', -1, self::EXISTS_VALIDATE, 'length'),
        array('nickname', 'checkNickname', -33, self::EXISTS_VALIDATE, 'callback'), //昵称长度不合法
        array('nickname', 'checkDenyNickname', -31, self::EXISTS_VALIDATE, 'callback'), //昵称禁止注册
        array('nickname', 'checkNickname', -32, self::EXISTS_VALIDATE, 'callback'),
        array('nickname', '', -30, self::EXISTS_VALIDATE, 'unique'), //昵称被占用
       */


    /**
     * 检测用户名是不是被禁止注册
     * @param  string $nickname 昵称
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyNickname($nickname,$rule)
    {
        $denyName = model("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->getField('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($nickname, $val))) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function checkNickname($value,$rule)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($value, ' ') !== false) {
            return false;
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $value, $result);

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 验证昵称长度
     * @param $nickname
     * @return bool
     */
    protected function checkNicknameLength($nickname,$rule)
    {
        $length = mb_strlen($nickname, 'utf-8'); // 当前数据长度
        if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            return false;
        }
        return true;
    }
    /**
     * 注册用户资料
     * @param  string $nickname [description]
     * @return [type]           [description]
     */
    public function registerMember($nickname = '')
    {
        /* 在当前应用中注册用户 */
        $user = [
            'nickname' => $nickname,
            'status' => 1
        ];
        $this->nickname = $nickname;
        $this->status   = 1;
        if ($res = $this->save()) {
            // $this->uid;主键ID;
            if (!$res) {
                $this->error = lang('_THE_FOREGROUND_USER_REGISTRATION_FAILED_PLEASE_TRY_AGAIN_WITH_EXCLAMATION_');
                return false;
            }
            $res_follow = $this->initFollow($this->uid);
            return $this->uid;
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
        $user = $this->find($uid);

        if ($role_id != 0) {
            $user['last_login_role'] = $role_id;
        } else {
            if (!intval($user['last_login_role'])) {
                $user['last_login_role'] = $user['show_role'];
            }
        }
        if ($user['status'] == 3 /*判断是否激活*/) {
            header('Content-Type:application/json; charset=utf-8');
            $data['status'] = 1;
            $data['url'] = Url('ucenter/Member/activate');

            if (request()->isAjax()) {
                exit(json_encode($data));
            } else {
                redirect($data['url']);
            }
        }

        if (1 != $user['status']) {
            $this->error = lang('_USERS_ARE_NOT_ACTIVATED_OR_DISABLED_WITH_EXCLAMATION_'); //应用级别禁用
            return false;
        }
        
        /* 登录用户 */
        $this->autoLogin($user, $remember);
        //记录行为
        model('common/action')->action_log('user_login', 'member', $uid, $uid);
        //挂载登录成功后钩子
        hook('login_after',['uid'=>$uid]);
        return true;
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
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(1),
            'last_login_role' => $user['last_login_role'],
        );
        $this->save($data,['uid'=>$user['uid']]);
        $this->where(['uid'=>$user['uid']])->setInc('login');
        //判断角色用户是否审核
        $map['uid'] = $user['uid'];
        $map['role_id'] = $user['last_login_role'];
        $audit = Db::name('UserRole')->where($map)->value('status');
        
        //判断角色用户是否审核 end

        /* 记录登录SESSION和COOKIES */
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
            $user1 = Db::name('user_token')->where(['uid'=>$user['uid']])->find();
            $token = $user1['token'];
            if ($user1 == null) {
                $token = build_auth_key();
                $data_token['token'] = $token;
                $data_token['time'] = time();
                $data_token['uid'] = $user['uid'];
                Db::name('user_token')->insert($data_token);
            }
        }

        if (!$this->getCookieUid() && $remember) {
            $expire = 3600 * 24 * 7;
            cookie('MUU_LOGGED_USER', $this->jiami($this->change() . ".{$user['uid']}.{$token}"), $expire);
        }
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout()
    {
        session('_AUTH_LIST_' . get_uid() . '1', null);
        session('_AUTH_LIST_' . get_uid() . '2', null);
        session('user_auth', null);
        session('user_auth_sign', null);
        cookie('MUU_LOGGED_USER', NULL);
    }

    /**
     * 通用用户授权判断
     * 增加微信网页判断
     * 依赖模块 Weixin基础模块（不安装跳过微信网页授权判断）
     * @return [type] [description]
     */
    public function need_login()
    {   
        if (!is_login()) {
            $this->rembember_login();
            //判断是否开启微信网页授权
            return false;
        }else{
            return is_login();
        }
    }
    /**
     * 记住登陆状态
     * @return [type] [description]
     */
    public function rembember_login(){
        if(!is_login()){
            //判断COOKIE
            if ($uid = $this->getCookieUid()>0) {
                $this->login($uid);
                return $uid;
            }
        }
    }

    public function getCookieUid()
    {
        static $cookie_uid = null;
        if (isset($cookie_uid) && $cookie_uid !== null) {
            return $cookie_uid;
        }else{
        	$cookie = cookie('MUU_LOGGED_USER');
        	if($cookie){
        		$cookie = explode(".", $this->jiemi($cookie));
	        	$map['uid'] = $cookie[1];
		        $user = Db::name('user_token')->where($map)->find();
		        $cookie_uid = ($cookie[0] != $this->change()) || ($cookie[2] != $user['token']) ? false : $cookie[1];
		        $cookie_uid = $user['time'] - time() >= 3600 * 24 * 7 ? false : $cookie_uid;//过期时间7天
        	}
        	
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
        preg_match_all('/\w/', Config('DATA_AUTH_KEY'), $sss);
        $str1 = '';
        foreach ($sss[0] as $v) {
            $str1 .= $v;
        }
        return $str1;
    }

    /**
     * 初始化角色用户信息
     * @param $role_id
     * @param $uid
     * @return bool
     */
    public  function initRoleUser($role_id = 0, $uid)
    {
        $role = Db::name('role')->where(['id' => $role_id])->find();
        $user_role = [
            'uid' => $uid,
            'role_id' => $role_id,
            'step' => "start"
        ];

        if ($role['audit']) { //该角色需要审核
            $user_role['status'] = 2; //未审核
        } else {
            $user_role['status'] = 1;
        }
        $result = Db::name('UserRole')->insert($user_role);
        if (!$role['audit']) {
            //该角色不需要审核
            $this->initUserRoleInfo($role_id, $uid);
        }
        $this->initDefaultShowRole($role_id, $uid);

        return $result;
    }
    /**
     * 设置角色用户默认基本信息
     * @param $role_id
     * @param $uid
     */
    public function initUserRoleInfo($role_id, $uid)
    {
        //默认用户组设置
        $role = Db::name('Role')->where(['id' => $role_id])->find();

        if ($role['user_groups'] != '') {
            $auth_groups_ids = explode(',', $role['user_groups']);

            //查询已拥有用户组
            $have_user_group_ids = Db::name('AuthGroupAccess')->where(['uid' => $uid])->select();
            $have_user_group_ids = array_column($have_user_group_ids, 'group_id');
            //查询已拥有用户组 end

            $authGroupAccess['uid'] = $uid;
            $authGroupAccess_list = [];
            foreach ($auth_groups_ids as $val) {
                if ($val != '' && !in_array($val, $have_user_group_ids)) { //去除已拥有用户组
                    $authGroupAccess['group_id'] = $val;
                    $authGroupAccess_list[] = $authGroupAccess;
                }
            }
            unset($val);
            Db::name('AuthGroupAccess')->insertAll($authGroupAccess_list);
        }
        //默认用户组设置 end

        $map['role_id'] = $role_id;
        $map['name'] = ['in', ['score', 'rank']];
        $config = Db::name('RoleConfig')->where($map)->select();
        $config = array_combine(array_column($config, 'name'), $config);

        
        //默认积分设置
        if (isset($config['score']['value'])) {
            $value = json_decode($config['score']['value'], true);

            $user = $this->where(['uid' => $uid])->find();
            foreach ($value as $key => $val) {
                if ($val > 0) {
                    if (isset($user[$key])) {
                        $this->where(['uid' => $uid])->setInc($key, $val);
                    } else {
                        $this->where(['uid' => $uid])->setField($key, $val);
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
                $have_rank_ids = Db::name('RankUser')->where(['uid' => $uid])->select();
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
                Db::name('RankUser')->insertAll($rank_user_list);
            }
        }
        //默认头衔设置 end
        //初始化状态更新
        Db::name('UserRole')->where(['role_id' => $role_id, 'uid' => $uid])->setField('init', 1);
    }

    /**
     * 默认显示哪一个角色的个人主页设置
     * @param  [type] $role_id [description]
     * @param  [type] $uid     [description]
     * @return [type]          [description]
     */
    public function initDefaultShowRole($role_id, $uid)
    {
        $data['show_role'] = $role_id;
        //执行member表默认值设置
        $this->where(['uid' => $uid])->update($data);

    }

    /**
     * 根据用户ID获取用户名
     * @param  integer $uid 用户ID
     * @return string       用户名
     */
    function get_username($uid = 0)
    {
        static $list;
        if (!($uid && is_numeric($uid))) { //获取当前登录用户名 
            $user_auth = session('user_auth');
            return $user_auth['username'];
        }

        /* 获取缓存数据 */
        if (empty($list)) {
            $list = cache('sys_active_user_list');
        }
        /* 查找用户信息 */
        $key = "u{$uid}";
        if (isset($list[$key])) { //已缓存，直接使用
            $name = $list[$key];
        } else { //调用接口获取用户信息
            $User = model('ucenter/UcenterMember');
            $info = $User->info($uid);

            if ($info && isset($info[1])) {
                $name = $list[$key] = $info[1];

                $count = count($list);
                $max = Config('USER_MAX_CACHE');
                while ($count-- > $max) {
                    array_shift($list);
                }
                cache('sys_active_user_list', $list);
            } else {
                $name = '';
            }

        }
        return $name;
    }

    /**
     * 根据用户ID获取用户昵称
     * @param  integer $uid 用户ID
     * @return string       用户昵称
     */
    function get_nickname($uid = 0)
    {
        if (!($uid && is_numeric($uid))) { //获取当前登录用户名
            return session('user_auth.nickname');
        }
        
        //调用接口获取用户信息
        $info = db('Member')->where('uid','=',$uid)->value('nickname');

        return $info;
    }
    
    /**
     * 初始关注用户
     * @param  integer $uid [description]
     * @return [type]       [description]
     */
    private function initFollow($uid = 0)
    {
        if ($uid != 0) {
            $followModel = model('common/Follow');
            $follow = modC('NEW_USER_FOLLOW', '', 'USERCONFIG');
            $fans = modC('NEW_USER_FANS', '', 'USERCONFIG');
            $friends = modC('NEW_USER_FRIENDS', '', 'USERCONFIG');
            if ($follow != '') {
                $follow = explode(',', $follow);
                foreach ($follow as $val) {
                    if (query_user('uid', $val)) {
                        $followModel->addFollow($uid, $val);
                    }
                }
                unset($val);
            }
            if ($fans != '') {
                $fans = explode(',', $fans);
                foreach ($fans as $val) {
                    if (query_user('uid', $val)) {
                        $followModel->addFollow($val, $uid);
                    }
                }
                unset($val);
            }
            if ($friends != '') {
                $friends = explode(',', $friends);
                foreach ($friends as $val) {
                    if (query_user('uid', $val)) {
                        $followModel->addFollow($val, $uid);
                        $followModel->addFollow($uid, $val);
                    }
                }
                unset($val);
            }
        }
        return true;
    }


    /**
     * addSyncData
     * @param $uid
     * @param $info
     * @return mixed
     * @author:大蒙 59262424@qq.com
     */
    public function addSyncData($uid, $info)
    {
        //去除特殊字符。
        $data['nickname'] = preg_replace('/[^A-Za-z0-9_\x80-\xff\s\']/', '', $info['nickname']);
        // 截取字数
        $data['nickname'] = mb_substr($data['nickname'], 0, 32, 'utf-8');
        // 为空则随机生成
        if (empty($data['nickname'])) {
            $data['nickname'] = rand_nickname();
        } else {
            if ($this->where(['nickname' => $data['nickname']])->count()) {
                $data['nickname'] .= '_' . $uid;
            }
        }
        $data['uid'] = $uid;
        $res = $this->save($data);
        return $res;
    }
    
    /**
     * 获取用户注册及更改信息的错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    public function showRegError($code = 0)
    {
        switch ($code) {
            case -1:
                $error = lang('_USER_NAME_MUST_BE_IN_LENGTH_').modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').lang('_BETWEEN_CHARACTERS_WITH_EXCLAMATION_'); //用户名长度不符
                break;
            case -2:
                $error = lang('_ERROR_USERNAME_FORBIDDEN_').lang('_EXCLAMATION_'); //用户名被禁止注册
                break;
            case -3:
                $error = lang('_ERROR_USERNAME_USED_').lang('_EXCLAMATION_'); //用户名被占用
                break;
            case -4:
                $error = lang('_ERROR_LENGTH_PASSWORD_').lang('_EXCLAMATION_');//密码长度必须在6-30个字符之间
                break;
            case -5:
                $error = lang('_ERROR_EMAIL_FORMAT_2_').lang('_EXCLAMATION_');//邮箱格式不正确
                break;
            case -6:
                $error = lang('_ERROR_EMAIL_LENGTH_').lang('_EXCLAMATION_');//邮箱长度不符
                break;
            case -7:
                $error = lang('_ERROR_EMAIL_FORBIDDEN_').lang('_EXCLAMATION_');//邮箱被禁止注册
                break;
            case -8:
                $error = lang('_ERROR_EMAIL_USED_2_').lang('_EXCLAMATION_');//邮箱被占用
                break;
            case -9:
                $error = lang('_ERROR_PHONE_FORMAT_2_').lang('_EXCLAMATION_');//手机格式不正确
                break;
            case -10:
                $error = lang('_ERROR_FORBIDDEN_').lang('_EXCLAMATION_');//手机被禁止注册
                break;
            case -11:
                $error = lang('_ERROR_PHONE_USED_').lang('_EXCLAMATION_');//手机号被占用
                break;
            case -12:
                $error = lang('_ERROR_USERNAME_FORMAT_').lang('_EXCLAMATION_');//用户名格式错误
                break;
            case -20:
                $error = lang('_ERROR_USERNAME_FORM_').lang('_EXCLAMATION_');//用户名只能由数字、字母和"_"组成
                break;
            case -30:
                $error = lang('_ERROR_NICKNAME_USED_').lang('_EXCLAMATION_');//昵称被占用
                break;
            case -31:
                $error = lang('_ERROR_NICKNAME_FORBIDDEN_2_').lang('_EXCLAMATION_');//昵称被禁止注册
                break;
            case -32:
                $error =lang('_ERROR_NICKNAME_FORM_').lang('_EXCLAMATION_');//昵称只能由数字、字母、汉字和"_"组成
                break;
            case -33:
                $error = lang('_ERROR_LENGTH_NICKNAME_1_').modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').lang('_ERROR_LENGTH_2_').lang('_EXCLAMATION_');//昵称长度必须在x-x 个字符之间
                break;
            case -40:
                $error = lang('_OLD_PW_').lang('_ERROR_');//原密码验证错误
                break;
            case -41:
                $error = lang('_ERROR_CONFIRM_PASSWORD_');//确认密码不能为空
                break;
            case -42:
                $error = lang('_ERROR_LENGTH_NEW_PASSWORD_');//确认密码不能为空
                break;
            case -43:
                $error = lang('_ERROR_NOT_SAME_PASSWORD_');//新密码与确认密码不一致
                break;
            default:
                $error = lang('_ERROR_UNKNOWN_');
        }
        return $error;
    }


}
