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
	),
	'fields' => array(
		'label' => 'MOD_BUILDER_URLS_FIELDS_INDEX',
		'm' => 'builder', 'c' => 'fields', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_BUILDER_URLS_FIELDS_CREATE',
			'm' => 'builder', 'c' => 'fields', 'a' => 'create'
		)
	),
	'groups' => array(
		'label' => 'MOD_BUILDER_URLS_GROUPS_INDEX',
		'm' => 'builder', 'c' => 'groups', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_BUILDER_URLS_GROUPS_CREATE',
			'm' => 'builder', 'c' => 'groups', 'a' => 'create'
		)
	),
);

if ($this->builder_id > 0) {
	$config['fields']['params']['builder_id'] = $this->builder_id;
	$config['groups']['params']['builder_id'] = $this->builder_id;
	$config['fields']['icon']['params']['builder_id'] = $this->builder_id;
	$config['groups']['icon']['params']['builder_id'] = $this->builder_id;
}

if ($this->controller === 'builders') {
	if ($this->action === 'trashindex') {
		$config['index_trashindex']['active'] = true;
	}
	else {
		$config['index_index']['active'] = true;
	}
}
elseif ($this->controller === 'types') {
	$config['types']['active'] = true;
}
elseif ($this->controller === 'fields') {
	$config['fields']['active'] = true;
}
elseif ($this->controller === 'groups') {
	$config['groups']['active'] = true;
}

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/builder.js?v=' . $this->version); ?>
