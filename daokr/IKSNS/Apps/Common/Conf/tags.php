<?php
return array(
	//'app_init'=>array('Common\Behavior\InitHookBehavior')
	//'app_begin'   => array('Behavior\CheckLangBehavior'), //启用多语言包
	'view_filter' => array('Common\Behavior\ReplaceContentBehavior'),
);