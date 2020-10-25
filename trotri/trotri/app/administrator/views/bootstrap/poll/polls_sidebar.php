<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'polls_index' => array(
		'label' => 'MOD_POLL_URLS_POLLS_INDEX',
		'm' => 'poll', 'c' => 'polls', 'a' => 'index', 'active' => true,
		'icon' => array(
			'label' => 'MOD_POLL_URLS_POLLS_CREATE',
			'm' => 'poll', 'c' => 'polls', 'a' => 'create'
		)
	),
);
$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>

<?php
if ($this->controller === 'polls') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl('index', 'polls', 'poll'),
			'elements_object' => $this->elements,
			'elements' => array(
				'allow_unregistered' => array(
					'type' => 'select',
				),
				'is_published' => array(
					'type' => 'select',
				),
				'poll_id' => array(
					'type' => 'text',
				),
			),
			'columns' => array(
				'poll_key',
				'poll_name',
				'allow_unregistered',
				'is_published',
				'poll_id',
			)
		)
	);
}
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/components.js?v=' . $this->version); ?>
