<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace osc\member\validate;
use think\Validate;
class Shipping extends Validate
{
    protected $rule = [
        'name'  => 'require|max:32|min:2',
        //'telephone'=>'require|number|length:11',	
        'telephone'=>'require|length:11',   
	    'province_id'=>'gt:-1',
	    'city_id'=>'gt:-1',
	    'address'  => 'require|max:128|min:3',   
    ];

    protected $message = [
        'name.require'  =>  '姓名必填',
        'name.min'  =>  '姓名不能小于2个字',     
        'name.max'  =>  '姓名不能大于32个字',         
		
		'address.require'  =>  '地址必填',
        'address.min'  =>  '地址不能小于3个字',     
        'address.max'  =>  '地址不能大于128个字', 
        
		'telephone.require'  =>  '手机号必填',
        //'telephone.number'  =>  '手机号必须是数字',     
        'telephone.length'  =>  '手机必须11位', 
        
		'province_id.gt'  =>  '省必填',
		'city_id.gt'  =>  '市必填',
    ];

	
}
?>