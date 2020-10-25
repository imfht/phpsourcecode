<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [

	'category/:id'  => 'category/index',
	'goods/:id'   	=> 'goods/index',
	'pay_success$'	=> 'pay_success/pay_success',	
	//'goods$'   		=> '/',
	
	'reg$'   		=> 'member/login/reg',
	'login$'   		=> 'member/login/login',
	'logout$'   	=> 'member/login/logout',	
	'getarea$' 		=> 'member/login/getarea',

	
	'add$'   		=> 'member/cart/add',
	'cart$'   		=> 'member/cart/index',
	'update$'   	=> 'member/cart/update',
	'remove/:id'   	=> 'member/cart/remove',
	
	'checkout$'     			=>'member/checkout/index',
	'shipping_address$' 		=>'member/checkout/shipping_address',	
	'validate_shipping_address$'=>'member/checkout/validate_shipping_address',
	'validate_shipping_method$'	=>'member/checkout/validate_shipping_method',
	'validate_payment_method$'	=>'member/checkout/validate_payment_method',	
	'shipping_method$'			=>'member/checkout/shipping_method',
	'payment_method$'			=>'member/checkout/payment_method',
	'confirm$'					=>'member/checkout/confirm',
	
	'payment$'   		=> 'payment/payment/pay_api',	
	'wxpay$'			=> 'payment/weixin/code',
	'get_order_status$' => 'payment/weixin/get_order_status',
	
	'mobile$'   		=> 'mobile/index/index',
	
	//修改后台入口 	start
	//'admin$'   		    => '/',	
	//'admin123$'   		=> 'admin/index/index',
	//修改后台入口 	end
];
