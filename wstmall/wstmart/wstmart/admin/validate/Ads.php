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
 * 广告验证器
 */
class Ads extends Validate{
	protected $rule = [
        'adName' => 'require|max:30',
        'adFile' => 'require',
        'adStartDate' => 'require',
        'adEndDate'  => 'require',
    ];

    protected $message = [
        'adName.require' => '请输入广告标题',
        'adName.max' => '广告标题不能超过10个字符',
        'adFile.require' => '请上传广告图片',
        'adStartDate.require' => '请输入广告开始时间',
        'adEndDate.require' => '请输入广告结束时间',
    ];

    protected $scene = [
        'add'   =>  ['adName','adURL','subTitle','adStartDate','adEndDate'],
        'edit'  =>  ['adName','adURL','subTitle','adStartDate','adEndDate'],
    ]; 
}