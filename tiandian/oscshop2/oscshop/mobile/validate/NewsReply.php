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
class NewsReply extends Validate
{
    protected $rule = [
        'keyword'  =>  'require|unique:wechat_rule',
        'content'=>'require',       
        'title'=> 'require',
    ];

    protected $message = [
        'keyword.require'  =>  '关键字必填',  
        'title.require'  =>  '标题必填',  
        'keyword.unique'  =>  '关键字已经存在',             
		'content.require'  =>  '图文内容必填',
    ];

	protected $scene = [
        'edit'  =>  ['keyword'=>'require','content','title'],
    ];
}
?>