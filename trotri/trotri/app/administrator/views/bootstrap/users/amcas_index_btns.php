<form class="form-inline">
<?php foreach ($this->apps as $appId => $prompt) : ?>
<?php
$createIcon = $this->getHtml()->tag(
	'span',
	array(
		'data-original-title' => $this->MOD_USERS_URLS_AMCAS_MODCREATE,
		'onclick' => 'return Trotri.href(\'' . $this->getUrlManager()->getUrl('create', '', '', array('amca_pid' => $appId)) . '\')',
		'class' => 'glyphicon glyphicon-' . views\bootstrap\components\ComponentsConstant::GLYPHICON_CREATE,
		'data-toggle' => 'tooltip',
		'data-placement' => 'left'
	),
	''
);

echo $this->getHtml()->a(
	$createIcon . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $prompt,
	$this->getUrlManager()->getUrl('index', '', '', array('app_id' => $appId)),
	array(
		'class' => 'btn btn-' . (($this->app_id == $appId) ? 'primary' : 'default')
	)
);
?>
&nbsp;
<?php endforeach; ?>
</form>