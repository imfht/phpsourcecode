<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'rank_id',
			'rank_name',
			'experience',
			'sort',
			'description',
			'_button_history_back_'
		)
	)
);
?>