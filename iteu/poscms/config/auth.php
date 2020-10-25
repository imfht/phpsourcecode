<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  后台权限控制
 *
 */

$config['auth'][] = array(
	'auth' => array(
		'admin/html/index' => fc_lang('生成静态'),
		'admin/notice/index' => fc_lang('系统提醒'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/system/oplog' => fc_lang('日志'),
        'admin/db/sql' => fc_lang('执行SQL'),
		'admin/system/index' => fc_lang('系统配置'),
		'admin/system/file' => fc_lang('分离存储'),
        'admin/db/index' => fc_lang('数据结构'),
		'admin/upgrade/index' => fc_lang('内核升级'),
		'admin/upgrade/branch' => fc_lang('程序升级'),
        'admin/check/index' => fc_lang('系统体检'),
        'admin/route/index' => fc_lang('生成伪静态'),
		'admin/cron/index' => fc_lang('任务队列'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/mail/index' => fc_lang('邮件系统'),
		'admin/mail/add' => fc_lang('添加'),
		'admin/mail/edit' => fc_lang('修改'),
		'admin/mail/del' => fc_lang('删除'),
		'admin/mail/send' => fc_lang('发送邮件'),
		'admin/mail/log' => fc_lang('日志'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/sms/index' => fc_lang('短信系统'),
		'admin/sms/send' => fc_lang('发送短信'),
		'admin/sms/log' => fc_lang('日志'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/menu/index' => fc_lang('后台菜单'),
		'admin/menu/add' => fc_lang('添加'),
		'admin/menu/edit' => fc_lang('修改'),
		'admin/menu/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/sysvar/index' => fc_lang('全局变量'),
		'admin/sysvar/add' => fc_lang('添加'),
		'admin/sysvar/edit' => fc_lang('修改'),
		'admin/sysvar/del' => fc_lang('删除'),
	)
);
$config['auth'][] = array(
	'auth' => array(
		'admin/syscontroller/index' => fc_lang('自定义控制器'),
		'admin/syscontroller/add' => fc_lang('添加'),
		'admin/syscontroller/edit' => fc_lang('修改'),
		'admin/syscontroller/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/attachment2/index' => fc_lang('远程附件'),
		'admin/attachment2/add' => fc_lang('添加'),
		'admin/attachment2/edit' => fc_lang('修改'),
		'admin/attachment2/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/role/index' => fc_lang('角色管理'),
		'admin/role/auth' => fc_lang('权限划分'),
		'admin/role/add' => fc_lang('添加'),
		'admin/role/edit' => fc_lang('修改'),
		'admin/role/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/root/index' => fc_lang('管理员管理'),
		'admin/root/log' => fc_lang('登录日志'),
		'admin/root/add' => fc_lang('添加'),
		'admin/root/edit' => fc_lang('修改'),
		'admin/root/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/verify/index' => fc_lang('审核流程'),
		'admin/verify/add' => fc_lang('添加'),
		'admin/verify/edit' => fc_lang('修改'),
		'admin/verify/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/site/index' => fc_lang('网站管理'),
		'admin/site/add' => fc_lang('添加'),
		'admin/site/config' => lang('配置'),
		'admin/site/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/navigator/index' => fc_lang('网站导航'),
		'admin/navigator/add' => fc_lang('添加'),
		'admin/navigator/edit' => fc_lang('修改'),
		'admin/navigator/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/field/index' => fc_lang('字段管理'),
		'admin/field/add' => fc_lang('添加'),
		'admin/field/edit' => fc_lang('修改'),
		'admin/field/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/application/index' => fc_lang('应用管理'),
		'admin/application/store' => fc_lang('商店'),
		'admin/application/config' => fc_lang('配置'),
		'admin/application/install' => fc_lang('安装'),
		'admin/application/uninstall' => fc_lang('卸载'),
		'admin/application/delete' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/module/index' => fc_lang('模块管理'),
		'admin/module/store' => fc_lang('商店'),
		'admin/module/install' => fc_lang('安装'),
		'admin/module/uninstall' => fc_lang('卸载'),
		'admin/module/delete' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/attachment/index' => fc_lang('附件管理'),
		'admin/attachment/unused' => fc_lang('未使用的附件'),
		'admin/attachment/del' => fc_lang('删除'),
	)
);


$config['auth'][] = array(
	'auth' => array(
		'admin/page/index' => fc_lang('单页管理'),
		'admin/page/add' => fc_lang('添加'),
		'admin/page/edit' => fc_lang('修改'),
		'admin/page/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/linkage/index' => fc_lang('联动菜单'),
		'admin/linkage/add' => fc_lang('添加'),
		'admin/linkage/edit' => fc_lang('修改'),
		'admin/linkage/data' => fc_lang('子菜单'),
		'admin/linkage/adds' => fc_lang('添加子菜单'),
		'admin/linkage/edits' => fc_lang('修改子菜单'),
		'admin/linkage/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/block/index' => fc_lang('资料块'),
		'admin/block/add' => fc_lang('添加'),
		'admin/block/edit' => fc_lang('修改'),
		'admin/block/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/form/index' => fc_lang('表单管理'),
		'admin/form/add' => fc_lang('添加'),
		'admin/form/edit' => fc_lang('修改'),
		'admin/form/del' => fc_lang('删除'),
		'admin/form/listc' => fc_lang('内容维护'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/downservers/index' => fc_lang('下载镜像'),
		'admin/downservers/add' => fc_lang('添加'),
		'admin/downservers/edit' => fc_lang('修改'),
		'admin/downservers/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'admin/tpl/index' => fc_lang('模板管理'),
		'admin/tpl/mobile' => fc_lang('移动端模板'),
		'admin/tpl/add' => fc_lang('添加'),
		'admin/tpl/edit' => fc_lang('修改'),
		'admin/tpl/del' => fc_lang('删除'),
		'admin/tpl/tag' => fc_lang('标签向导'),
	)
);

$config['auth'][] = array(
	'name' => fc_lang('风格管理'),
	'auth' => array(
		'admin/theme/index' => fc_lang('风格管理'),
		'admin/theme/add' => fc_lang('添加'),
		'admin/theme/edit' => fc_lang('修改'),
		'admin/theme/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
    'name' => fc_lang('URL规则'),
    'auth' => array(
        'admin/urlrule/index' => fc_lang('URL规则'),
        'admin/urlrule/add' => fc_lang('添加'),
        'admin/urlrule/edit' => fc_lang('修改'),
        'admin/urlrule/del' => fc_lang('删除'),
    )
);

$config['auth'][] = array(
	'auth' => array(
		'member/admin/home/index' => fc_lang('会员管理'),
		'member/admin/home/add' => fc_lang('添加'),
		'member/admin/home/edit' => fc_lang('修改'),
		'member/admin/home/del' => fc_lang('删除'),
        'member/admin/home/score' => SITE_SCORE,
		'member/admin/home/experience' => SITE_EXPERIENCE,
	)
);

$config['auth'][] = array(
	'auth' => array(
		'member/admin/group/index' => fc_lang('会员组模型'),
		'member/admin/group/add' => fc_lang('添加'),
		'member/admin/group/edit' => fc_lang('修改'),
		'member/admin/group/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'member/admin/level/index' => fc_lang('等级管理'),
		'member/admin/level/add' => fc_lang('添加'),
		'member/admin/level/edit' => fc_lang('修改'),
		'member/admin/level/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'member/admin/setting/oauth' => 'OAuth2',
		'member/admin/setting/index' => fc_lang('功能配置'),
		'member/admin/setting/permission' => fc_lang('权限划分'),
		'member/admin/setting/pay' => fc_lang('网银配置'),
		'space/admin/setting/space' => fc_lang('空间配置'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'member/admin/menu/index' => fc_lang('会员菜单'),
		'member/admin/menu/add' => fc_lang('添加'),
		'member/admin/menu/edit' => fc_lang('修改'),
		'member/admin/menu/del' => fc_lang('删除'),
	)
);


$config['auth'][] = array(
	'auth' => array(
		'member/admin/tpl/index' => fc_lang('模板管理'),
        'member/admin/tpl/mobile' => fc_lang('移动端模板'),
		'member/admin/tpl/add' => fc_lang('添加'),
		'member/admin/tpl/edit' => fc_lang('修改'),
		'member/admin/tpl/del' => fc_lang('删除'),
		'member/admin/tpl/tag' => fc_lang('标签向导'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'member/admin/theme/index' => fc_lang('风格管理'),
		'member/admin/theme/add' => fc_lang('添加'),
		'member/admin/theme/edit' => fc_lang('修改'),
		'member/admin/theme/del' => fc_lang('删除'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'space/admin/space/index' => fc_lang('空间管理'),
		'space/admin/space/edit' => fc_lang('修改'),
		'space/admin/space/del' => fc_lang('删除'),
		'space/admin/space/init' => fc_lang('默认栏目'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'space/admin/spacetpl/index' => fc_lang('空间模板'),
		'space/admin/spacetpl/add' => fc_lang('添加'),
		'space/admin/spacetpl/edit' => fc_lang('修改'),
		'space/admin/spacetpl/del' => fc_lang('删除'),
		'space/admin/spacetpl/permission' => fc_lang('权限划分'),
	)
);

$config['auth'][] = array(
	'auth' => array(
		'space/admin/model/index' => fc_lang('空间模型'),
		'space/admin/model/add' => fc_lang('添加'),
		'space/admin/model/edit' => fc_lang('修改'),
		'space/admin/model/del' => fc_lang('删除')
	)
);

$config['auth'][] = array(
	'auth' => array(
		'space/admin/content/index' => fc_lang('模型内容'),
		'space/admin/content/edit' => fc_lang('审核'),
		'space/admin/content/del' => fc_lang('删除')
	)
);


$config['auth'][] = array(
	'auth' => array(
		'member/admin/pay/index' => fc_lang('财务流水'),
		'member/admin/pay/add' => fc_lang('充值'),
	)
);

$config['auth'][] = array(
    'auth' => array(
        'space/admin/sns/index' => fc_lang('动态管理'),
        'space/admin/sns/topic' => fc_lang('话题管理'),
    )
);