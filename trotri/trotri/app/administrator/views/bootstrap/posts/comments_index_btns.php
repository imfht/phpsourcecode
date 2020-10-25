<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_REMOVE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_DIALOGBATCHREMOVE,
		'url' => $this->getUrlManager()->getUrl('remove', '', '', array('is_batch' => 1)),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_REMOVE,
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
?>
</form>