<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'builders_index' => array(
		'label' => 'MOD_BUILDER_URLS_BUILDERS_INDEX',
		'm' => 'builder', 'c' => 'builders', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_BUILDER_URLS_BUILDERS_CREATE',
			'm' => 'builder', 'c' => 'builders', 'a' => 'create'
		)
	),
	'builders_trashindex' => array(
		'label' => 'MOD_BUILDER_URLS_BUILDERS_TRASHINDEX',
		'm' => 'builder', 'c' => 'builders', 'a' => 'trashindex'
	),
	'types' => array(
		'label' => 'MOD_BUILDER_URLS_TYPES_INDEX',
		'm' => 'builder', 'c' => 'types', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_BUILDER_URLS_TYPES_CREATE',
			'm' => 'builder', 'c' => 'types', 'a' => 'create'
		)
	)
);

if ($this->controller === 'builders') {
	if ($this->action === 'trashindex') {
		$config['builders_trashindex']['active'] = true;
	}
	else {
		$config['builders_index']['active'] = true;
	}
}
elseif ($this->controller === 'types') {
	$config['types']['active'] = true;
}

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>

<?php
if ($this->controller === 'builders') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'create',
			'action' => $this->getUrlManager()->getUrl((($this->action == 'trashindex') ? 'trashindex' : 'index'), 'builders', 'builder'),
			'elements_object' => $this->elements,
			'elements' => array(
				'builder_id' => array(
					'type' => 'text',
				),
				'tbl_profile' => array(
					'type' => 'select'
				),
				'tbl_engine' => array(
					'type' => 'select'
				),
				'tbl_charset' => array(
					'type' => 'select'
				),
			),
			'columns' => array(
				'builder_name',
				'builder_id',
				'tbl_name',
				'tbl_profile',
				'tbl_engine',
				'tbl_charset',
				'app_name'
			)
		)
	);
}
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/builder.js?v=' . $this->version); ?>
