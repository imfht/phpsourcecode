<?php
$columns = array(
	'title',
	'alias',
	'category_id',
	'module_id',
	'picture',
	'picture_file',
	'content',
	'keywords',
	'description',
	'sort',
	'password',
	'is_head',
	'is_recommend',
	'is_jump',
	'jump_url',
	'is_published',
	'dt_publish_up',
	'dt_publish_down',
	'comment_status',
	'allow_other_modify',
	'hits',
	'praise_count',
	'comment_count',
	'creator_id',
	'creator_name',
	'last_modifier_id',
	'last_modifier_name',
	'dt_created',
	'dt_last_modified',
	'ip_created',
	'ip_last_modified',
	'_button_save_',
	'_button_saveclose_',
	'_button_savenew_',
	'_button_cancel_'
);

$columns = array_merge($columns, $this->profile_fields);

$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'modify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'category_id' => array(
				'options' => $this->elements->getCategoryNames()
			),
			'module_id' => array(
				'options' => $this->elements->getModuleNames(),
				'disabled' => true,
			),
		),
		'columns' => $columns
	)
);
?>