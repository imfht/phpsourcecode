<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'module_id',
			'module_name',
			'forbidden',
			'description',
			'fields',
			'_button_history_back_'
		)
	)
);
?>