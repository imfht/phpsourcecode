<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'fields' => array(
		'label' => 'MOD_BUILDER_URLS_FIELDS_INDEX',
		'm' => 'builder', 'c' => 'fields', 'a' => 'index',
		'params' => array('builder_id' => $this->builder_id),
	),
	'validators' => array(
		'label' => 'MOD_BUILDER_URLS_VALIDATORS_INDEX',
		'm' => 'builder', 'c' => 'validators', 'a' => 'index',
		'params' => array('field_id' => $this->field_id),
		'active' => true
	),
);

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/builder.js?v=' . $this->version); ?>
