<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'amca_pid' => array(
				'value' => $this->amca_pid
			),
			'amca_pname' => array(
				'value' => $this->elements->getAmcaNameByAmcaId($this->amca_pid),
			),
		),
		'columns' => array(
			'amca_name',
			'amca_pname',
			'prompt',
			'sort',
			'category',
			'amca_pid',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>