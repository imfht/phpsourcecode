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

/**
 * 微信用户模型，也就是公众号粉丝
 */
class Ucuser extends Model
{
    protected $pk = 'mid';    //定义主键
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'reg_time';
    protected $updateTime = 'last_login_time';

    /* 用户模型自动完成 */
    protected $auto = [];
    protected $insert = ['login'=> 0,
        'sex'=> 0,
        'mp_id',
        'reg_ip',
        'last_login_ip'=> 0,
        'status'=>0,
        'score1'=> 0,
        'score2'=> 0,
        'score3'=> 0,
        'score4'=> 0,
        'province'=> 0,
        'city'=> 0,
        'country'=> 0,
        'language'=> 0];
    protected $update = [];

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
    public function setMpIdAttr($value)
    {
        return get_mpid();
    }
    public function getSex($key = null){
        $array = array(0 => '未知', 1 => '男性', 2 => '女性');
        return !isset($key)?$array:$array[$key];
    }

    /**
     * 通过手机号码获取粉丝信息，不区分归属公众号
     * @param  string $mobile 手机
     * @return mixed 粉丝信息
     */
    public function getUcuserByMobile($mobile){
        $map['mobile'] = $mobile;

        /* 获取粉丝数据 */
        $user = $this->where($map)->select();
        return $user;
    }

    /**
     * 检测用户名是不是被禁止注册
     * @param  string $nickname 昵称
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyNickname($nickname)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    protected function checkNickname($nickname)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($nickname, ' ') !== false) {
            return false;
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname, $result);

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 初始化一个新用户
     * @param  string $mp_id 公众号mp_id
     * @param  string $openid 用户openid
     * @return integer          注册成功-用户mid，注册失败-错误编号
     */
    public function registerUser($mp_id = '',$openid = '')
    {
        /* 在当前应用中注册用户 */
        if ($user = $this->create(array('mp_id' => $mp_id,'openid' => $openid, 'status' => 1))) {
            $ucmid = $user->id;
            if (!$ucmid) {
                $this->error = '微会员信息注册失败，请重试！';
                return false;
            }
            sync_wxuser($mp_id,$openid);                                //初始化用户后同步一次用户资料
            return $ucmid;
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }

    /**
     * 根据粉丝mid更新关联的uid，电话号码，密码
     * @param  integer $mid 粉丝ID
     * @param  string $uid member表的uid
     * @param  string $password 用户密码
     * @param  string $mobile 用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($mid,$uid,$password, $mobile)
    {
            $data['mid'] = $mid;
            $data['uid'] = $uid;                            //将UCenterMember表的id写入ucuser表mid字段
            $data['mobile'] = $mobile;
            $data['password'] = think_ucenter_md5($password, UC_AUTH_KEY);
            $res = $this->save($data);
            return $res;
    }

    /**
     * 登录指定用户
     * @param  integer $mid 粉丝ID
     * @param  string  $mobile 用户名
     * @param  string  $password 用户密码
     * @param bool $remember
     * @param int $role_id 有值代表强制登录这个角色
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($mid,$mobile = '', $password = '', $remember = false,$role_id=0)
    {
        /* 检测是否在当前应用注册 */
        $map['mid'] = $mid;
        $map['mobile'] = $mobile;

        /* 获取用户数据 */
        $user = $this->where($map)->find();

        if($role_id!=0){
            $user['last_login_role']=$role_id;
        }else{
            if(!intval($user['last_login_role'])){
                $user['last_login_role']=$user['show_role'];
            }
        }

      //  $return = check_action_limit('input_password','ucuser',$user['mid'],$user['mid']);
      //  if($return && !$return['state']){
      //      return $return['info'];
      //  }

        if (is_array($user) && $user['status']) {
            /* 验证用户密码 */
            if (think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
                $this->updateLogin($user['mid']); //更新用户登录信息
                return $user['mid']; //登录成功，返回粉丝ID
            } else {

                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }

        //以下程序运行不到

    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout($mid = 0)
    {
        session('user_auth', null);
        session('user_auth_sign', null);
        cookie('UCTOO_LOGGED_USER', NULL);
        $data = array(
            'mid' => $mid,
            'login' => 0,                                              //登录状态设置为0
        );
        $this->save($data);
    }

    /**
     * 更新用户登录信息
     * @param  integer $mid 粉丝ID
     */
    protected function updateLogin($mid)
    {
        $data = array(
            'mid' => $mid,
            'last_login_time' => $_SERVER['REQUEST_TIME'],
            'last_login_ip' => get_client_ip(1),
            'login' => 1,                                              //登录状态设置为1
        );
        $this->save($data);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user, $remember = false,$role_id=0)
    {

        /* 更新登录信息 */
        $data = array(
            'mid' => $user['mid'],
            'last_login_time' => $_SERVER['REQUEST_TIME'],
            'last_login_ip' => get_client_ip(1),
            'last_login_role'=>$user['last_login_role'],
        );
        $this->save($data);
        //判断角色用户是否审核
        $map['mid']=$user['mid'];
        $map['role_id']=$user['last_login_role'];
        $audit=model('UserRole')->where($map)->value('status');
        //判断角色用户是否审核 end

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'mid' => $user['mid'],
            'last_login_time' => $user['last_login_time'],
            'role_id'=>$user['last_login_role'],
            'audit'=>$audit,
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        if ($remember) {
            $user1 = model('ucuser_token')->where('mid=' . $user['mid'])->find();
            $token = $user1['token'];
            if ($user1 == null) {
                $token = build_auth_key();
                $data['token'] = $token;
                $data['time'] = time();
                $data['mid'] = $user['mid'];
                model('ucuser_token')->save($data);
            }
        }

        if (!$this->getCookieUid() && $remember) {
            $expire = 3600 * 24 * 7;
            cookie('UCTOO_LOGGED_USER', $this->jiami($this->change() . ".{$user['mid']}.{$token}"), $expire);
        }
    }

    public function need_login()
    {
        if ($mid = $this->getCookieUid()) {
            $this->login($mid);
            return true;
        }
    }

    public function getCookieUid()
    {

        static $cookie_uid = null;
        if (isset($cookie_uid) && $cookie_uid !== null) {
            return $cookie_uid;
        }
        $cookie = cookie('UCTOO_LOGGED_USER');
        $cookie = explode(".", $this->jiemi($cookie));
        $map['mid'] = $cookie[1];
        $user = model('ucuser_token')->where($map)->find();
        $cookie_uid = ($cookie[0] != $this->change()) || ($cookie[2] != $user['mp_token']) ? false : $cookie[1];
        $cookie_uid = $user['time'] - time() >= 3600 * 24 * 7 ? false : $cookie_uid;
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
     * 同步登陆时添加粉丝信息，这个方法一般不在PC端使用，PC端不知道粉丝归属的公众号，且粉丝访问公众号时会自动生成粉丝数据
     * @param $mid
     * @param $info
     * @return mixed
     * autor:uctoo
     */
    public function addSyncUcuser($uid, $info)
    {
        $data1 = $info;
        $data1['nickname'] = mb_substr($info['nickname'], 0, 11, 'utf-8');
        //去除特殊字符。
        $data1['nickname'] = preg_replace('/[^A-Za-z0-9_\x80-\xff\s\']/', '', $data1['nickname']);
        empty($data1['nickname']) && $data1['nickname'] = $this->rand_nickname();
        $data1['sex'] = $info['sex'];
        $data = $this->create($data1);
        $data['uid'] = $uid;
        $res = $this->save($data);
        return $res;
    }

    public function rand_nickname()
    {
        $nickname = $this->create_rand(4);
        if ($this->where(array('nickname' => $nickname))->select()) {
            $this->rand_nickname();
        } else {
            return $nickname;
        }
    }

    function create_rand($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * 设置角色用户默认基本信息,160721还未支持微信端粉丝角色设置
     * @param $role_id
     * @param $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function initUserRoleInfo($role_id,$uid){
        $roleModel=model('Role');
        $roleConfigModel=model('RoleConfig');
        $authGroupAccessModel=model('AuthGroupAccess');
        model('UserRole')->where(array('role_id'=>$role_id,'uid'=>$uid))->setField('init',1);
        //默认用户组设置
        $role=$roleModel->where(array('id'=>$role_id))->find();
        if($role['user_groups']!=''){
            $role=explode(',',$role['user_groups']);

            //查询已拥有用户组
            $have_user_group_ids=$authGroupAccessModel->where(array('uid'=>$uid))->select();
            $have_user_group_ids=array_column($have_user_group_ids,'group_id');
            //查询已拥有用户组 end

            $authGroupAccess['uid']=$uid;
            $authGroupAccess_list=array();
            foreach($role as $val){
                if($val!=''&&!in_array($val,$have_user_group_ids)){//去除已拥有用户组
                    $authGroupAccess['group_id']=$val;
                    $authGroupAccess_list[]=$authGroupAccess;
                }
            }
            unset($val);
            $authGroupAccessModel->saveAll($authGroupAccess_list);
        }
        //默认用户组设置 end

        $map['role_id']=$role_id;
        $map['name']=array('in',array('score','rank'));
        $config=$roleConfigModel->where($map)->select();
        $config=array_combine(array_column($config,'name'),$config);

        //默认积分设置
        if(isset($config['score']['value'])){
            $value=json_decode($config['score']['value'],true);
            $data=$this->getUserScore($role_id,$uid,$value);
            $user=$this->where(array('uid'=>$uid))->find();
            foreach($data as $key=>$val){
                if($val>0){
                    if(isset($user[$key])){
                        $this->where(array('uid'=>$uid))->setInc($key,$val);
                    }else{
                        $this->where(array('uid'=>$uid))->setField($key,$val);
                    }
                }
            }
            unset($val);
        }
        //默认积分设置 end

        //默认头衔设置
        if(isset($config['rank']['value'])&&$config['rank']['value']!=''){
            $ranks=explode(',',$config['rank']['value']);
            if(count($ranks)){
                //查询已拥有头衔
                $rankUserModel=model('RankUser');
                $have_rank_ids=$rankUserModel->where(array('uid'=>$uid))->select();
                $have_rank_ids=array_column($have_rank_ids,'rank_id');
                //查询已拥有头衔 end

                $reason=json_decode($config['rank']['data'],true);
                $rank_user['uid']=$uid;
                $rank_user['create_time']=time();
                $rank_user['status']=1;
                $rank_user['is_show']=1;
                $rank_user['reason']=$reason['reason'];
                $rank_user_list=array();
                foreach($ranks as $val){
                    if($val!=''&&!in_array($val,$have_rank_ids)){//去除已拥有头衔
                        $rank_user['rank_id']=$val;
                        $rank_user_list[]=$rank_user;
                    }
                }
                unset($val);
                $rankUserModel->saveAll($rank_user_list);
            }
        }
        //默认头衔设置 end
    }

    //默认显示哪一个角色的个人主页设置,160721还未支持微信端粉丝主页设置
    public function initDefaultShowRole($role_id,$uid)
    {
        $userRoleModel=model('UserRole');

        $roles=$userRoleModel->where(array('uid'=>$uid,'status'=>1,'role_id'=>array('neq',$role_id)))->select();
        if(!count($roles)){
            $data['show_role']=$role_id;
            //执行member表默认值设置
            $this->where(array('uid'=>$uid))->update($data);
        }
    }
    //默认显示哪一个角色的个人主页设置 end

    /**
     * 获取用户初始化后积分值,160721还未支持微信端粉丝积分
     * @param $role_id 当前初始化角色
     * @param $uid 初始化用户
     * @param $value 初始化角色积分配置值
     * @return array
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function getUserScore($role_id,$uid,$value)
    {
        $roleConfigModel=model('RoleConfig');
        $userRoleModel=model('UserRole');

        $map['role_id']=array('neq',$role_id);
        $map['uid']=$uid;
        $map['init']=1;
        $role_list=$userRoleModel->where($map)->select();
        $role_ids=array_column($role_list,'role_id');
        $map_config['role_id']=array('in',$role_ids);
        $map_config['name']='score';
        $config_list=$roleConfigModel->where($map_config)->field('value')->select();
        $change=array();
        foreach($config_list as &$val){
            $val=json_decode($val['value'],true);
        }
        unset($val);
        unset($config_list[0]['score1']);
        foreach($value as $key=>$val){
            $config_list=list_sort_by($config_list,$key,'desc');
            if($val>$config_list[0][$key]){
                $change[$key]=$val-$config_list[0][$key];
            }else{
                $change[$key]=0;
            }
        }
        return $change;
    }

	/*
	 * 获取 带有tag_id的 筛选数组
	 */
	public function get_tag_id_map($tag_id)
	{

		if(is_array($tag_id))
		{
			foreach($tag_id as $id)
			{
				$ret[] =  array($this->get_tag_id_map($id));
			}
//			$ret[] = 'and';
		}else
		{
			$ret[] = array(
				array('like','['.$tag_id.']'),
				array('like','%,'.$tag_id.']'),
				array('like','['.$tag_id.',%'),
				array('like','%,'.$tag_id.',%'),
				'or'
			);
		}
		if(((is_numeric($tag_id) && !($tag_id==1 ))
			|| (is_array($tag_id) && !in_array(1,$tag_id))))
		{
			$ret[] = array(
				array('notlike','[1]'),
				array('notlike','%,1]'),
				array('notlike','[1,%'),
				array('notlike','%,1,%'),
				'and'
			);
		}
		return $ret;
	}

	public function delete_tag_id($ucuser_info,$tag_id)
	{
		if(!empty($ucuser_info['tagid_list'])
		&& ($ucuser_info['tagid_list'] = json_decode($ucuser_info['tagid_list'],true)))
		{
			$ucuser_info['tagid_list'] = array_diff($ucuser_info['tagid_list'],array($tag_id));
			$ucuser_info['tagid_list'] = json_encode($ucuser_info['tagid_list']);
			$this->where('uid = '.$ucuser_info['uid'])->update($ucuser_info);

		}
	}

    //deprecated 已废弃
    public function getErrorMessage($error_code = null)
    {

        $error = $error_code == null ? $this->error : $error_code;
        switch ($error) {
            case -1:
                $error = '用户名长度必须在16个字符以内！';
                break;
            case -2:
                $error = '用户名被禁止注册！';
                break;
            case -3:
                $error = '用户名被占用！';
                break;
            case -4:
                $error = '密码长度必须在6-30个字符之间！';
                break;
            case -41:
                $error = '用户旧密码不正确';
                break;
            case -5:
                $error = '邮箱格式不正确！';
                break;
            case -6:
                $error = '邮箱长度必须在1-32个字符之间！';
                break;
            case -7:
                $error = '邮箱被禁止注册！';
                break;
            case -8:
                $error = '邮箱被占用！';
                break;
            case -9:
                $error = '手机格式不正确！';
                break;
            case -10:
                $error = '手机被禁止注册！';
                break;
            case -11:
                $error = '手机号被占用！';
                break;
            case -12:
                $error = '用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';
                break;
            case -31:
                $error = '昵称禁止注册';
                break;
            case -33:
                $error = '昵称长度不合法';
                break;
            case -32:
                $error = '昵称不合法';
                break;
            case -30:
                $error = '昵称已被占用';
                break;

            default:
                $error = '未知错误';
        }
        return $error;
    }
}
