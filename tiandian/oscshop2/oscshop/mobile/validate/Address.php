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
 * 用户注册验证
 */ 
namespace osc\mobile\validate;
use think\Validate;
class Address extends Validate
{
    protected $rule = [
        'name'  =>  'require',
        'tel'  =>  'require',
        'address'  =>  'require',
        'province'  =>  'require',
        'city_id'  =>  'require',
        'country_id'  =>  'require',
    ];

    protected $message = [
        'name.require'  =>  '收货人必填',
        'tel.require'  =>  '联系电话必填',
        'address.require'  =>  '收货地址必填',
        'province.require'  =>  '地区必填',
        'city_id.require'  =>  '地区必填',
        'country_id.require'  =>  '地区必填',
    ];
	
}
?>