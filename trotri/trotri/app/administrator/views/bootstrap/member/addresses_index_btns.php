<form class="form-inline">
<?php
$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' => $this->MOD_MEMBER_URLS_ADDRESSES_CREATE,
		'jsfunc' => \views\bootstrap\components\ComponentsConstant::JSFUNC_HREF,
		'url' => $this->getUrlManager()->getUrl('create', '', '', array('member_id' => $this->member_id)),
		'glyphicon' => \views\bootstrap\components\ComponentsConstant::GLYPHICON_CREATE,
		'primary' => true,
	)
);

$this->widget(
	'views\bootstrap\widgets\ButtonBuilder',
	array(
		'label' =>
		$this->MOD_MEMBER_MEMBER_PORTAL_LOGIN_NAME_LABEL . ': ' . $this->login_name . '&nbsp;&nbsp;|&nbsp;&nbsp;'
	  . $this->MOD_MEMBER_MEMBER_PORTAL_MEMBER_ID_LABEL  . ': ' . $this->member_id,
	)
);
?>
</form>