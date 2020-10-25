<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$backend=dirname(dirname(__FILE__));
$frontend=dirname($backend);
Yii::setPathOfAlias('backend',$backend);
$frontendArray=require_once($frontend.'/config/main.php');
$backendArray=array(
	'name'=>'保定仁域网络科技有限公司后台管理系统',
	'basePath'=>$frontend,
        'viewPath'=>$backend.'/views',
		'controllerPath'=>$backend.'/controllers',
        'runtimePath'=>$backend.'/runtime',
		'import'=>array(	
		'application.models.*',
		'application.components.*',
		'application.extensions.upload.*',
	    'backend.models.*',
		'backend.components.*',
	),
	'components'=>array(
			'urlManager'=>array(
				'urlFormat'=>'path',
				'urlSuffix'=>null,
				'showScriptName'=>true, 
				'rules'=>null,
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
               
                
            ),
        ), 
		
	),
        
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('121.19.92.211','::1'),
		),
		
	),
	'params'=>require(dirname(__FILE__) . '/params.php'),
);
return CMap::mergeArray($frontendArray,$backendArray);