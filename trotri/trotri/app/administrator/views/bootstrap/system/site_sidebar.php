<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'site_index' => array(
		'label' => 'MOD_SYSTEM_URLS_SITE_INDEX',
		'm' => 'system', 'c' => 'site', 'a' => 'index'
	),
	'tools_cacheclear' => array(
		'label' => 'MOD_SYSTEM_URLS_TOOLS_CACHECLEAR',
		'm' => 'system', 'c' => 'tools', 'a' => 'cacheclear'
	),
);

if ($this->controller === 'site') {
	$config['site_index']['active'] = true;
}
elseif ($this->controller === 'tools') {
	if ($this->action === 'cacheclear') {
		$config['tools_cacheclear']['active'] = true;
	}
}

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/system.js?v=' . $this->version); ?>
