<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'builder_id',
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
			'trash',
			'_button_history_back_'
		)
	)
);
?>