<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'member_id',
			'login_name',
			'login_type',
			'member_name',
			'member_mail',
			'member_phone',
			'relation_member_id',
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
			'_button_history_back_'
		)
	)
);
?>