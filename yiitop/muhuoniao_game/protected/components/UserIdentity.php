<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	
	private $_id;
	private $_name;
	public function authenticate(){
		$this->username=trim($this->username);
		$user = Member::model()->findByAttributes(array('mname'=>$this->username));
		if($user===null){
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}else{
			if($user->password !==$user->encrypt($this->password)){
				$this->errorCode=self::ERROR_PASSWORD_INVALID;
			}else{
				//if($this->createAction('captcha')->getVerifyCode()!=$this->verifyCode){
					//$this->errorCode=self::ERROR_VERIFYCODE_INVALID;
				//}else{
					$this->_id=$user->id;
					
					if(null===$user->login_time){
						$lastLogin=time();
					}else{
						$lastLogin=strtotime($user->login_time);

					}
					$this->_name=$user->mname;
					$this->setState('lastLoginTime',$lastLogin);
					$this->errorCode=self::ERROR_NONE;
				//}
				
			}
		}
		return !$this->errorCode;
	}
	public function getId(){
		return $this->_id;
	}
	public function getName(){
		return $this->_name;
	}
}