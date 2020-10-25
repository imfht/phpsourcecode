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
namespace osc\mobile\validate;
use think\Validate;
class AgentLevel extends Validate
{
    protected $rule = [
        'type'  =>  'require',
        'name'=>'require',
        'return_percent'=>'float|require'    
    ];

    protected $message = [
        'type.require'  =>  '代理等级必填',               
		'name.require'  =>  '等级名称必填',        
        'return_percent.require'  =>  '返佣比例必填',
        'return_percent.float'  =>  '返佣比例必须是数字',  
    ];

	
}
?>