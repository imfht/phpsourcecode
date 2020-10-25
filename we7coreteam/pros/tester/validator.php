<?php
use Testify\Testify;

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';
load()->classs('validator');

$tester = new \Testify\Testify('hello');

/**
 * 'required' => ':attribute 必须填写',
'integer' => ':attribute必须是整数',
'string' => ':attribute必须是字符串',
'json' => ':attribute 必须是json',
'array' => ':attribute必须是数组',
'min' => ':attribute不能小于%s',
'max' => ':attribute不能大于%s',
'between'=> ':attribute 必须在 %s %s 范围内',
'size' => ':attribute 大小必须是 %s',
'url' => ':attribute不是有效的url', //url //不带参数默认过滤127 172 10开头的ip 预防ssrf
'email' => ':attribute不是有效的邮箱',
'mobile' => ':attribute不是有效的手机号',
'file' => ':attribute必须是一个文件',
'image' => ':attribute必须是一个图片',
'ip' => ':attribute不是有效的ip',
'numeric' => ':attribute必须是数字',
'in' => ':attribute 必须在 %s 内',
'notin'=> ':attribute 不在 %s 内',
'date' => ':attribute 必须是有效的日期',
'after' => ':attribute 日期不能小于 %s',
'before'=> ':attribute 日期不能大于 %s',
'regex' => ':attribute 不是有效的数据', //regex:pattern
'same' => ':attribute 和 $s 不一致', //some:field
'boolean'=> ':attribute 必须是boolean值',
 */



$tester->test('testValid', function(){
	$url = null;// 'https://www.baidu.com/&ssd=as&as=../asd../\asdad\\sdff..//asdas..sdf&script=123';
	$file = __DIR__.'/test_1x.php';
	$validor = new Validator(
		array(
			'data_url'=>$url,
			'data_int'=>3232,
			'data_file'=>$file,
			'data_array'=>array(1,2,3),
			'data_email'=>'sdjkd@qqcom',
			'data_string'=>'3',
			'data_ip'=> '1.25.55.55133',
			'data_in'=> '2',
			'data_notin'=>'4',
			'data_between'=>3,
			'data_same'=>'3',
			'data_date'=>'2017-11-22',
			'data_after'=>'2017-11-20',
			'data_before'=>'2017-11-23',
			'data_bool'=>'2232',
			'data_sms'=>'32'
			),
		array(
		'data_url'=>'required|url',
		'data_int'=>'min:3233|max:90',
		'data_file'=>'file|min:8|max:3',
		'data_array'=>'array|size:3',
		'data_email'=>'email',
		'data_string'=>'required|string',
		'data_ip'=>'ip',
		'data_between'=>'between:5,10',
		'data_same'=> 'same:data_string'	,
		'data_date'=>'date',
		'data_after'=>'after:2017-11-21',
		'data_before'=>'before:data_date',
		'data_in'=>'in:3,4,5',
		'data_notin'=>array(array('name'=>'notin', 'params'=>array('3', '4', '7'))),
		'data_bool'=>'bool',
		'data_sms' => 'required|sms|size:5',

	),array(
		'sms'=>'验证码不正确',
		'data_notin.notin'=>'字段内容必须不在 3,4,7 内',
		'data_same'=>'字段必须和data_string字段一致',
		'data_sms'=>'短信验证码不正确',
		'data_before'=>'date_before不能大于data_date'
	));
	$validor->addRule('sms', function($key, $value, $params, $validor){
		return false;

	});
	$validor->valid();
	var_dump($validor->errors());

});
/*
$tester->test('testurl', function(){

	$url = 'https://www.baidu.com/&ssd=as&as=../asd../\asdad\\sdff..//asdas..sdf';


	$validor = new Validator(array('url'=>$url), array(
		'url'=>'required|url'
	),array());
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testrequired', function(){

	$validor = new Validator(array('data'=>null, 'data1'=>'', 'data2'=>array()), array(
		'data'=>'required',
		'data1'=>'required',
		'data2'=>'required',

	),array());
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testrequired', function(){

	$validor = new Validator(array('data'=>null, 'data1'=>'', 'data2'=>array(), 'data3'=>' eee'), array(
		'data'=>'required',
		'data1'=>'required',
		'data2'=>'required',
		'data3'=>'required'

	),array());
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testinterger', function(){

	$validor = new Validator(array('data'=> '1', 'data1'=> 33.33, 'data2'=>3333, 'data3'=>655225588555), array(
		'data'=>'integer',
		'data1'=>'integer',
		'data2'=>'integer',
		'data3'=>'integer'

	),array());
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testString', function(){

	$validor = new Validator(array('data'=> 1, 'data1'=> 33.33, 'data2'=>3333, 'data3'=>'655225588555'), array(
		'data'=>'string',
		'data1'=>'string',
		'data2'=>'string',
		'data3'=>'string'

	),array(
		'data'=> 'data必须是字符串'
	));
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testString', function(){
	$validor = new Validator(array('data'=> 1, 'data1'=> 33.33, 'data2'=>3333, 'data3'=>'655225588555'), array(
		'data'=>'string',
		'data1'=>'string',
		'data2'=>'string',
		'data3'=>'string'

	),array(
		'data'=> 'data必须是字符串'
	));
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testArray', function(){
	$validor = new Validator(
		array('data'=> 1,
			'data1'=> array(123),
			'data2'=>array(),
			'data3'=>'655225588555'),
		array(
		'data'=>'array',
		'data1'=>'array',
		'data2'=>'array',
		'data3'=>'array'
	),array(
		'data'=> 'data必须是数组',
	));
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testMobile', function(){
	$validor = new Validator(
		array('data'=> 13966552255,
			'data1'=> '12323',
			'data2'=> '1232',
			'data3'=>'13566552255'),
		array(
			'data'=>'mobile',
			'data1'=>'mobile',
			'data2'=>'mobile',
			'data3'=>'mobile'
		),array(
		'data'=> 'data手机号不正确',
	));
	$validor->valid();
	var_dump($validor->errors());
});

$tester->test('testEmail', function(){
	$validor = new Validator(
		array('data'=> 'asad@email.com',
			'data1'=> 'asad@qq.com',
			'data2'=> '1232',
			'data3'=>'asad@44.com'),
		array(
			'data'=>'email',
			'data1'=>'email',
			'data2'=>'email',
			'data3'=>'email'
		),array(
		'data'=> 'null',
	));
	$validor->valid();
	var_dump($validor->errors());
});
*/

$tester->run();