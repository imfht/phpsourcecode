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
			'builder_name',
			'tbl_name',
			'tbl_profile',
			'tbl_engine',
			'tbl_charset',
			'tbl_comment',
			'srv_type',
			'srv_name',
			'app_name',
			'mod_name',
			'cls_name',
			'ctrl_name',
			'fk_column',
			'act_index_name',
			'act_view_name',
			'act_create_name',
			'act_modify_name',
			'act_remove_name',
			'index_row_btns',
			'description',
			'author_name',
			'author_mail',
			'dt_created',
			'dt_modified',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>