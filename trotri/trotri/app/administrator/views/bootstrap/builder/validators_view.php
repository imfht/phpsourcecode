<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'validator_id',
			'validator_name',
			'field_id',
			'options',
			'option_category',
			'message',
			'sort',
			'when',
			'_button_history_back_'
		)
	)
);
?>