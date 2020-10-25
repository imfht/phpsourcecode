<?php
return array(
	'URL_ROUTER_ON'		=>	true,
	//路由定义
	'URL_ROUTE_RULES'	=> 	array(
		'blog/:year\d/:month\d'	=>	'Home/Route/archive', //规则路由
		'blog/:id\d'			=>	'Home/Route/read', //规则路由
		'blog/:cate'			=>	'Home/Route/category', //规则路由
	),
);