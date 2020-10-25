<?php
$settings = array(
	'entity' => array(
		'httpMethods' => 'GET',
		'pattern' => '/e_{entity:([a-z\-]{2,20})}/{id:([1-9]{1}[0-9]{0,11})}.html',
		'paths' => array(
			'controller' => 'Index',
			'action' => 'Index',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
	'entityList' => array(
		'httpMethods' => 'GET',
		'pattern' => '/el_{entity:([a-zA-Z\.\-]{2,20})}/{page:([1-9]{1}[0-9]{0,11})}',
		'paths' => array(
			'controller' => 'Index',
			'action' => 'EntityList',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
	'entityContentModelList' => array(
		'httpMethods' => 'GET',
		'pattern' => '/eml_{entity:([a-z\-]{2,20})}_{model:([a-z\-]{2,20})}/{page:([1-9]{1}[0-9]{0,11})}.html',
		'paths' => array(
			'controller' => 'Index',
			'action' => 'EntityModelList',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
	'entityModelFieldList' => array(
		'httpMethods' => null,
		'pattern' => '/emfl_{entity:([a-z\-]{2,20})}_{model:([a-z\-]{2,20})}_{field:([a-z\-]{2,20})}/{page:([1-9]{1}[0-9]{0,11})}.html',
		'paths' => array(
			'controller' => 'Index',
			'action' => 'EntityModelFieldList',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
	'adminEntityList' => array(
		'httpMethods' => null,
		'pattern' => '/' . ADMIN_PREFIX . '/e_list/{entity:([a-z\-]{2,20})}/{page:([1-9]{1}[0-9]{0,11})}.html',
		'paths' => array(
			'controller' => 'Admin',
			'action' => 'Index',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
	'adminEntityEdit' => array(
		'httpMethods' => null,
		'pattern' => '/' . ADMIN_PREFIX . '/e_edit/{entity:([a-z\-]{2,20})}/{contentModel:([0-9A-Za-z\-\_]{1,20})}/{id:([0-9a-z\-\_]{1,20})}',
		'paths' => array(
			'controller' => 'Admin',
			'action' => 'edit',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
	'adminEntityAdd' => array(
		'httpMethods' => null,
		'pattern' => '/' . ADMIN_PREFIX . '/e_add/{entity:([a-z\-]{2,20})}/{contentModel:([0-9A-Za-z\-\_]{1,20})}',
		'paths' => array(
			'controller' => 'Admin',
			'action' => 'add',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
	'adminEntityDelete' => array(
		'httpMethods' => 'GET',
		'pattern' => '/' . ADMIN_PREFIX . '/e_delete/{entity:([a-z\-]{2,20})}/{id:([0-9a-z\-\_\.]{1,20})}',
		'paths' => array(
			'controller' => 'Admin',
			'action' => 'Delete',
			'module' => 'entity',
			'namespace' => 'Modules\Entity\Controllers',
		),
	),
);
