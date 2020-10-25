<?php 
namespace wstmart\common\validate;
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
 * 商品咨询验证器
 */
class GoodsConsult extends Validate{
	protected $rule = [
		'consultContent'  => 'require|length:3,600',
		'consultType'   => 'in:1,2,3,4',
		'reply' => 'require|length:3,600'
	];
	
	protected $message  =   [
		'consultContent.require'   => '请输入咨询内容',
		'consultContent.length' => '咨询内容应为3-200个字',
		'consultType.in' => '请选择咨询类别',
		'reply.require'   => '请输入回复内容',
		'reply.length'  => '回复内容应为3-200个字'
	];

    protected $scene = [
        'add'   =>  ['consultContent','consultType'],
        'edit'  =>  ['reply']
    ]; 
}