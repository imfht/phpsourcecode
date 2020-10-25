<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->MOD_MENUS_URLS_MENUS_CREATE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('create', '', '', array('type_key' => $this->type_key)),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_CREATE,
		'primary' => true,
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' =>
			$this->MOD_MENUS_MENU_TYPES_TYPE_NAME_LABEL . ': ' . $this->type_name . '&nbsp;&nbsp;|&nbsp;&nbsp;'
		  . $this->MOD_MENUS_MENU_TYPES_TYPE_KEY_LABEL  . ': ' . $this->type_key,
	)
);
?>
</form>