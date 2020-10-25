<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$this->widget('views\bootstrap\widgets\SearchBuilder', 
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl('index', $this->controller, $this->module),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'already_gb' => array(
				'type' => 'select'
			)
		),
		'columns' => array(
			'stbl_name',
			'already_gb',
		)
	)
);
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/builder.js?v=' . $this->version); ?>