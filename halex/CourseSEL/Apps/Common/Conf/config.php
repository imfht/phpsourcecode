<?php
	return array(
	//'配置项'=>'配置值'
	'TMPL_L_DELIM'=>'<{',
	'TMPL_R_DELIM'=>'}>',
	'DEFAULT_V_LAYER' => 'Template',
	//'TMPL_FILE_DEPR' => '_',
	'SHOW_PAGE_TRACE'=>false,
	'URL_CASE_INSENSITIVE'  =>  false, 
	'URL_MODEL' => 1,
	'DEFAULT_MODULE' => 'Index',
	'SITE_NAME'=>'Siku在线OA管理系统',
		// 数据库配置信息
 	'LOAD_EXT_CONFIG' => 'db,site',
 	//***********************************SESSION设置**********************************
    'SESSION_OPTIONS'         =>  array(
        'name'                =>  'coursesel',                    //设置session名
        'expire'              =>  4*3600,                      //SESSION保存4小时
        'use_trans_sid'       =>  1,                               //跨页传递
        'use_only_cookies'    =>  0,                               //是否只开启基于cookies的session的会话方式
    ),

);
