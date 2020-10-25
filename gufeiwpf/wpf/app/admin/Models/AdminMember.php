<?php
namespace Wpf\App\Admin\Models;
class AdminMember extends \Wpf\App\Admin\Common\Models\CommonModel{
    public function initialize(){
        parent::initialize();
        
        $this->hasMany("id", "Wpf\App\Admin\Models\AdminAuthGroupAccess", "uid");
        
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\SoftDelete(
            array(
                'field' => 'status',
                'value' => -1
            )
        ));
        
    }
    
    public function onConstruct(){
        parent::onConstruct();
    }
    
    public function beforeCreate(){
        $this->reg_time = time();
        $this->password = crypt_md5($this->password);
        $this->reg_ip = $this->getDI()->getRequest()->getClientAddress();
    }
    
    public function beforeSave(){
        $this->update_time = $this->last_login_time = time();        
        $this->last_login_ip = $this->getDI()->getRequest()->getClientAddress();
    }
    
    
    public function validation(){

        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(
            array(
                "field"   => "username",
                "message" => "用户名重复"
            )
        ));
        
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(
            array(
                "field"   => "username",
                "message" => "用户名必须填写"
            )
        ));
        
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(
            array(
                "field"   => "password",
                "message" => "密码不能为空"
            )
        ));
        
        
        $this->validate(new \Phalcon\Mvc\Model\Validator\Email(
            array(
                "field"   => "email",
                "message" => "1111必须是邮箱格式",
                "allowEmpty" => true
            )
        ));


        return $this->validationHasFailed() != true;
    }
    
    public function updateLogin($id){
        
        $info = $this->findFirst($id);
        
        if($info){
            $info->login           = $info->login+1;
            $info->last_login_time = time();
            $info->last_login_ip = $this->getDI()->getRequest()->getClientAddress();
            
            $info->save();
        }
        
    }
    
    
    public function checkLogin($username=null,$password=null,$remember = 0){
        
        $map = array(
            "username = '{$username}'"
        );
        
        
        if($userinfo = $this->cleanCache()->findFirst($map)){
            if(crypt_md5($password) === $userinfo->password){
                $this->updateLogin($userinfo->id); //更新用户登录信息
                
                /*记录COOKIE*/
                if($remember){
                    $remember = 86400*365;
                }else{
                    $remember = 86400;
                }
                
                
                $cookie = array(
                    "id" => $userinfo->id,
                    "username" => $userinfo->username
                );
                
                $this->getDI()->getShared('cookies')->set($this->getDI()->getConfig()->cookie_name->WPF_ADMIN_AUTH,serialize($cookie),time()+$remember);
                $this->getDI()->getShared('cookies')->send();
                
                
				return $userinfo->id; //登录成功，返回用户ID
			} else {
				return -2; //密码错误
			}    
        }else{
            return -1; //用户不存在或被禁用
        }
    }
    
    
    public function register($username, $password, $email, $mobile=""){
		$data = array(
			'username' => $username,
			'password' => $password,
			'email'    => $email,
			'mobile'   => $mobile,
		);

		//验证手机
		if(empty($data['mobile'])) unset($data['mobile']);
        
		/* 添加用户 */
		if($this->create($data)){
			$uid = $this->id;
			return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
		}
	}
    
    /**
	 * 验证用户密码
	 * @param int $uid 用户id
	 * @param string $password_in 密码
	 * @return true 验证成功，false 验证失败
	 * @author 吴佳恒
	 */
	public function verifyUser($uid, $password_in){
	   
        $user = $this->getInfo($uid)->toArray();
        $password = $user['password'];
        
        if(crypt_md5($password_in) === $password){
            return true;
        }
        return false;
	}
    
    public function logout(){
        $this->getDI()->getShared('cookies')->delete($this->getDI()->getConfig()->cookie_name->WPF_ADMIN_AUTH);
        $this->getDI()->getShared('cookies')->send();
    }
    
}