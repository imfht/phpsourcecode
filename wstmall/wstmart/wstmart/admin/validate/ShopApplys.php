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
 * 商家入驻验证器
 */
class ShopApplys extends Validate{
	protected $rule = [
		'applyStatus'=>'in:1,-1',
        'shopName'=>'checkShopName:1',
        'handleReamrk'=>'checkStatus:1'
	];

    protected $message = [
		'applyStatus.in'=>'无效的申请状态',
        'handleReamrk.checkStatus'=>'',
    ];
    /**
     * 检测店铺名称
     */
    function checkShopName(){
       $applyStatus = (int)input('applyStatus');
       $shopName = input('shopName');
       if($applyStatus==1 && $shopName=='')return '请输入店铺名称';
       return true;
    }
    /**
     * 检测申请失败原因
     */
    function checkStatus(){
       $applyStatus = (int)input('applyStatus');
       $handleReamrk = input('handleReamrk');
       if($applyStatus==-1 && $handleReamrk=='')return '请输入审核不通过原因';
       return true;
    }
    protected $scene = [
        'edit'=>['applyStatus','handleReamrk']
    ];
}