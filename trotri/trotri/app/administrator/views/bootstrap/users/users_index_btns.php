<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->MOD_USERS_URLS_USERS_CREATE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('create', '', ''),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_CREATE,
		'primary' => true,
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_FORBIDDEN,
		'jsfunc' => 'Users.batchForbidden',
		'url' => $this->getUrlManager()->getUrl('singlemodify', '', '', array('is_batch' => 1)),
		'glyphicon' => 'lock',
		'primary' => false,
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_UNFORBIDDEN,
		'jsfunc' => 'Users.batchUnforbidden',
		'url' => $this->getUrlManager()->getUrl('singlemodify', '', '', array('is_batch' => 1)),
		'glyphicon' => 'open',
		'primary' => false,
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_TRASH,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_DIALOGBATCHTRASH,
		'url' => $this->getUrlManager()->getUrl('trash', '', '', array('is_batch' => 1)),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_TRASH,
	)
);
?>
</form>