<?php
return array(
	/* 模块控制 */
	'DEFAULT_MODULE' => 'Admin',
	'MODULE_DENY_LIST' => array('Common, CigoAdminLib'),

	'URL_CASE_INSENSITIVE' => false,
	'URL_MODEL' => 2,
	'VAR_URL_PARAMS' => '',
	'URL_PATHINFO_DEPR' => '/',

	/* 模板相关 */
	'TMPL_L_DELIM' => '<<{',
	'TMPL_R_DELIM' => '}>>',

//    //自动命名空间，配置CigoAdmin插件
//    'AUTOLOAD_NAMESPACE'=> array(
//        'CigoAdminLib'=>APP_PATH.'Common/Lib/CigoAdminLib'
//    ),

	/* 扩展配置 */
//    'LOAD_EXT_CONFIG' => 'cigo,db,upload,settings,order,pay',
	'LOAD_EXT_CONFIG' => 'db,cigo,upload',
);