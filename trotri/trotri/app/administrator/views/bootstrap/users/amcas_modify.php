<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'modify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'amca_pid' => array(
				'value' => $this->data['amca_pid']
			),
			'amca_pname' => array(
				'value' => $this->elements->getAmcaNameByAmcaId($this->data['amca_pid']),
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