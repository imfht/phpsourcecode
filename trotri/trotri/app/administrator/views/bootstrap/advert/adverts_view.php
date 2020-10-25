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
		),
		'columns' => array(
			'advert_id',
			'advert_name',
			'type_name',
			'type_key',
			'description',
			'is_published',
			'dt_publish_up',
			'dt_publish_down',
			'sort',
			'show_type',
			'show_code',
			'title',
			'advert_url',
			'advert_src',
			'advert_src_file',
			'advert_src2',
			'advert_src2_file',
			'attr_alt',
			'attr_width',
			'attr_height',
			'attr_fontsize',
			'attr_target',
			'dt_created',
			'_button_history_back_'
		)
	)
);
?>