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
		),
		'columns' => array(
			'login_name',
			'p_password',
			'p_repassword',
			'_button_save_',
			'_button_saveclose_',
			'_button_cancel_'
		)
	)
);
?>