<?php
return array(
	'TMPL_PARSE_STRING'  =>array(     
		'__PUBLIC__' => __ROOT__, // 更改默认的/Public 替换规则  
	),
	'URL_MODEL'=>2,
	'URL_ROUTER_ON'   => true, 
	'URL_ROUTE_RULES'=>array(      
		'ping'              => 'Index/ping',    
		'control'			=> 'Index/control',
		'login'				=> 'Index/login',
		'portal'			=> 'Index/portal',
		'auth'				=> 'Index/auth',
		'upgrade'			=> 'Index/upgrade',
		'ad/show'				=> 'Index/show',
	),
	
);