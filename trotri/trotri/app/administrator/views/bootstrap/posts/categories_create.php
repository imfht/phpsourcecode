<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'category_pid' => array(
				'options' => $this->elements->getOptions()
			),
		),
		'columns' => array(
			'category_name',
			'category_pid',
			'alias',
			'meta_title',
			'meta_keywords',
			'meta_description',
			'tpl_home',
			'tpl_list',
			'tpl_view',
			'sort',
			'description',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>