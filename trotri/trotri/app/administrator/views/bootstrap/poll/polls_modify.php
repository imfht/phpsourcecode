<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'modify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
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
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>