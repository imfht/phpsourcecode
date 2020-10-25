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
 * 店铺入驻表单字段验证器
 */
class ShopBase extends Validate{
	protected $rule = [
	    'fieldName' => 'require',
	    'dataType' => 'require|checkDataType',
	    'fieldTitle' => 'require',
	    'dataLength' => 'require',
	    'isRequire' => 'require|in:0,1',
	    'isRelevance' => 'in:0,1',
        'fieldType' => 'require|checkFieldType',
	    'fieldAttr' => 'require',
    ];
    protected $message  =   [
	    'fieldName.require' => '请填写表单字段',
        'dataType.require' => '请选择数据类型',
	    'fieldTitle.require' => '请填写表单标题',
	    'dataLength.require' => '请填写数据长度',
	    'isRequire.require' => '请选择是否必填',
	    'isRequire.in' => '无效的必填参数',
	    'fieldType.require' => '请选择表单类型',
	    'fieldAttr.require' => '请填写表单属性',
    ];
    protected function checkDataType($value){
        $array = ['varchar','char','int','mediumint','smallint','tinyint','text','decimal','date','time'];
        if(!in_array($value,$array))return '无效的数据类型';
        return true;
    }
    protected function checkFieldType($value){
        $array = ['input','textarea','radio','checkbox','select','other'];
        if(!in_array($value,$array))return '无效的表单类型';
        return true;
    }
    protected $scene = [
        'edit'  =>  ['fieldName','dataType','fieldTitle','dataLength','isRequire','isRelevance','fieldType','fieldAttr'],
    ]; 
}