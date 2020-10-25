<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'builder_id' => array(
				'value' => $this->builder_id
			),
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