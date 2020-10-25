<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'www.918.mx',
	'language'=>'zh_cn',
	// preloading 'log' component
	'preload'=>array('log'),
	
	// autoloading model and component classes
	'viewPath'=>'views',
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),
	
	// application components
	'components'=>array(
		/*
		* sae的key
		*/
		'SAEOAuth' => array(
            'WB_AKEY' => 'jkonomnjjj',
            'WB_SKEY' => 'l0l51mm5ykm3ll4h5z1hx3hz0lm31jlwyimwh21i',
            'callback' => '/site/callback',
            'class'=>'SAEOAuth',
        ),
		'request'=>array(
            //Cookie攻击的防范
            'enableCookieValidation'=>true,
            //跨站请求伪造(简称CSRF)攻击 防范
            #'enableCsrfValidation'=>true,
        ),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		'mailer' => array(
			'class' => 'application.extensions.mailer.EMailer',
			'pathViews' => 'application.views.email',
			'pathLayouts' => 'application.views.email.layouts'
		 ),
		
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
			'showScriptName' => false,
		),
		
		// uncomment the following to use a MySQL database

		'db'=>array(
			#'class'=>'SAEDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=games',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix'=>'renyu_',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    //SAE 不支持直接本地IO 改为db记录 
                    'class'=>'CDbLogRoute',
                    'connectionID'=>'db',
                    'levels'=>'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                */
                
            ),
        ), 


	),

	'params'=>require(dirname(__FILE__) . '/params.php'),
);
//如果定义了常量，则默认为在SAE环境中
if(defined('SAE_TMP_PATH'))
{
    //SAE 不支持I/O
    $config['runtimePath'] = SAE_TMP_PATH;
    //配置为 SAEDbConnection 则不必考虑用户名密码 并自动读写分离
    $config['components']['db'] = array(
            'class'=>'SAEDbConnection',
            'charset' => 'utf8',
        'tablePrefix'=>'renyu_',
            'emulatePrepare' => true,
            //开启sql 记录
            'enableProfiling'=>true,
            'enableParamLogging'=>true,
            //cache
            'schemaCachingDuration'=>3600,
    );
    //SAE不支持I/O 使用storage 存储 assets。 如果在正式环境，请将发布到assets的css/js做合并，直接放到app目录下，storage的分钟限额为5000，app为200000
    //最新的SAE 不使用storage 而是在siteController中，导入了一个SAEAssetsAction，通过 site/assets?path=aaa.txt ，将文件内容输出到web端，来访问实际的 aaa.txt 文件， 
    $config['components']['assetManager'] = array('class' => 'SAEAssetManager','domain'=> 'assets');
    //如果没有必要，不用修改缓存配置。 SAE不支持本地文件的IO处理 已经提供了memcache
    $config['components']['cache'] = array(
            'class'=> 'SAEMemCache',
            'servers'=>array(
                array('host'=>'localhost', 'port'=>11211, 'weight'=>100),
            ),
        );

}
return $config;