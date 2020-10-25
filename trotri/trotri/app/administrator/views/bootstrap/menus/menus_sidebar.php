<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'types_index' => array(
		'label' => 'MOD_MENUS_URLS_TYPES_INDEX',
		'm' => 'menus', 'c' => 'types', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_MENUS_URLS_TYPES_CREATE',
			'm' => 'menus', 'c' => 'types', 'a' => 'create'
		)
	),
	'menus_index' => array(
		'label' => 'MOD_MENUS_URLS_MENUS_INDEX',
		'm' => 'menus', 'c' => 'menus', 'a' => 'index', 'active' => true,
		'params' => array('type_key' => $this->type_key),
	),
);

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/components.js?v=' . $this->version); ?>
