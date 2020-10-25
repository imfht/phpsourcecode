<?php


class SetupForm extends CFormModel
{
	public $dbhost;
	public $dbname;
	public $username;
	public $password;
	public $repassword;
	public $verifyCode;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('dbhost,dbname,username', 'required', 'message' => '不能为空'),
            array('password', 'length', 'max' => 100, 'tooLong' => '密码过长'),
            array('repassword', 'compare', 'compareAttribute' => 'password', 'message' => '两次密码不一致'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'dbhost'=> 'MySQL主机地址',
			'dbname'=> 'MySQL数据库名',
			'username'=> 'MySQL用户名',
			'password'=> 'MySQL用户密码',
            'repassword' => '确认密码',
			'verifyCode'=>'验证码',
		);
	}
}
