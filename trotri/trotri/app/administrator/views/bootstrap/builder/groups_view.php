<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'group_id',
			'group_name',
			'prompt',
			'builder_id',
			'sort',
			'description',
			'_button_history_back_'
		)
	)
);
?>