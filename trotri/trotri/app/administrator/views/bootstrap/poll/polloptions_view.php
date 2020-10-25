<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
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
			'option_id',
			'option_name',
			'poll_name',
			'poll_key',
			'votes',
			'sort',
			'_button_history_back_'
		)
	)
);
?>