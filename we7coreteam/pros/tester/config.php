<?php

use Testify\Testify;
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';
load()->library('oss');
//load()->classs('cdn/qiniuapi');
//load()->classs('cdn/cosapi');
//load()->classs('cdn/cos4api');
//load()->classs('cdn/ossapi');
load()->classs('filesystem/storage');
load()->func('communication');

$tester = new Testify('测试CDN');



$tester->test('cos', function (){


});

$tester->test('oss', function (){


});

 $APPID = '10016060';
 $SECRET_ID = 'AKIDHUEPJRbHDtdIM2gbIdYtskbCfjZUnjGZ';
 $SECRET_KEY = 'QdPKU6wRGeOYkxiucAzCVqc6uFRl1vvQ';
 $BUCKET = 'we7cloud';

$cos = new CosApi($SECRET_ID, $SECRET_KEY, $BUCKET, $APPID);
$cos->upload('/wxapp/a.txt', '123');

//$cos4 = new Cos4Api('AKIDc7Tlz2W43rTt79ClofP0mRsBKMDtbcms',
//	'OemlEallZGoAPSJyWt44sXLAtTK2Z7Wj','cos4api', '1251743857');
//
//$result = $cos4->putFile('/dddd123.txt', __DIR__.'/cdn.php');
////$result = $cos4->delete('/dddd123.txt');
//var_dump($result);



//$content = Storage::disk('cos4')->putFile('dddd', __DIR__.'/cdn.php');
//var_dump($content);





