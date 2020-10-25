<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'category_pid' => array(
				'options' => $this->elements->getOptions()
			),
		),
		'columns' => array(
			'category_id',
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
			'_button_history_back_'
		)
	)
);
?>