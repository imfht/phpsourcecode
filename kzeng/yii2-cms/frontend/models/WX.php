<?php
/**
 * @link http://www.wstech.com/
 * @copyright Copyright (c) 2015 wstech LLC
 * @license http://www.wstech.com/license/
 *
 * Yii component, Just a Wrap of easywechat, 
 *
 * Usage:
 *
 *
 // ¾²Ì¬ÅäÖÃ
 'wx' => [
     'class' => 'common\wosotech\WX',
     'config' => [
         'debug' => true,
         'app_id' => 'wxd1806e66fe96a00c',
         'secret' => '17ac86b02a204254b4a563cd6a3c05af',
         'token' => 'vDHg6heBH3m6OM6F7D3638EObEZEDm3b',
         //'aes_key' => 'aaa',
         'log' => [
             'level' => 'debug',
             'file'  => '../runtime/easywechat.log',
         ],
         'oauth' => [
             'scopes' => ['snsapi_base'], // scopes: snsapi_userinfo, snsapi_base, snsapi_login
             //'callback' => '/examples/oauth_callback.php',
         ],
         'payment' => [
             'merchant_id' => '',
             'key' => '',
             'cert_path' => 'path/to/your/cert.pem',
             'key_path' => 'path/to/your/key', // XXX: absolute path£¡£¡£¡£¡
             // 'device_info'     => '013467007045764',
             // 'sub_app_id'      => '',
             // 'sub_merchant_id' => '',
             // ...
         ],     
        'guzzle' => [
            'timeout' => 5,
            //'verify' => false,
        ],         
     ]
 ],        

// »òÕß¶¯Ì¬ÅäÖÃ
 yii::$app->set('wx', [
     'class' => 'common\wosotech\WX',
     'config' => [
         'debug' => true,
         'app_id' => 'wxd1806e66fe96a00c',
         'secret' => '17ac86b02a204254b4a563cd6a3c05af',
         'token' => 'vDHg6heBH3m6OM6F7D3638EObEZEDm3b',
         //'aes_key' => 'aaa',         
         'log' => [
             'level' => 'debug',
             'file'  => '../runtime/easywechat.log',
         ],
         'oauth' => [
             'scopes' => ['snsapi_base'], // scopes: snsapi_userinfo, snsapi_base, snsapi_login
             'callback' => '/examples/oauth_callback.php',
         ],
         'payment' => [
             'merchant_id' => '',
             'key' => '',
             'cert_path' => 'path/to/your/cert.pem',
             'key_path' => 'path/to/your/key', // XXX: absolute path£¡£¡£¡£¡
             // 'device_info'     => '013467007045764',
             // 'sub_app_id'      => '',
             // 'sub_merchant_id' => '',
             // ...
         ],    
         'guzzle' => [
             'timeout' => 5,
             //'verify' => false,
         ],                  
     ]            
 ]);
// WxGh::findOne(['gh_id' => WxGh::DEFAULT_GH_ID])->loadWx();

// Ê¹ÓÃ·½·¨
$app = \yii::$app->wx->getApplication();
$menu = $app->menu;
$menus = $menu->all();
$menus = $menus['menu']['button'];
var_dump($menus);

$a = $app->server;
$a = $app->user;
$a = $app->user_tag;
$a = $app->user_group;
$a = $app->js;
$a = $app->oauth;
$a = $app->menu;
$a = $app->material;
$a = $app->material_temporary;
$a = $app->staff;
$a = $app->url;
$a = $app->qrcode;
$a = $app->semantic;
$a = $app->stats;
$a = $app->merchant;
$a = $app->payment;
$a = $app->lucky_money;
$a = $app->merchant_pay;
$a = $app->reply;
$a = $app->broadcast;
*/

namespace frontend\models;

use Yii;
use yii\base\Component;

class WX extends Component
{
	public $config;

	private $application;

	public function getApplication() {
		if ($this->application === null) {
			$this->application = new \EasyWeChat\Foundation\Application($this->config);
		}
		return $this->application;
	}
}

