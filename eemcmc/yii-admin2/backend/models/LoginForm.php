<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{

	public $username;
	public $password;
	public $rememberMe = true;
	private $_user = false;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['username', 'password'], 'required'],
			['rememberMe', 'boolean'],
			['password', 'validatePassword'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'username' => '用户名',
			'password' => '密码',
			'rememberMe' => '记住我',
		];
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 * @param array $params the additional name-value pairs given in the rule
	 */
	public function validatePassword($attribute, $params)
	{
		if (!$this->hasErrors())
		{
			$user = $this->getUser();
			if (!$user || !$user->validatePassword($this->password))
			{
				$this->addError($attribute, '错误的用户名或密码');
			}
		}
	}

	/**
	 * Logs in a user using the provided username and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function login()
	{
		if ($this->validate())
		{
			$remember_expire = \Yii::$app->params['user.remember_expire'];
			return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $remember_expire : 0);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	public function getUser()
	{
		if ($this->_user === false)
		{
			$this->_user = User::findByUsername($this->username);
		}

		return $this->_user;
	}

}
