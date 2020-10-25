<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'topic_id',
			'topic_name',
			'topic_key',
			'cover',
			'cover_file',
			'meta_title',
			'meta_keywords',
			'meta_description',
			'html_style',
			'html_script',
			'html_head',
			'html_body',
			'sort',
			'is_published',
			'use_header',
			'use_footer',
			'dt_created',
			'_button_history_back_'
		)
	)
);
?>