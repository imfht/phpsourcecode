<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'group_pid' => array(
				'options' => $this->elements->getOptions()
			),
		),
		'columns' => array(
			'group_id',
			'group_name',
			'group_pid',
			'sort',
			'permission',
			'description',
			'_button_history_back_'
		)
	)
);
?>