<?php
$settings = array(
	'formId' => 'nodeAddForm',
	'form' => array(
		'action' => '',
		'method' => 'post',
		'class' => array(),
		'accept-charset' => 'utf-8',
		'role' => 'form',
		'id' => 'formRoleAddForm',
	),
	'tel' => array(
		'type' => 'field',
		'label' => '电话',
		'userOptions' => array(
			'labelAttributes' => array(
				'class' => array(),
			),
			'groupAttributes' => array(
				'class' => array(),
				'id' => 'group_name',
			),
			'widgetBoxAttributes' => array(
				'class' => array(),
			),
			'helpAttributes' => array(
				'class' => array(),
			),
		),
		'settings' => array(
			'mainTable' => 0,
			'default' => '',
		),
		'error' => '',
		'description' => '网站电话',
		'field' => 'string',
		'widget' => 'Text',
		'validate' => array(),
		'attributes' => array(),
		'required' => true,
	),
	'body' => array(
		'label' => '内容',
		'type' => 'field',
		'description' => '内容主体',
		'field' => 'textLong',
		'userOptions' => array(
			'labelAttributes' => array(
				'class' => array(),
			),
			'groupAttributes' => array(
				'class' => array(),
				'id' => 'group_description',
			),
			'widgetBoxAttributes' => array(
				'class' => array(),
			),
			'helpAttributes' => array(
				'class' => array(),
			),
			'wordsmiths' => true,
		),
		'settings' => array(
			'mainTable' => 1,
			'default' => '',
		),
		'error' => '',
		'widget' => 'Textarea',
		'required' => true,
		'validate' => array(),
		'attributes' => array(),
	),
	'other' => array(
		'type' => 'field',
		'label' => '其他选项',
		'userOptions' => array(
			'labelAttributes' => array(
				'class' => array(),
			),
			'groupAttributes' => array(
				'class' => array(),
				'id' => 'group_name',
			),
			'widgetBoxAttributes' => array(
				'class' => array(),
			),
			'helpAttributes' => array(
				'class' => array(),
			),
		),
		'settings' => array(
			'mainTable' => 1,
			'default' => '',
		),
		'error' => '',
		'description' => '一行一个',
		'field' => 'textLong',
		'widget' => 'Textarea',
		'validate' => array(),
		'attributes' => array(),
	),
	'cat' => array(
		'label' => '分类',
		'type' => 'field',
		'description' => '内容分类',
		'field' => 'list',
		'userOptions' => array(
			'labelAttributes' => array(
				'class' => array(),
			),
			'groupAttributes' => array(
				'class' => array(),
				'id' => 'group_description',
			),
			'widgetBoxAttributes' => array(
				'class' => array(),
			),
			'helpAttributes' => array(
				'class' => array(),
			),
		),
		'settings' => array(
			'mainTable' => false,
			'init' => array('\Modules\Taxonomy\Library\Common', 'formTermOptions'),
			'save' => array('\Modules\Node\Library\Common', 'saveNodeTerm'),
			'delete' => '\Modules\Taxonomy\Library\Common::delete',
			'field' => 'cat',
			'valueType' => 'id',
			'module' => 'node',
			'type' => 'page',
			'maxNum' => 1,
			'createTerm',
			'taxonomy' => 'page_cat',
			'mainTable' => 2,
			'parent' => 0,
		),
		'error' => '',
		'options' => array(),
		'termOptions' => array(
			'term' => 'cat',
		),
		'widget' => 'Select',
		'required' => true,
		'validate' => array(),
		'attributes' => array(
			'class' => 'width-100',
		),
	),
	'settings' => array(
		'save' => array('\Modules\Node\Library\Common', 'save'),
		'etype' => 'node.page',
		'type' => 'page',
		'module' => 'node',
		'entityTableColumns' => array('e.body'),
	),
);
