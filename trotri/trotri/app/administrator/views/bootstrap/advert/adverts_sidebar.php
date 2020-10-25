<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'types_index' => array(
		'label' => 'MOD_ADVERT_URLS_TYPES_INDEX',
		'm' => 'advert', 'c' => 'types', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_MENUS_URLS_TYPES_CREATE',
			'm' => 'advert', 'c' => 'types', 'a' => 'create'
		)
	),
	'adverts_index' => array(
		'label' => 'MOD_ADVERT_URLS_ADVERTS_INDEX',
		'm' => 'advert', 'c' => 'adverts', 'a' => 'index', 'active' => true,
		'params' => array('type_key' => $this->type_key),
	),
);

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->cssFile($this->static_url . '/plugins/jquery-upload-file/uploadpreviewimg.css'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/jquery-upload-file/jquery.uploadfile.min.js'); ?>

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/components.js?v=' . $this->version); ?>
