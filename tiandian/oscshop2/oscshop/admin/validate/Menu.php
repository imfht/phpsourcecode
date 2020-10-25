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
namespace osc\admin\validate;
use think\Validate;
class Menu extends Validate
{
    protected $rule = [
        'title'  =>  'require|min:2',
        'sort_order'=>'number'    
    ];

    protected $message = [
        'title.require'  =>  '后台菜单名称必填',
        'title.min'  =>  '后台菜单名称不能小于两个字',     
      
        'sort_order.number'  =>  '排序必须是数字' 
    ];

	
}
?>