<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;

	public function authenticate()
	{
		$user= User::model()->find(
            'LOWER(username)=? and password=? and status=?',
            array(
                strtolower($this->username),
                User::encrpyt($this->password),
                Yii::app()->params['status']['ischecked'],
            ));
		if($user === null)
			return false;
		else
		{
			$this->_id = $user->id;
			$this->setState('userInfo',$user);
            $user->updateLoginInfo();
			return true;
		}
	}

	public function getId()
    {
        return $this->_id;
    }
}