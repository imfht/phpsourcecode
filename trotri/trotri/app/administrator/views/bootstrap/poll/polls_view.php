<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'poll_id',
			'poll_name',
			'poll_key',
			'allow_unregistered',
			'm_rank_ids',
			'join_type',
			'interval',
			'is_published',
			'dt_publish_up',
			'dt_publish_down',
			'is_visible',
			'is_multiple',
			'max_choices',
			'description',
			'dt_created',
			'_button_history_back_'
		)
	)
);
?>