<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'group_ids' => array(
				'options' => $this->elements->getGroupIds()
			),
		),
		'columns' => array(
			'user_id',
			'login_name',
			'login_type',
			'salt',
			'user_name',
			'user_mail',
			'user_phone',
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
			'trash',
			'group_ids',
			'sex',
			'birthday',
			'address',
			'qq',
			'head_portrait',
			'remarks',
			'_button_history_back_'
		)
	)
);
?>