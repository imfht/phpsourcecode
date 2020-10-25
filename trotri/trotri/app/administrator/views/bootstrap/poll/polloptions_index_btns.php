<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->MOD_POLL_URLS_POLLOPTIONS_CREATE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('create', '', '', array('poll_id' => $this->poll_id)),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_CREATE,
		'primary' => true,
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

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' =>
			$this->MOD_POLL_POLLS_POLL_NAME_LABEL . ': ' . $this->poll_name . '&nbsp;&nbsp;|&nbsp;&nbsp;'
		  . $this->MOD_POLL_POLLS_POLL_KEY_LABEL  . ': ' . $this->poll_key,
	)
);
?>
</form>