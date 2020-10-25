<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{

		$user = Users::model()->find('email=:email',array('email'=>$this->username));
		//var_dump($user->passwd.'=='.Main::mymd5($this->password));die();

		if($user===null)
		{
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			//return false;
		}
		elseif($user->password != Main::myMd5($this->password))
		{
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			//return false;
		}
		else
		{
			$this->errorCode = self::ERROR_NONE;
		}

		return !$this->errorCode;

	}



}