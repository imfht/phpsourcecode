<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->MOD_ADVERT_URLS_ADVERTS_CREATE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('create', '', '', array('type_key' => $this->type_key)),
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
			$this->MOD_ADVERT_ADVERT_TYPES_TYPE_NAME_LABEL . ': ' . $this->type_name . '&nbsp;&nbsp;|&nbsp;&nbsp;'
		  . $this->MOD_ADVERT_ADVERT_TYPES_TYPE_KEY_LABEL  . ': ' . $this->type_key,
	)
);
?>
</form>