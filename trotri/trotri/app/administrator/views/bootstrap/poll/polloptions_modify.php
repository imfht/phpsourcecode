<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'modify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'values' => $this->data,
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