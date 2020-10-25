<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'poll_id' => array(
				'value' => $this->poll_id
			),
			'poll_name' => array(
				'value' => $this->poll_name
			),
			'poll_key' => array(
				'value' => $this->poll_key
			),
		),
		'columns' => array(
			'option_name',
			'poll_id',
			'poll_name',
			'poll_key',
			'votes',
			'sort',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>