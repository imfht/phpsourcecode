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
 * 行业验证器
 */
class Trades extends Validate{
	protected $rule = [
	    'tradeName' => 'require|max:30',
	    'tradeSort' => 'require|max:16'
    ];
    
    protected $message = [
         'tradeName.require' => '请输入行业名称',
         'tradeName.max' => '行业名称不能超过10个字符',
         'tradeSort.require' => '请输入排序号',
         'tradeSort.max' => '排序号不能超过8个字符'
    ];
    
    protected $scene = [
        'add'   =>  ['tradeName','tradeSort'],
        'edit'  =>  ['tradeName','tradeSort']
    ]; 
}