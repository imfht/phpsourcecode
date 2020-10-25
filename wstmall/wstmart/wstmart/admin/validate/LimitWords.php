<?php 
namespace wstmart\admin\validate;
use think\Validate;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 系统禁用关键字验证器
 */
class LimitWords extends Validate{
	protected $rule = [
	    'word' => 'require|max:50',
    ];
    
    protected $message = [
         'word.require' => '请输入系统禁用关键字',
         'word.max' => '系统禁用关键字不能超过50个字符',
    ];
    
    protected $scene = [
        'add'   =>  ['word'],
        'edit'  =>  ['word']
    ]; 
}