<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'type_key' => array(
				'value' => $this->type_key
			),
			'type_name' => array(
				'value' => $this->elements->getTypeNameByTypeKey($this->type_key),
			),
			'menu_pid' => array(
				'options' => $this->elements->getOptions($this->type_key),
			)
		),
		'columns' => array(
			'menu_id',
			'menu_name',
			'menu_pid',
			'menu_url',
			'type_name',
			'type_key',
			'picture',
			'alias',
			'description',
			'allow_unregistered',
			'is_hide',
			'sort',
			'attr_target',
			'attr_title',
			'attr_rel',
			'attr_class',
			'attr_style',
			'dt_created',
			'dt_last_modified',
			'_button_history_back_'
		)
	)
);
?>