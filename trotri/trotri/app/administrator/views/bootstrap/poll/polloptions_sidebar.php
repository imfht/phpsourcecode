<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'polls_index' => array(
		'label' => 'MOD_POLL_URLS_POLLS_INDEX',
		'm' => 'poll', 'c' => 'polls', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_POLL_URLS_POLLS_CREATE',
			'm' => 'poll', 'c' => 'polls', 'a' => 'create'
		)
	),
	'polloptions_index' => array(
		'label' => 'MOD_POLL_URLS_POLLOPTIONS_INDEX',
		'm' => 'poll', 'c' => 'polloptions', 'a' => 'index', 'active' => true,
		'params' => array('poll_id' => $this->poll_id),
	),
);
$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/components.js?v=' . $this->version); ?>
