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
class StockStatus extends Validate
{
    protected $rule = [
        'name'  =>  'require|min:2|unique:stock_status'
    ];

    protected $message = [
        'name.require'  =>  '库存状态必填',
        'name.min'  =>  '库存状态不能小于两个字',     
        'name.unique'  =>  '库存状态已存在'
    ];

	
}
?>