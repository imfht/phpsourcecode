<?php
/**
 * 路由配置表
 */
return array(
	array('pattern' =>'* /login','handler' => 'Index#login'),
	array('pattern' =>'get /loginout','handler' => 'Index#loginout'),
	array('pattern' =>'get /goods/index','handler' => 'Goods#index'),
	array('pattern' =>'* /goods/add','handler' => 'Goods#add'),
	array('pattern' =>'get /goods/get','handler' => 'Goods#get'),
	array('pattern' =>'get /goods/del','handler' => 'Goods#del'),
	array('pattern' =>'* /goods/edit','handler' => 'Goods#edit'),
	array('pattern' =>'get /','handler' => 'Index#index'),
);