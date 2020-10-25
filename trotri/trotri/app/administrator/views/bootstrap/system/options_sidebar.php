<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'options_modify' => array(
		'label' => 'MOD_SYSTEM_URLS_OPTIONS_MODIFY',
		'm' => 'system', 'c' => 'options', 'a' => 'modify'
	),
	'pictures_index' => array(
		'label' => 'MOD_SYSTEM_URLS_PICTURES_INDEX',
		'm' => 'system', 'c' => 'pictures', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_SYSTEM_URLS_PICTURES_CREATE',
			'm' => 'system', 'c' => 'pictures', 'a' => 'upload'
		)
	),
);

if ($this->controller === 'options') {
	$config['options_modify']['active'] = true;
}
elseif ($this->controller === 'pictures') {
	$config['pictures_index']['active'] = true;
}

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/system.js?v=' . $this->version); ?>
