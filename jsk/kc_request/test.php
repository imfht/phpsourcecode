<?php

// 测试文件

require './lib/kc_request.php';

$kr = new KC_Request();
if( $kr->get('http://www.baidu.com/')){
	echo $kr->result;
}else{
	echo $kr->error;
}



