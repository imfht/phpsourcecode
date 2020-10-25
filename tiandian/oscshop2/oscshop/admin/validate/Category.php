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
class Category extends Validate
{
    protected $rule = [
        'name'  =>  'require|min:2|unique:category',
       
    ];

    protected $message = [
        'name.require'  =>  '分类名必填',
        'name.min'  =>  '分类名不能小于两个字',     
        'name.unique'  =>  '分类名已存在', 
       
    ];

	
}
?>