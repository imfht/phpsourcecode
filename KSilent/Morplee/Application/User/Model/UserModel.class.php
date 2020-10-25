<?php

namespace User\Model;
use Think\Model;
/**
 * 会员模型
 */
class UserModel extends Model{
	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = "u_";

	/**
	 * 数据库连接
	 * @var string
	 */
	protected $connection = UC_DB_DSN;

	/* 用户模型自动验证 */
	protected $_validate = array(
		/* 验证用户名 */
		array('user_name', '1,30', -1, self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
		array('user_name', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册
		array('user_name', '', -3, self::EXISTS_VALIDATE, 'unique'), //用户名被占用

		/* 验证密码 */
		array('user_pwd', '6,30', -4, self::EXISTS_VALIDATE, 'length'), //密码长度不合法

		/* 验证邮箱 */
		array('user_email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
		array('user_email', '1,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
		array('user_email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
		array('user_email', '', -8, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用

		/* 验证手机号码 */
		array('user_phone', '//', -9, self::EXISTS_VALIDATE), //手机格式不正确 TODO:
		array('user_phone', 'checkDenyuser_phone', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
		array('user_phone', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用
	);

	/* 用户模型自动完成 */
	protected $_auto = array(
		array('user_pwd', 'think_ucenter_md5', self::MODEL_BOTH, 'function', UC_AUTH_KEY),
		array('user_status',1),
		array('site_id', 0)
	);

	/**
	 * 检测用户名是不是被禁止注册
	 * @param  string $user_name 用户名
	 * @return boolean          ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyMember($user_name){
		return true; //TODO: 暂不限制，下一个版本完善
	}

	/**
	 * 注册一个新用户
	 * @param  string $user_name 用户名
	 * @param  string $user_pwd 用户密码
	 * @param  string $user_email    用户邮箱
	 * @param  string $user_phone   用户手机号码
	 * @return integer          注册成功-用户信息，注册失败-错误编号
	 */
	public function register($user_name, $user_pwd, $user_email, $user_phone){
		$data = array(
			'user_name' => $user_name,
			'user_pwd' => $user_pwd,
			'user_email'    => $user_email,
			'user_phone'   => $user_phone,
		);

		//验证手机
		if(empty($data['user_phone'])) unset($data['user_phone']);

		/* 添加用户 */
		if($this->create($data)){
			$uid = $this->add();
			return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
		} else {
			return $this->getError(); //错误详情见自动验证注释
		}
	}

	/**
	 * 用户登录认证
	 * @param  string  $user_name 用户名
	 * @param  string  $user_pwd 用户密码
	 * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	public function login($user_name, $user_pwd, $type = 1){
		$map = array();
		switch ($type) {
			case 1:
				$map['user_name'] = $user_name;
				break;
			case 2:
				$map['user_email'] = $user_name;
				break;
			case 3:
				$map['user_phone'] = $user_name;
				break;
			case 4:
				$map['id'] = $user_name;
				break;
			default:
				return 0; //参数错误
		}

		/* 获取用户数据 */
		$user = $this->where($map)->find();
		if(is_array($user) && $user['user_status']){
			/* 验证用户密码 */
			if(think_ucenter_md5($user_pwd, UC_AUTH_KEY) === $user['user_pwd']){
				return $user['user_id']; //登录成功，返回用户ID
			} else {
				return -2; //密码错误
			}
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * 获取用户信息
	 * @param  string  $uid         用户ID或用户名
	 * @param  boolean $is_user_name 是否使用用户名查询
	 * @return array                用户信息
	 */
	public function info($uid, $is_user_name = false){
		$map = array();
		if($is_user_name){ //通过用户名获取
			$map['user_name'] = $uid;
		} else {
			$map['id'] = $uid;
		}

		$user = $this->where($map)->field('id,user_name,user_email,user_phone,status')->find();
		if(is_array($user) && $user['status'] = 1){
			return array($user['id'], $user['user_name'], $user['user_email'], $user['user_phone']);
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * 检测用户信息
	 * @param  string  $field  用户名
	 * @param  integer $type   用户名类型 1-用户名，2-用户邮箱，3-用户电话
	 * @return integer         错误编号
	 */
	public function checkField($field, $type = 1){
		$data = array();
		switch ($type) {
			case 1:
				$data['user_name'] = $field;
				break;
			case 2:
				$data['user_email'] = $field;
				break;
			case 3:
				$data['user_phone'] = $field;
				break;
			default:
				return 0; //参数错误
		}

		return $this->create($data) ? 1 : $this->getError();
	}

	/**
	 * 更新用户信息
	 * @param int $uid 用户id
	 * @param string $user_pwd 密码，用来验证
	 * @param array $data 修改的字段数组
	 * @return true 修改成功，false 修改失败
	 * @author huajie <banhuajie@163.com>
	 */
	public function updateUserFields($uid, $user_pwd, $data){
		if(empty($uid) || empty($user_pwd) || empty($data)){
			$this->error = '参数错误！';
			return false;
		}

		//更新前检查用户密码
		if(!$this->verifyUser($uid, $user_pwd)){
			$this->error = '验证出错：密码不正确！';
			return false;
		}

		//更新用户信息
		$data = $this->create($data);
		if($data){
			return $this->where(array('id'=>$uid))->save($data);
		}
		return false;
	}

	/**
	 * 验证用户密码
	 * @param int $uid 用户id
	 * @param string $user_pwd_in 密码
	 * @return true 验证成功，false 验证失败
	 * @author huajie <banhuajie@163.com>
	 */
	protected function verifyUser($uid, $user_pwd_in){
		$user_pwd = $this->getFieldById($uid, 'user_pwd');
		if(think_ucenter_md5($user_pwd_in, UC_AUTH_KEY) === $user_pwd){
			return true;
		}
		return false;
	}

}
