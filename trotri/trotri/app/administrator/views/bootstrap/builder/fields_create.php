<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'builder_id' => array(
				'value' => $this->builder_id
			),
			'builder_name' => array(
				'value' => $this->elements->getBuilderNameByBuilderId($this->builder_id),
			),
			'group_id' => array(
				'options' => $this->elements->getGroupsByBuilderId($this->builder_id, true)
			),
			'type_id' => array(
				'options' => $this->elements->getTypeNames()
			)
		),
		'columns' => array(
			'field_name',
			'column_length',
			'column_auto_increment',
			'column_unsigned',
			'column_comment',
			'builder_name',
			'group_id',
			'type_id',
			'sort',
			'html_label',
			'form_prompt',
			'form_prompt_examples',
			'form_required',
			'form_modifiable',
			'index_show',
			'index_sort',
			'form_create_show',
			'form_create_sort',
			'form_modify_show',
			'form_modify_sort',
			'form_search_show',
			'form_search_sort',
			'builder_id',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>