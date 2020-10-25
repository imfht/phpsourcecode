<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * �����̨Ȩ��ѡ��
 *
 * ��ʽ��$config['auth'][] = array('name' => ѡ�����, 'auth' => array(uri => Ȩ�����, ....)) , ...
 * URI˵����ģ��/Ӧ��URI����Ҫ����ģ��/Ӧ�õ�Ŀ¼���
 *
 */

$config['auth'][] = array(
	'name' => fc_lang('内容管理'),
	'auth' => array(
		'admin/home/index' => fc_lang('管理'),
		'admin/home/add' => fc_lang('添加'),
		'admin/home/edit' => fc_lang('修改'),
		'admin/home/del' => fc_lang('删除'),
		'admin/home/verify' => fc_lang('审核'),
        'admin/home/content' => fc_lang('内容维护'),
		'admin/home/html' => fc_lang('生成静态'),
        'admin/home/draft' => fc_lang('草稿箱'),
	)
);

$config['auth'][] = array(
	'name' => fc_lang('栏目管理'),
	'auth' => array(
		'admin/category/index' => fc_lang('管理'),
		'admin/category/add' => fc_lang('添加'),
		'admin/category/edit' => fc_lang('修改'),
		'admin/category/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'name' => fc_lang('单页管理'),
	'auth' => array(
		'admin/page/index' => fc_lang('管理'),
		'admin/page/add' => fc_lang('添加'),
		'admin/page/edit' => fc_lang('修改'),
		'admin/page/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'name' => fc_lang('Tag标签'),
	'auth' => array(
		'admin/tag/index' => fc_lang('管理'),
		'admin/tag/add' => fc_lang('添加'),
		'admin/tag/edit' => fc_lang('修改'),
		'admin/tag/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'name' => fc_lang('风格管理'),
	'auth' => array(
		'admin/theme/index' => fc_lang('管理'),
		'admin/theme/add' => fc_lang('添加'),
		'admin/theme/edit' => fc_lang('修改'),
		'admin/theme/del' => fc_lang('删除'),
	)
);


$config['auth'][] = array(
	'name' => fc_lang('模板管理'),
	'auth' => array(
		'admin/tpl/index' => fc_lang('管理'),
		'admin/tpl/add' => fc_lang('添加'),
		'admin/tpl/edit' => fc_lang('修改'),
		'admin/tpl/del' => fc_lang('删除'),
		'admin/tpl/tag' => fc_lang('标签向导'),
	)
);