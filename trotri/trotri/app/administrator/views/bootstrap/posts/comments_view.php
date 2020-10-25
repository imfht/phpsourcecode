<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'comment_id',
			'content',
			'author_name',
			'author_mail',
			'post_title',
			'post_id',
			'is_published',
			'author_url',
			'good_count',
			'bad_count',
			'creator_name',
			'last_modifier_name',
			'dt_created',
			'dt_last_modified',
			'ip_created',
			'ip_last_modified',
			'_button_history_back_'
		)
	)
);
?>