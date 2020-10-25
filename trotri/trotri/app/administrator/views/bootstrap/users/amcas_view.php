<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'amca_pname' => array(
				'value' => $this->elements->getAmcaNameByAmcaId($this->data['amca_pid']),
			),
		),
		'columns' => array(
			'amca_id',
			'amca_name',
			'amca_pname',
			'prompt',
			'sort',
			'category',
			'_button_history_back_'
		)
	)
);
?>