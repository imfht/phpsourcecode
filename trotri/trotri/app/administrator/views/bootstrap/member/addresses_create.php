<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'member_id' => array(
				'value' => $this->member_id
			),
		),
		'columns' => array(
			'consignee',
			'mobiphone',
			'telephone',
			'email',
			'addr_country_id',
			'addr_province_id',
			'addr_city_id',
			'addr_district_id',
			'addr_street',
			'addr_zipcode',
			'when',
			'is_default',
			'dt_created',
			'dt_last_modified',
			'member_id',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>

<?php echo $this->getHtml()->js('var g_data = ' . json_encode($_POST) . ';'); ?>
