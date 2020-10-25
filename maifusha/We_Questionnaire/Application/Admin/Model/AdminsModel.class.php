<?php 
namespace Admin\Model;
use Think\Model;

class AdminsModel extends Model
{
	protected $_validate = array(
		array('account', 'require', '请填写账号', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('account', '', '账号已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT),

		array('password', 'require', '请填写密码', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array(
		array('last_login_date', 'date', self::MODEL_BOTH, 'function', array('Y-m-d')),
		array('last_login_ip', 'get_client_ip', self::MODEL_BOTH, 'function', array(1)),
	);


	/**
	 * 账号认证，认证通过则返回账号信息数组
	 * @param string $account  账号
	 * @param string $password  密码
	 * @return array  账号信息关联数组
	 */
	public function authenticate( $account, $password )
	{
		$admin = $this->getByAccount( $account );

		if( $admin ){ //指定账号存在
			if( md5($password) == $admin['password'] ){ //密码匹配
				return $admin;
			}else{ //密码不匹配
				$this->error = '密码错误';
				return false;
			}
		}else{ //指定账号不存在
			$this->error = '账号不存在';
			return false;
		}
	}

	/**
	 * 更新登录信息
	 */
	public function updateLogin()
	{
		//
	}

}
?>