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
 * 店铺验证器
 */
class Shops extends Validate{
	protected $rule = [
	    'shopSn' => 'checkShopSn:1|max:40',
	    'shopName' => 'require|max:40',
        'shopCompany' => 'require|max:300',
        'shopTel' => 'require|max:40',
        'longitude' => 'checkLocation',
        'latitude' => 'checkLocation',
        'mapLevel' => 'checkLocation',
        'shopkeeper' => 'require|max:100',
        'telephone' => 'require|max:40',
        'isSelf' => 'in:0,1',
        'shopImg' => 'require',
        'areaId'  => 'require',
        'shopAddress' => 'require',
        'isInvoice' => 'in:0,1',
        'invoiceRemarks' => 'checkInvoiceRemark:1',
        'shopAtive' => 'in:0,1',
        'bankUserName' => 'require|max:100',
        'bankNo' => 'require',
        'bankId' => 'require',
        'bankAreaId' => 'require',
        'shopStatus' => 'in:-1,1',
        'statusDesc' => 'checkStatusDesc:1',
    ];
    
    protected $message = [
        'shopSn.checkShopSn' => '请输入店铺编号',
        'shopSn.max' => '店铺编号不能超过20个字符',
        'shopName.require' => '请输入店铺名称',
        'shopName.max' => '店铺名称不能超过20个字符',
        'shopCompany.require' => '请输入公司名称',
        'shopCompany.max' => '公司名称不能超过100个字符',
        'longitude.checkLocation' => '请选择公司所在区域',
        'latitude.checkLocation' => '请选择公司所在区域',
        'mapLevel.checkLocation' => '请选择公司所在区域',
        'shopTel.require' => '请输入公司联系电话',
        'shopTel.max' => '公司联系电话不能超过20个字符',
        'shopkeeper.require' => '请输入公司紧急联系人',
        'shopkeeper.max' => '公司紧急联系人不能超过50个字符',
        'telephone.require' => '请输入公司紧急联系人手机',
        'telephone.max' => '公司紧急联系人手机不能超过20个字符',
        'isSelf.in' => '无效的自营店类型',
        'shopImg.require' => '请上传店铺图标',
        'areaId.require' => '请选择公司所在区域',
        'shopAddress.require' => '请输入公司详细地址',
        'isInvoice.in' => '无效的发票类型',
        'invoiceRemarks.checkInvoiceRemark' => '请输入发票说明',
        'shopAtive.in' => '无效的营业状态',
        'bankUserName.require' => '请输入银行开户名',
        'bankUserName.max' => '银行开户名称长度不能能超过50个字符',
        'bankNo.require' => '请输入对公结算银行账号',
        'bankId.require' => '请选择结算银行',
        'bankAreaId.require' => '请选择开户所地区',
        'shopStatus.in' => '无效的店铺状态',
        'statusDesc.checkStatusDesc' => '请输入店铺停止原因',
    ];
    
    protected $scene = [
        'add'   =>  ['shopSn','shopName','shopCompany','longitude','latitude','shopkeeper','telephone','shopCompany','shopTel','isSelf','shopImg',
                     'areaId','shopAddress','isInvoice','shopAtive','bankId','bankAreaId','bankNo','bankUserName','shopAtive'],
        'edit'  =>  ['shopSn','shopName','shopCompany','shopkeeper','telephone','shopCompany','shopTel','isSelf','shopImg',
                     'areaId','shopAddress','isInvoice','shopAtive','bankId','bankAreaId','bankNo','bankUserName','shopAtive']
    ]; 
    
    protected function checkShopSn($value){
    	$shopId = Input('post.shopId/d',0);
    	$key = Input('post.shopSn');
    	if($shopId>0){
    		if($key=='')return '请输入店铺编号';
    		$isChk = model('Shops')->checkShopSn($key,$shopId);
    		if($isChk)return '对不起，该店铺编号已存在';
    	}
    	return true;
    }
    
    protected function checkInvoiceRemark($value){
    	$isInvoice = Input('post.isInvoice/d',0);
    	$key = Input('post.invoiceRemarks');
    	return ($isInvoice==1 && $key=='')?'请输入发票说明':true;
    }
    
    protected function checkStatusDesc($value){
    	$shopStatus = Input('post.shopStatus/d',0);
    	$key = Input('post.statusDesc');
    	return ($shopStatus==-1 && $key=='')?'请输入店铺停止原因':true;
    }
     protected function checkLocation($value){
        $longitude = (float)input('post.longitude',0);
        $latitude = (float)input('post.latitude',0);
        $mapLevel = input('post.mapLevel',0);
        if(WSTConf('CONF.mapKey') == ''){
            return true;
        }else{
            return ($longitude==0 ||  $latitude==0 || $mapLevel==0)?'请选择经纬度':true;
        }

    }
}