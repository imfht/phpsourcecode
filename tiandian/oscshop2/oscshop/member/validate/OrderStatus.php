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
class OrderStatus extends Validate
{
    protected $rule = [
        'name'  =>  'require|min:2|unique:order_status',
          
    ];

    protected $message = [
        'name.require'  =>  '名称必填',
        'name.min'  =>  '名称不能小于两个字',     
        'name.unique'  =>  '名称已存在',         
		
    ];

	
}
?>