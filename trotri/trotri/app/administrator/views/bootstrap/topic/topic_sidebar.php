<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'topic_index' => array(
		'label' => 'MOD_TOPIC_URLS_TOPIC_INDEX',
		'm' => 'topic', 'c' => 'topic', 'a' => 'index', 'active' => true,
		'icon' => array(
			'label' => 'MOD_TOPIC_URLS_TOPIC_CREATE',
			'm' => 'topic', 'c' => 'topic', 'a' => 'create'
		)
	),
);

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>

<?php
if ($this->controller === 'topic') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl('index', 'topic', 'topic'),
			'elements_object' => $this->elements,
			'elements' => array(
				'topic_id' => array(
					'type' => 'text',
				),
				'is_published' => array(
					'type' => 'select',
				),
			),
			'columns' => array(
				'topic_key',
				'topic_name',
				'is_published',
				'topic_id',
			)
		)
	);
}
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->cssFile($this->static_url . '/plugins/jquery-upload-file/uploadpreviewimg.css'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/jquery-upload-file/jquery.uploadfile.min.js'); ?>

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/components.js?v=' . $this->version); ?>
