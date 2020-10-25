<?php
$this->widget('views\bootstrap\widgets\ViewBuilder',
	array(
		'name' => 'view',
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
		),
		'columns' => array(
			'address_id',
			'address_name',
			'member_id',
			'consignee',
			'mobiphone',
			'telephone',
			'email',
			'addr_country',
			'addr_province',
			'addr_city',
			'addr_district',
			'addr_street',
			'addr_zipcode',
			'when',
			'is_default',
			'dt_created',
			'dt_last_modified',
			'_button_history_back_'
		)
	)
);
?>