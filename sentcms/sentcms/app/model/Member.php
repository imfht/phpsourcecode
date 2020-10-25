<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\model;

use sent\jwt\facade\JWTAuth;
use think\Model;
use xin\helper\Server;

class Member extends Model {

	protected $pk = 'uid';

	protected $createTime = 'reg_time';
	protected $updateTime = 'last_login_time';

	protected $insert = ['reg_ip', 'status' => 1];

	public $editfield = [
		['name'=>'uid','type'=>'hidden'],
		['name'=>'username','title'=>'用户名','type'=>'readonly','help'=>''],
		['name'=>'nickname','title'=>'昵称','type'=>'text','help'=>''],
		['name'=>'password','title'=>'密码','type'=>'password','help'=>'为空时则不修改'],
		['name'=>'sex','title'=>'性别','type'=>'select','option'=> [['key' => '0', 'label'=>'保密'],['key' => '1', 'label' =>'男'],['key' => '2', 'label'=>'女']],'help'=>''],
		['name'=>'email','title'=>'邮箱','type'=>'text','help'=>'用户邮箱，用于找回密码等安全操作'],
		['name'=>'qq','title'=>'QQ','type'=>'text','help'=>''],
		['name'=>'score','title'=>'用户积分','type'=>'text','help'=>''],
		['name'=>'signature','title'=>'用户签名','type'=>'textarea','help'=>''],
		['name'=>'status','title'=>'状态','type'=>'select','option'=>[['key' => '0', 'label'=>'禁用'],['key' => '1', 'label'=>'启用']],'help'=>''],
	];

	public $addfield = [
		['name'=>'username','title'=>'用户名','type'=>'text', 'is_must'=> true,'help'=>'用户名会作为默认的昵称'],
		['name'=>'nickname','title'=>'昵称','type'=>'text','help'=>''],
		['name'=>'password','title'=>'密码','type'=>'password', 'is_must'=> true, 'help'=>'用户密码不能少于6位'],
		['name'=>'repassword','title'=>'确认密码','type'=>'password', 'is_must'=> true, 'help'=>'确认密码'],
		['name'=>'email','title'=>'邮箱','type'=>'text','help'=>'用户邮箱，用于找回密码等安全操作'],
	];
    
	public static $useredit = [
		['name'=>'uid','type'=>'hidden'],
		['name'=>'nickname','title'=>'昵称','type'=>'text','help'=>''],
		['name'=>'sex','title'=>'性别','type'=>'select','option'=>[['key' => '0', 'label'=>'保密'],['key' => '1', 'label' =>'男'],['key' => '2', 'label'=>'女']],'help'=>''],
		['name'=>'email','title'=>'邮箱','type'=>'text','help'=>'用户邮箱，用于找回密码等安全操作'],
		['name'=>'mobile','title'=>'联系电话','type'=>'text','help'=>''],
		['name'=>'qq','title'=>'QQ','type'=>'text','help'=>''],
		['name'=>'signature','title'=>'用户签名','type'=>'textarea','help'=>''],
	];

	public $userextend = [
		['name'=>'company','title'=>'单位名称','type'=>'text','help'=>''],
		['name'=>'company_addr','title'=>'单位地址','type'=>'text','help'=>''],
		['name'=>'company_contact','title'=>'单位联系人','type'=>'text','help'=>''],
		['name'=>'company_zip','title'=>'单位邮编','type'=>'text','help'=>''],
		['name'=>'company_depart','title'=>'所属部门','type'=>'text','help'=>''],
		['name'=>'company_post','title'=>'所属职务','type'=>'text','help'=>''],
		['name'=>'company_type','title'=>'单位类型','type'=>'select', 'option'=>'', 'help'=>''],
	];

	protected $status = [
		1 => '正常',
		0 => '禁用',
	];

	public $loginVisible = ['uid', 'username', 'nickname', 'access_token', 'department']; //用户登录成功返回的字段

	protected function getStatusTextAttr($value, $data) {
		return isset($this->status[$data['status']]) ? $this->status[$data['status']] : '未知';
	}

	protected function getAvatarAttr($value, $data) {
		return $value ? $value : request()->domain() . '/static/common/images/default_avatar.jpg';
	}

	protected function setPasswordAttr($value, $data) {
		return md5($value . $data['salt']);
	}

	protected function setRegIpAttr($value) {
		return Server::getRemoteIp();
	}

	protected function getAccessTokenAttr($value, $data) {
		$token = ['data' => ['uid' => $data['uid'], 'username' => $data['username'], 'password' => $data['password']]];
		return JWTAuth::builder($token); //参数为用户认证的信息，请自行添加
	}

	protected function getNicknameAttr($value, $data){
		return $value ? $value : $data['username'];
	}

	/**
	 * 用户登录
	 */
	public function login($request) {
		$username = $request->param('username', '');
		$password = $request->param('password', '');
		$type = $request->param('type', 1);
		$map = [];
		switch ($type) {
			case 1:
				$map['username'] = $username;
				break;
			case 2:
				$map['email'] = $username;
				break;
			case 3:
				$map['mobile'] = $username;
				break;
			default:
				throw new \think\Exception('参数错误', 10006);
				return false; //参数错误
		}
		if (!$username) {
			throw new \think\Exception('用户名不能为空', 10006);
			return false;
		}

		$user = $this->where($map)->find();
		if (isset($user['uid']) && $user['uid'] && $user['status']) {
			/* 验证用户密码 */
			if (md5($password . $user['salt']) === $user['password']) {
				/* 更新登录信息 */
				$this->record($user);
				return $user->append(array('access_token', 'avatar'))->visible($this->loginVisible)->toArray(); //登录成功，返回用户信息
			} else {
				throw new \think\Exception('密码错误', 10006);
				return false; //密码错误
			}
		} else {
			throw new \think\Exception('用户不存在或被禁用', 10006);
			return false;
		}
	}

	/**
	 * @title: 注册
	 */
	public function register($request) {
		$data = [];
		$data['username'] = $request->param('username', '');
		$data['nickname'] = $request->param('nickname', '');
		$data['password'] = $request->param('password', '');
		$data['repassword'] = $request->param('repassword', '');
		$data['email'] = $request->param('email', '');
		$data['mobile'] = $request->param('mobile', '');
		$data['salt'] = \xin\helper\Str::random(6);

		$result = self::create($data);
		if (false !== $result) {
			$user = $this->where('uid', $result->uid)->find();
		} else {
			$this->error = "注册失败！";
			return false;
		}
		/* 更新登录信息 */
		$this->record($user);
		return $user->append(['access_token', 'avatar'])->visible($this->loginVisible)->toArray(); //登录成功，返回用户信息
	}

	/**
	 * @title: 获取用户列表
	 */
	public function getUserList($request) {
		$map = [];
		$param = $request->param();

		$order = "status desc, uid desc";

		$map[] = ['status', '>=', 0];

		if (isset($param['status']) && $param['status'] != '') {
			$map[] = ['status', '=', $param['status']];
		}

		if (isset($param['department']) && $param['department'] != '') {
			$map[] = ['department', '=', $param['department']];
		}

		if (isset($param['email']) && $param['email'] != '') {
			$map[] = ['email', '=', $param['email']];
		}
		if (isset($param['name']) && $param['name'] != '') {
			$map[] = ['username|nickname', 'LIKE', '%' . $param['name'] . '%'];
		}

		if (isset($param['task_id']) && $param['task_id']) {
			$map[] = ['uid', 'IN', function ($query) use ($param) {
				$query->name('task_user')->where('task_id', $param['task_id'])->field('user_id');
			}];
		}

		$res = self::with(['role', 'group'])->field('uid,username,nickname,status,email,mobile,department,reg_time')->where($map)->order($order)->paginate($request->pageConfig);
		$list = $res->append(['avatar', 'status_text'])->each(function($item){
			if($item['group'] === null){
				$item['group'] = ['title' => '未定义'];
				$item['group_title'] = '未定义';
			}else{
				$item['group_title'] = $item['group']['title'];
			}
			return $item;
		});
		return $list;
	}

	/**
	 * @title: 获取用户列表
	 */
	public function getUserDetail($request) {
		$uid = $request->param('uid');
		if (!$uid) {
			$uid = $request->user['uid'];
		}

		$info = $this->where('uid', $uid)->find();
		return $info->append(['avatar', 'status_text'])->toArray();
	}

	public function editUser($request, $uid = 0){
		$data = $request->post();
		$data['uid'] = $uid ? $uid : $data['uid'];

		if (!$data['uid']) {
			return false;
		}
		if (isset($data['password']) && $data['password'] !== '') {
			$data['salt'] = \xin\helper\Str::random(6);
			return self::update($data, ['uid' => $data]);
		}else{
			if(isset($data['password'])){
				unset($data['password']);
			}
			return $this->where('uid', $data['uid'])->save($data);
		}
	}

	/**
	 * 用户登录信息更新
	 * @param  [type] $user [description]
	 * @return [type]       [description]
	 */
	public function record($user) {
		/* 更新登录信息 */
		$data = array(
			'uid' => $user['uid'],
			'login' => array('inc', '1'),
			'last_login_time' => time(),
			'last_login_ip' => get_client_ip(1),
		);
		self::where(['uid' => $user['uid']])->update($data);
	}

	public static function sendFindPaswd($user){
		$token = \xin\helper\Secure::encrypt($user['username'] . "|" . $user['email'], \think\facade\Env::get('jwt.secret'));
		$url = url('/user/index/resetpasswd', ['token'=>$token], true, true);
		return true;
	}

	public function depart() {
		return $this->hasOne('Department', 'id', 'department');
	}

	public function role() {
		return $this->hasOne('RoleAccess', 'uid', 'uid');
	}

	public function group(){
		return $this->hasOneThrough(AuthGroup::class, AuthGroupAccess::class, 'uid', 'id', 'uid', 'group_id');
	}
}