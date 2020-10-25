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
class Brand extends Validate
{
    protected $rule = [
        'name'  =>  'require|min:2|unique:brand',  
    ];

    protected $message = [
        'name.require'  =>  '品牌名称必填',
        'name.min'  =>  '品牌名称不能小于两个字',     
        'name.unique'  =>  '品牌名称已存在',
    ];

	
}
?>