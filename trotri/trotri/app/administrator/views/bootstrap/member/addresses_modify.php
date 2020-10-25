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
			'address_name',
			'member_id',
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
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>

<?php echo $this->getHtml()->js('var g_data = ' . json_encode($this->data) . ';'); ?>
