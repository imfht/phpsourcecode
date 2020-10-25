<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'',
	'language'=>'zh_cn',
	// preloading 'log' component
	'preload'=>array('log'),
    'runtimePath'=> dirname(dirname(dirname(__FILE__))) .'/runtime/front',
//	'viewPath'=>'views',
	// autoloading model and component classes
	'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.extensions.image.*',
	),
	
//	'modules'=>array(
//		// uncomment the following to enable the Gii tool
//		
//		'gii'=>array(
//			'class'=>'system.gii.GiiModule',
//			'password'=>'123456',
//		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
//			//'ipFilters'=>array('127.0.0.1','::1'),
//		),
//        'api' => array(
//            'modules' => array (
//                'misc',
//             )
//        ),
//		
//	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		
//		'urlManager'=>array(
//			'urlFormat'=>'path',
//			'urlSuffix'=>'.html', 
//			'showScriptName'=> false,
//			'rules'=>array(
//				'article/index/<cid:\d+>'=>'article/index',
//        		'article/<id:\d+>'=>'article/view',
//        		'article/anli/<cid:\d+>'=>'article/anli',
//        		'anli/<id:\d+>'=>'article/alview',
//        		'article/blog/<cid:\d+>'=>'article/blog',
//        		'blog/<id:\d+>'=>'article/bview',
//        		'baike/<id:\d+>'=>'article/bkview',
//        		'article/about/<id:\d+>'=>'article/about',
//        		'product/index/<cid:\d+>'=>'product/index',
//        		'product/<id:\d+>'=>'product/view',
//        	),
//		),
        /*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/db_magnet.db',
			'tablePrefix'=>'mt_',
		),
         * 
         */
		// uncomment the following to use a MySQL database
        // 支持读写分离
		'db'=> include(dirname(dirname(dirname(__FILE__))) . '/runtime/front/db.config.php'),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
                	'class'=>'CFileLogRoute', 
					'levels'=>'error,warning,trace,info',
           	 	),
//           	 	array(
//                    'class' => 'CWebLogRoute',
//					  'levels'=>'trace,info,error, warning',
//                ),
			),
		),
		'image'=>array(
					'class'=>'application.extensions.image.CImageComponent',
					// GD or ImageMagick
					'driver'=>'GD',
					// ImageMagick setup path
					//'params'=>array('directory'=>'D:/Program Files/ImageMagick-6.4.8-Q16'),
				),		
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__) . '/params.php'),
	//'onBeginRequest'=>create_function('$event', 'return ob_start("ob_gzhandler");'),
	//'onEndRequest'=>create_function('$event', 'return ob_end_flush();'),
);