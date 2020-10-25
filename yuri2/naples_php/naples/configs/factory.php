<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 10:55
 */

//工厂配置
return [
    'Core'=>['\naples\lib\Core'],
    'Help'=>['\naples\lib\Help',['pathHelp'=>PATH_NAPLES.'/functions/help.php','pathCustom'=>PATH_NAPLES.'/functions/custom.php',]],
    'ErrorCatch'=>['\naples\lib\ErrorCatch'],
    'Config'=>['\naples\lib\Config',PATH_NAPLES.'/configs/config.php'],
    'Logger'=>['\naples\lib\Logger',['savePath'=>PATH_RUNTIME.'/logs']],
    'Debug'=>['\naples\lib\Debug'],
    'Route'=>['\naples\lib\Route',PATH_NAPLES.'/configs/route.php'],
    'Dispatch'=>['\naples\lib\Dispatch'],
    'DocParser'=>['\naples\lib\DocParser',['rule'=>PATH_NAPLES.'/configs/docParse.php']],
    'Attention'=>['\naples\lib\Attention',PATH_NAPLES.'/configs/attention.php'],
    'Cookie'=>['\naples\lib\Cookie',['encrypt'=>true,'key'=>'cookie加密密钥']],
    'Cache'=>['naples\lib\caches\FileCache',['path'=>PATH_RUNTIME.'/cache/','expire'=>3600]],
    'TplExtend'=>['\naples\lib\TplExtend'],
    'Captcha'=>['\naples\lib\Captcha\Captcha',PATH_NAPLES.'/configs/captcha.php'],
    'WeiChat'=>['\naples\lib\weiChat\NPweiChat',PATH_NAPLES.'/configs/weiChat.php'],
    'QyWeiChat'=>['\naples\lib\weiChat\NPqyWeichat',PATH_NAPLES.'/configs/qyWeiChat.php'],
    'View'=>['\naples\lib\naplesTpl\NaplesTpl',PATH_NAPLES.'/configs/naplesTpl.php'],
    'DbConfig'=>['\naples\lib\DbConfig',PATH_NAPLES.'/configs/dbConfig.php'],
    'TimingProcess'=>['\naples\lib\TimingProcess',PATH_NAPLES.'/configs/timingProcess.php'],
    'PhpExcelHelper'=>['\naples\lib\PhpExcelHelper'],
    'FileUpload'=>['\naples\lib\FileUpload',['path'=>PATH_DATA.DS.'upload','allowtype'=>array('jpg','gif','png'),'maxsize'=>10000000,'israndname'=>true]],
];