<?php

//CMS应用参数配置
return array(
	'siteurl'=>'/',
	//内置模块ID
	'actionInfo'=>array(
		'saveSuccess'=>'信息添加成功！请继续添加。',
		'saveFail'=>'信息添加失败！请重新添加。',
		'updateSuccess'=>'信息更新成功！',
		'updateFail'=>'信息更新失败！请重新保存。',
		'deleteSuccess'=>'信息删除成功！',
		'deleteFail'=>'信息删除失败！',
	),
	'module'=>array(
		'novel'=> 1,
		'news'=> 2,
		'link'=> 3,
	),
	'pager'=>array(
		'prevPageLabel'=>'上一页',
    	'nextPageLabel'=>'下一页',
    	'header'=>'',
	),
	'pagesize'=>array(
		'book'=>10,
		'news'=>10,
	),
    'girdpagesize' => 100,
	'status'=>array(
		'isstop' => 0, //待审
        'isdelete' => -1, // 删除
		'ischecked'=> 1, // 审核通过
	),
    'role' => array(
        '1' => '超级管理员',
        '2' => '网站管理员',
        '3' => '网站会员',
    ),
    'gather_auth_key' => '52694e26e7b55',
    'urlSuffix' => array('.html', '.htm', '.shtml', '.asp', '.aspx', '.php'),
    'lockFile' => 'install.lock',
    'novelType' => array(
       '1' => '连载',
       '2' => '完本'
    ),
    'novelAdsStatus' => array(
        '0' => '未启用',
        '1' => '启用'
    ),
);
