<?php
return array(
	/* 模块相关配置 */
	'AUTOLOAD_NAMESPACE' => array('Addons' => SENT_ADDON_PATH), //扩展模块列表

	/* 主题设置 */
	'DEFAULT_THEME' =>  'Default',  // 默认模板主题名称
	'TAGLIB_PRE_LOAD'     =>    'OT\\TagLib\\Sent',
	'TAGLIB_BEGIN'          =>  '{',  // 标签库标签开始标记
	'TAGLIB_END'            =>  '}',  // 标签库标签结束标记

	/* 用户相关设置 */
	'USER_MAX_CACHE'     => 1000, //最大缓存用户数
	'USER_ADMINISTRATOR' => 1, //管理员用户ID

	/* URL配置 */
	'URL_CASE_INSENSITIVE' => false, //默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL'            => 2, //URL模式
	'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
	'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

	/* 全局过滤配置 */
	'DEFAULT_FILTER' => '', //全局过滤函数

	/* SESSION 和 COOKIE 配置 */
	'SESSION_PREFIX' => 'sent_home', //session前缀
	'COOKIE_PREFIX'  => 'sent_home_', // Cookie前缀 避免冲突

	'LANG_SWITCH_ON' => true,   // 开启语言包功能
	'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
	'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
	'VAR_LANGUAGE'     => 'l', // 默认语言切换变量

	// 'TMPL_ACTION_ERROR'     =>  COMMON_PATH.'View/Default/Public/message.html', // 默认错误跳转对应的模板文件
	// 'TMPL_ACTION_SUCCESS'   =>  COMMON_PATH.'View/Default/Public/message.html', // 默认成功跳转对应的模板文件
);