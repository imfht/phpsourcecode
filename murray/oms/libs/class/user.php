<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 用户模型类
*/

defined('INPOP') or exit('Access Denied');

class user extends Model{

	public $username = '';
	public $email = '';
	public $db;
	public $errormsg;
	public $errormsgs;
	public $table;
	public $info;

	//初始化
    public function __construct(){
		parent::__construct("users", "uid");
		$this->checkLogin();
        register_shutdown_function(array(&$this, '__destruct'));
    }

	//销毁
	public function __destruct(){}

	//设置用户名
	public function setUsername($username){
		if($this->isBadword($username)){
			$this->errormsg = 'username_is_verify';
			return false;
		}
		$this->username = $username;
		return TRUE;
	}

	//设置邮箱
	public function setMail($mail){
		if($mail == "") return false;
		$this->email = $mail;
		return true;
	}
	
	//设置用户
	protected function setSessionInfo($info){
		if(!empty($info) && is_array($info)){		
			unset($info['password']);
			unset($info['auth']);
			if($info['groups']){
				$groupids = explode(";", $info['groups']);
				array_unique($groupids);
				if(!empty($groupids)){
					$groupids = implode(",", $groupids);
					$_group = new groupModel();
					$groupInfo = $_group->getList(" groupid in (".$groupids.") ");
					$info['groupinfo'] = $groupInfo;
					$roleids = array();
					foreach($groupInfo as $g){
						$roleids[] = $g['roleids'];
					}
					$roleids = implode(";", $roleids);
					$info['roleids'] = $roleids;
				}
			}
			$_SESSION['user'] = $info;
			$this->info = $info;
		}
	}

	//获取用户信息
	public function getInfo($keyName = "username", $input = ""){
		if($input != ""){
			$getBy = $input;
		}else{
			$getBy = $this->username;
		}
        $return = $this->getOneBy($keyName."='".$getBy."'");
		return $return;
	}

	//通过用户id获取用户信息
	public function getInfoById($uid = 0){
		$id = (int)$uid;
		if($id > 0){
			$return = $this->getOneBy("uid=".$id);
		}else{
			$return = false;
		}
		return $return;
	}

	//屏蔽用户
	public function banName($username){
		$bannames = explode(',', $MOD['banname']);
		foreach($bannames as $banname){
			if(strpos($username, $banname)!==false) return TRUE;
		}
		return false;
	}

	//凭借邮箱登陆
	public function loginByMail($email, $loginPassword, $login_cookietime = 31536000, $forward = ''){
		$info = $this->getInfo("email", $email);
		$return = $this->login($info, $loginPassword, $login_cookietime, $forward);
		return $return;
	}

	//凭借用户名登陆
	public function loginByUsername($username, $loginPassword, $login_cookietime = 31536000, $forward = ''){
		$info = $this->getInfo("username", $username);
		$return = $this->login($info, $loginPassword, $login_cookietime, $forward);
		return $return;
	}

	//登陆
	protected function login($info = array(), $login_password, $login_cookietime = 31536000, $forward = ''){
		if(empty($info)){
			$this->errormsg = 'user_not_exists';
			return false;
		}
		//释放变量
		extract($info);
		if($password != md5(md5($login_password).$auth)){
			$this->errormsg = 'password_is_wrong';
			return false;
		}
		if($groupid == 2){
			$this->errormsg = 'user_is_locked';
			return false;
		}elseif($groupid == 4){
			$this->errormsg = 'user_is_verify';
			return false;
		}elseif($groupid == 5){
			$this->errormsg = 'user_is_checking';
			return false;
		}
		$this->setSessionInfo($info);
		$_cookietime = $login_cookietime ? intval($login_cookietime) : (getCookie('cookietime') ? getCookie('cookietime') : 0);
		$cookietime = $_cookietime ? time() + $_cookietime : 0;
		$isAuthKey = md5($_SERVER['HTTP_USER_AGENT']);
		$isAuth = isAuth($uid."\t".$password."\t", 'ENCODE', $isAuthKey);
		mkCookie('auth', $isAuth, $cookietime);
		mkCookie('cookietime', $_cookietime, $cookietime);
		return $info;
	}

	//退出
	public function logout($forward = ''){
		mkCookie('auth', '');
		mkCookie('cookietime', '');
		unset($_SESSION['user']);
		return true;
	}

	//注册
	public function register($userinfo){
		if(!is_array($userinfo)) return false;
		if(!$this->setUsername($userinfo['username'])) return false;
		if(!$this->setMail($userinfo['email'])) return false;      
		$userinfo = new_htmlspecialchars($userinfo);
		$userinfo['auth'] = $this->makeAuthStr($userinfo['username']);
		if(!$userinfo['password']){
			//从盐的第6位往后取6位
			$userinfo['password'] = substr($userinfo['auth'], 6, 6);
		}
		$userinfo['password'] = md5(md5($userinfo['password']).$userinfo['auth']);
		$userinfo['createtime'] = time();
		if(!$this->userExists()) $return = $this->add($userinfo);
		return $return;
	}

	//检测登录状态
	public function checkLogin(){
		//本地账户状态验证
		$isAuth = getCookie('auth');
		if($isAuth){
			$isAuthKey = md5($_SERVER['HTTP_USER_AGENT']);
			list($uid, $password) = $isAuth ? explode("\t", isAuth($isAuth, 'DECODE', $isAuthKey)) : array(0, '', '');
			$uid = intval($uid);
			if($uid >0){
				//不走缓存
				$info = $this->getOne($uid, false);
				if($info){
					$this->setSessionInfo($info);
				}else{
					mkCookie('auth', '');
					mkCookie('cookietime', '');
				}
			}
		}
		return $isAuth;
	}
	
	//修改用户信息
	public function doUpdate($uid, $userinfo){
		if(!is_array($userinfo) || !$uid) return false;        
		$userinfo = new_htmlspecialchars($userinfo);
		$userinfo['updatetime'] = time();
		$info = $this->getInfoById($uid);
		if(empty($info)) return false;
		if($userinfo['password']){
			$userinfo['password'] = md5(md5($userinfo['password']).$info['auth']);
		}else{
			unset($userinfo['password']);
		}
		
		$this->keyId = $uid;
		$return = $this->edit($userinfo);
		return $userinfo;
	}

	//用户修改密码，需要旧密码
    public function editPasswordByUser($uid, $oldpassword, $password){
		if($uid < 1) return false;
        if(empty($oldpassword) || empty($password) || !$uid) return false;
		$info = $this->getInfoById($uid);
        if(md5(md5($oldpassword).$info['auth']) != $info['password']) return false; 
		$userinfo['password'] = md5(md5($password).$info['auth']);
		$userinfo['updatetime'] = time();
		$this->keyId = $uid;
		$return = $this->edit($userinfo);
		return $return;
    }

	//后台修改用户密码
	public function editPassword($uid, $password){
        if(empty($oldpassword) || empty($password) || !$uid) return false;
		$info = $this->getInfoById($uid);
		$userinfo['password'] = md5(md5($password).$info['auth']);
		$userinfo['updatetime'] = time();
		$this->keyId = $uid;
		$return = $this->edit($userinfo);
		return $return;
	}

	//用户组
	public function group($groupid = 0){
        return $this->db->get_one("SELECT * FROM ".$this->table_group." WHERE groupid=$groupid limit 0,1");
	}

	//设置用户组
	public function setGroup($username, $groupid){
		if(!$username) $username = $_username;
		$groupid = intval($groupid);
	    $this->db->query("UPDATE ".$this->table." SET groupid=$groupid WHERE username='$username'");
		return $this->db->affected_rows();
	}

	//检查邮箱地址
	public function emailExists($email){
		return $this->db->get_one("SELECT email FROM ".$this->table." WHERE email='$email' limit 0,1");
	}

	//是否存在用户
	public function userExists(){
		return $this->db->get_one("SELECT uid FROM ".$this->table." WHERE username='".$this->username."' limit 0,1");
	}

	//锁定
	public function lock($userid, $val = 1){
		$userids = is_array($userid) ? implode(',', $userid) : intval($userid);
		$groupid = intval($val) == 1 ? 2 : 6;
		$this->db->query("UPDATE ".$this->table." SET groupid=$groupid WHERE uid IN ($userids)");
		return $this->db->affected_rows();
	}
	
	//过滤敏感注册名
	public function isBadword($string){
		$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
		foreach($badwords as $value){
			if(strpos($string, $value) !== false){ 
				return TRUE; 
			}
		}
		return false;
	}

	//生成用户KEY
	protected function makeAuthStr($username){
		$authstr = random(32, 'abcABCdefDEFghjGHJklmnKLMNopqOPQrstRSTxyzXYZuvwiUVXWYZ0123456789');
		return $authstr;
	}

	//检查用户KEY
	public function checkAuthStr($username, $authstr){
        $authstr = trim($authstr);
		if(!$username || !$authstr) return false;
		$info = $this->getOneBy("username='".$username."'");
		return $authstr == $info['authstr'];
	}

	//删除
	public function delete($userid){
		$userids = is_array($userid) ? implode(',', $userid) : intval($userid);
		$this->db->query("DELETE FROM ".$this->table." WHERE uid IN ($userids)");
		$result = $this->db->affected_rows();
		return $result;
	}

	//出错信息
	public function errormsg(){
		return $this->errormsgs[$this->errormsg];
	}
}
?>