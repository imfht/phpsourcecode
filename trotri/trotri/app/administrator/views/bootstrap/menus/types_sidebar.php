<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'types_index' => array(
		'label' => 'MOD_MENUS_URLS_TYPES_INDEX',
		'm' => 'menus', 'c' => 'types', 'a' => 'index', 'active' => true,
		'icon' => array(
			'label' => 'MOD_MENUS_URLS_TYPES_CREATE',
			'm' => 'menus', 'c' => 'types', 'a' => 'create'
		)
	),
);

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>

<?php
if ($this->controller === 'types') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl('index', 'types', 'menus'),
			'elements_object' => $this->elements,
			'elements' => array(
				'type_id' => array(
					'type' => 'text',
				),
			),
			'columns' => array(
				'type_key',
				'type_name',
				'type_id',
			)
		)
	);
}
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/components.js?v=' . $this->version); ?>
