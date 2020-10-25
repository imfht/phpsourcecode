<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_PARSE_STRING'  =>array(     
		'__PUBLIC__' => __ROOT__, // 更改默认的/Public 替换规则  
	),
	'URL_MODEL'=>0,
	'TMPL_ACTION_ERROR'		=> 'Public:error',
	'TMPL_ACTION_SUCCESS'	=> 'Public:success',
	'LAYOUT_ON'				=> true,
	'MENU_LIST'		=> array(
		
		'3'			=> array(
			1	=> array(
				'ico'	=> 'icon-wifi',
				'name'	=> '路由管理',
				'url'	=> 'Router/index',
				'sub'	=> array(
					1	=> array(
						'url'	=> 'Router/router_wifi_config',
						'name'	=> '配置下发',
						'status'=> 1,
					),
					
				),
			),
			2	=> array(
				'ico'	=> 'icon-desktop',
				'name'	=> '认证设置',
				'url'	=> 'Merchant/auth_set',
				'status'=> 1,
			),
			3	=> array(
				'ico'	=> 'icon-user',
				'name'	=> '在线用户列表',
				'url'	=> 'Client/onlineuser',
				'status'=> 1,
			),
			4	=> array(
				'ico'	=> 'icon-history',
				'name'	=> '历史用户列表',
				'url'	=> 'Client/userlist',
				'status'=> 1,
			),
			5	=> array(
				'ico'	=> 'icon-history',
				'name'	=> '历史认证记录',
				'url'	=> 'Client/signinlog',
				'status'=> 1,
			),
			
			6	=> array(
				'url'	=> '#',
				'ico'	=> 'icon-home',
				'name'	=> '微站管理',
				'status'=> 1,
				'sub'	=> array(
					0	=> array(
						'url'	=> 'Station/nav',
						'name'	=> '导航菜单管理',
						'status'=> 1,
					),
					1	=> array(
						'url'	=> 'Station/slide',
						'name'	=> '幻灯片管理',
						'status'=> 1,
					),
					2	=> array(
						'url'	=> 'Station/about_us',
						'name'	=> '关于我们',
						'status'=> 1,
					),
					3	=> array(
						'url'	=> 'Station/new_list',
						'name'	=> '新闻中心',
						'status'=> 1,
					),
					4	=> array(
						'url'	=> 'Station/product',
						'name'	=> '产品展示',
						'status'=> 1,
					),
					5	=> array(
						'url'	=> 'Station/contact_us',
						'name'	=> '联系我们',
						'status'=> 1,
					),
					6	=> array(
						'url'	=> 'Station/activity',
						'name'	=> '活动中心',
						'status'=> 1,
					),
				),
			),
			7	=> array(
				'url'	=> '#',
				'ico'	=> 'icon-phone',
				'name'	=> '短信日志',
				'status'=> 1,
				'sub'	=> array(
					0	=> array(
						'url'	=> 'Sms/phone_list',
						'name'	=> '手机用户管理',
						'status'=> 1,
					),
					1	=> array(
						'url'	=> 'Sms/sms_log_list',
						'name'	=> '短信发送纪录',
						'status'=> 1,
					),
				),
			),
			8	=> array(
				'state'	=> 'index.system',
				'ico'	=> 'icon-cog',
				'name'	=> '系统管理',
				'url'	=> '#/index/system',
				'status'=> 1,
				'sub'	=> array(
					0	=> array(
						'url'	=> 'Config/index',
						'name'	=> '网站设置',
						'status'=> 1,
					),
					1	=> array(
						'url'	=> 'Config/auth',
						'name'	=> '认证设置',
						'status'=> 1,
					),
					2	=> array(
						'url'	=> 'Config/sms',
						'name'	=> '短信设置',
						'status'=> 1,
					),
					/*3	=> array(
						'url'	=> 'Backup/index',
						'name'	=> '数据备份与恢复',
						'status'=> 1,
					),*/
					/*3	=> array(
						'url'	=> '#/index/system/firmware',
						'state'	=> 'index.system.firmware',
						'name'	=> '固件管理',
						'status'=> 1,
					),*/
				),
			),
		),
	),
);