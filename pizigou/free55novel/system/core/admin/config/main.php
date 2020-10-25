<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$backend=dirname(dirname(__FILE__));
$frontend=dirname($backend);
Yii::setPathOfAlias('backend',$backend);
$frontendArray=require_once($frontend.'/config/main.php');
unset($frontendArray['components']['urlManager']);

$backendArray=array(
	'name'=>'飞舞小说系统',
	'basePath'=>$frontend,
    'viewPath'=>$backend.'/views',
	'controllerPath'=> $backend.'/controllers',
    'runtimePath'=> $frontend .'/../../runtime/backend',
	'import'=>array(	
    'application.models.*',
    'application.components.*',
    'application.extensions.upload.*',
//    'application.extensions..gallerymanager.*',
//    'application.extensions..gallerymanager.models.*',
    'backend.models.*',
    'backend.components.*',      
	),
    'theme' => 'bootstrap',
    'behaviors' => null,
    'components'=>array(
        'user'=>array(
            // enable cookie-based authentication
            'stateKeyPrefix' => '_free55admin',
            'allowAutoLogin'=> true,
        ),

		'urlManager'=>array(
			'urlFormat'=> 'path',
			'urlSuffix'=> null,
			'showScriptName'=> true,
			'rules'=> array(
//                'chapter/<id:\d+>' => 'article/view',
//                'book/<id:\d+>' => 'book/view',
//                'category/<title:\w+>' => 'category/index',
//                'admin/index' => 'site/index',
                'admin/<_c:(site|adminuser|user|book|category|article)>/<_a:(index|create|update|delete)>' => '<_c>/<_a>',
                'admin/login' => 'site/login',
                'admin/logout' => 'site/logout',
            ),
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
                	'class'=>'CFileLogRoute', 
					'levels'=>'error,warning,trace,info',
           	 	),        
                )
    ),

    'themeManager' => array(
      'basePath' => BASE_THEME_PATH . DS . 'admin',
      'baseUrl' => $webUrl . '/' . BASE_THEME_DIR . '/admin',
    ),
	),
	'params'=> require($backend.'/config/params.php'),
);
return CMap::mergeArray($frontendArray,$backendArray);