<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
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
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>