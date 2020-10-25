<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->MOD_POSTS_URLS_POSTS_CREATE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('create', '', ''),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_CREATE,
		'primary' => true,
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

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_PUBLISH,
		'jsfunc' => 'Posts.batchPublish',
		'url' => $this->getUrlManager()->getUrl('singlemodify', '', '', array('is_batch' => 1)),
		'glyphicon' => 'eye-open',
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_UNPUBLISH,
		'jsfunc' => 'Posts.batchUnpublish',
		'url' => $this->getUrlManager()->getUrl('singlemodify', '', '', array('is_batch' => 1)),
		'glyphicon' => 'eye-close',
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_MODIFY_SORT,
		'jsfunc' => 'Core.batchModifySort',
		'url' => $this->getUrlManager()->getUrl('batchmodifysort', '', ''),
		'glyphicon' => 'pencil',
		'primary' => false,
	)
);
?>
</form>