<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'type_id',
			'type_name',
			'form_type',
			'field_type',
			'category',
			'sort',
			'_button_history_back_'
		)
	)
);
?>