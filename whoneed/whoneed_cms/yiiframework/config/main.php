<?php
// yii ext path
$strYiiExtentions = ROOT . '/yiiframework/extentions';

return array(
    'name' => '',
    'basePath' => APP_ROOT . '/protected',
    'ExtensionPath' => $strYiiExtentions,
	'runtimePath' => WEB_ROOT .'/runtime',
    'preload' => array('log'),	// 预加载日志模块
    'import' => include 'auto_import.php',	// 自动引入扩展的包
    'components' => array(
		//================ 总后台
        'db' => array(
            'class'					=> 'CDbConnection',
            'connectionString'		=> 'mysql:host=127.0.0.1;dbname=wcms',
            'emulatePrepare'		=> TRUE,
            'username'				=> 'root',
            'password'				=> 'root',
            'charset'				=> 'utf8',
            'tablePrefix'			=> '',
            'schemaCachingDuration'	=> 0, // 数据缓存时间
            'enableProfiling'		=> FALSE, // 是否开启数据缓存
        ),
        'db_waf' => array(
            'class'					=> 'CDbConnection',
            'connectionString'		=> 'mysql:host=127.0.0.1;dbname=waf',
            'emulatePrepare'		=> TRUE,
            'username'				=> 'root',
            'password'				=> 'root',
            'charset'				=> 'utf8',
            'tablePrefix'			=> '',
            'schemaCachingDuration'	=> 0, // 数据缓存时间
            'enableProfiling'		=> FALSE, // 是否开启数据缓存
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => FALSE,
            'rules' => include 'url.php',
        ),
        'cache' => array(
            'class' => 'CFileCache', //文件缓存
            //'cachePath' =>  ROOT.'/runtime/cache',// 缓存目录
            'directoryLevel' => '1', // 缓存文件的目录深度
        ),
        'session' => array(
            'class' => 'CDbHttpSession', // 基于数据库的session
            'connectionID' => 'db',
            'sessionTableName' => 'user_session',
            'autoCreateSessionTable' => TRUE,
        ),
        'user' => array(
            'allowAutoLogin' => TRUE, // enable cookie-based authentication
        ),
        'request' => array(
            'enableCookieValidation' => TRUE, // 防止Cookie攻击,要用CHttpCookie
        ),
        'assetManager' => array(
            'BasePath' => WEB_ROOT .'/runtime',
            'baseUrl' => '/runtime',
        ),
    ),
    // 加载全局变量
    // 调用方法 Yii::app()->params['paramName']
    'params' => array(
		'dynamicRule'		=> array('all'=>'/.*/'),//测试模式,不开启缓存
    ),
);
