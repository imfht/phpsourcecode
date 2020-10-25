<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'modify',
		'action' => $this->getUrlManager()->getUrl($this->action, '', '', array('id' => $this->id)),
		'errors' => $this->errors,
		'values' => $this->data,
		'elements_object' => $this->elements,
		'elements' => array(
			'login_name' => array(
				'readonly' => true
			),
		),
		'columns' => array(
			'login_name',
			'realname',
			'sex',
			'birth_ymd',
			'is_pub_birth',
			'is_pub_anniversary',
			'head_portrait',
			'head_portrait_file',
			'introduce',
			'interests',
			'is_pub_interests',
			'telephone',
			'mobiphone',
			'is_pub_mobiphone',
			'email',
			'is_pub_email',
			'live_country_id',
			'live_province_id',
			'live_city_id',
			'live_district_id',
			'live_street',
			'live_zipcode',
			'address_country_id',
			'address_province_id',
			'address_city_id',
			'address_district_id',
			'address_street',
			'address_zipcode',
			'qq',
			'msn',
			'skypeid',
			'wangwang',
			'weibo',
			'blog',
			'website',
			'fax',
			'_button_save_',
			'_button_saveclose_',
			'_button_cancel_'
		)
	)
);
?>

<?php echo $this->getHtml()->js('var g_data = ' . json_encode($this->data) . ';'); ?>
