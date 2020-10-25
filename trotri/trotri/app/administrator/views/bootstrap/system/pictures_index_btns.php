<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_UPLOAD,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('upload', '', ''),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_UPLOAD,
		'primary' => true,
		
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->CFG_SYSTEM_GLOBAL_HISTORY_BACK,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('index', '', '', array('directory' => substr($this->directory, 0, 6))),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_HISTORYBACK,
		'primary' => false,
	)
);
?>
</form>