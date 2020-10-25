<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'modify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'builder_name' => array(
				'value' => $this->elements->getBuilderNameByBuilderId($this->builder_id),
			),
		),
		'columns' => array(
			'group_name',
			'builder_name',
			'prompt',
			'builder_id',
			'sort',
			'description',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>