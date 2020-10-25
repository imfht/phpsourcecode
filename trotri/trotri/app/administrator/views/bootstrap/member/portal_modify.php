<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'modify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'login_name' => array(
				'readonly' => true
			),
			'password' => array(
				'required' => false,
				'hint' => $this->MOD_MEMBER_MEMBER_PORTAL_PASSWORD_MODIFY_HINT
			),
			'repassword' => array(
				'required' => false,
			),
		),
		'columns' => array(
			'login_name',
			'password',
			'repassword',
			'member_name',
			'member_mail',
			'member_phone',
			'dt_registered',
			'dt_last_login',
			'dt_last_repwd',
			'ip_registered',
			'ip_last_login',
			'ip_last_repwd',
			'login_count',
			'repwd_count',
			'valid_mail',
			'valid_phone',
			'forbidden',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>