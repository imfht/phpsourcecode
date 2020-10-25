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
 * 规格类型验证器
 */
class SpecCats extends Validate{
	protected $rule = [
		'catName' => 'require|max:30',
		'goodsCatId' => 'require|gt:0',
		'isAllowImg' => 'number|in:0,1',
		'isShow' => 'number|in:0,1',
	];

	protected $message = [
        'catName.require' => '请输入规格名称',
        'catName.max' => '规格名称不能超过10个字符',
        'goodsCatId.require' => '请选择所属商品分类',
        'goodsCatId.gt' => '请选择所属商品分类',
        'isAllowImg.number' => '请选择是否显示允许上传图片',
        'isAllowImg.in' => '请选择是否显示允许上传图片',
        'isShow.number' => '请选择是否显示',
        'isShow.in' => '请选择是否显示',
	];

	protected $scene = [
		'add'=>['catName','goodsCatId','isAllowImg','isShow'],
		'edit'=>['catName','goodsCatId','isAllowImg','isShow']
	];
	
}