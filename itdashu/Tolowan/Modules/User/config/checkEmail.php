<?php
$settings = array(
	'formId' => 'login',
	'form' => array(
		'action' => '',
		'method' => 'post',
		'class' => array(),
		'accept-charset' => 'utf-8',
		'role' => 'form',
		'id' => 'login',
	),
	'email_code' => array(
		'label' => '验证码',
		'userOptions' => array(
			'labelAttributes' => array(
				'class' => array(),
			),
			'groupAttributes' => array(
				'class' => array(),
				'id' => 'group_name',
			),
			'widgetBoxAttributes' => array(
				'class' => array(),
			),
			'helpAttributes' => array(
				'class' => array(),
			),
		),
		'error' => '',
		'description' => '您的邮箱收到的验证码',
		'field' => 'string',
		'widget' => 'Password',
		'validate' => array(),
		'attributes' => array(),
		'required' => true,
	),
);
