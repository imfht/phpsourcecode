<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_RESTORE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_BATCHRESTORE,
		'url' => $this->getUrlManager()->getUrl('trash', '', '', array('is_batch' => 1, 'is_restore' => 1)),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_RESTORE,
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_BATCH_REMOVE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_DIALOGBATCHREMOVE,
		'url' => $this->getUrlManager()->getUrl('remove', '', '', array('is_batch' => 1)),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_REMOVE,
	)
);
?>
</form>