<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
	return array(
		'host'=>array(
			'title'=>'FTP服务器:',
			'type'=>'text',
			'value'=>'127.0.0.1',
			'tip'=>'远程服务器地址'
		),
		'port'=>array(
			'title'=>'服务器端口:',
			'type'=>'num',
			'value'=>'21',
			'tip'=>'默认 21'
			
		),
		'username'=>array(
			'title'=>'FTP用户名:',
			'type'=>'text',
			'value'=>'',
			'tip'=>''
			
		),
		'password'=>array(
			'title'=>'FTP密码:',
			'type'=>'password',
			'value'=>'',
			'tip'=>''
			
		),
		'timeout'=>array(
			'title'=>'超时时间:',
			'type'=>'num',
			'value'=>'3600',
			'tip'=>'默认 90秒'
			
		),
		'server'=>array(
			'title'=>'服务器调用地址:',
			'type'=>'num',
			'value'=>'',
			'tip'=>'填写你的服务器组id'
			
		)
		
	);