<?php 
namespace wstmart\home\validate;
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
        //入驻步骤1
        'applyLinkMan' => 'require',
        'applyLinkTel' => 'require',
        'applyLinkEmail' => 'require',
        'isInvestment' => 'in:0,1',
        'investmentStaff' => 'checkInvestment:1',
        //入驻步骤2
        'businessLicenceType' => 'require',
        'businessLicence' => 'require',
        'legalPersonName' => 'require',
        'businessAreaPath0' => 'require',
        'licenseAddress' => 'require',
        'establishmentDate' => 'require',
        'businessStartDate' => 'require',
        'businessEndDate' => 'checkBusinessEndDate:1',
        'isLongbusinessDate' => 'in:0,1',
        'registeredCapital' => 'require',
        'empiricalRange' => 'require',
        'areaIdPath0' => 'require',
        'shopCompany' => 'require',
        'shopAddress' => 'require',
        'shopTel' => 'require',
        'shopkeeper' => 'require',
        'telephone' => 'require',
        'legalCertificateType' => 'require',
        'legalCertificate' => 'require',
        'legalCertificateStartDate' => 'require',
        'legalCertificateEndDate' => 'checkLegalCertificateEndDate:1',
        'isLonglegalCertificateDate' => 'in:0,1',
        'legalCertificateImg' => 'require',
        'businessLicenceImg' => 'require',
        'bankAccountPermitImg' => 'require',
        'organizationCode' => 'require',
        'organizationCodeStartDate' => 'require',
        'organizationCodeEndDate' => 'checkOrganizationCodeEndDate:1',
        'isLongOrganizationCodeDate' => 'in:0,1',
        'organizationCodeImg' => 'require',
        //入驻步骤3
        'taxpayerType' => 'require',
        'taxpayerNo' => 'require',
        'taxRegistrationCertificateImg' => 'require',
        'taxpayerQualificationImg' => 'require',
        'bankUserName' => 'require|max:100',
        'bankNo' => 'require',
        'bankId' => 'require',
        'bankAreaId' => 'require',
        //入驻步骤4
        'shopName' => 'require',
        'shopImg' => 'require',
        'goodsCatIds' => 'require',
        'isInvoice' => 'in:0,1',
        'invoiceRemarks' => 'checkInvoiceRemark:1',
        'serviceStartTime' => 'require',
        'longitude' => 'checkLocation',
        'latitude' => 'checkLocation',
        'mapLevel' => 'checkLocation',
        'serviceEndTime' => 'require'
    ];
	
	protected $message  =  [
        //入驻步骤1
        'applyLinkMan.require' => '请输入联系人姓名',
        'applyLinkTel.require' => '请输入联系人手机',
        'applyLinkEmail.require' => '请输入联系人邮箱',
        'isInvestment.in' => '无效的对接商城招商人参数',
        'investmentStaff.checkInvestment' => '请输入商城招商人员姓名',
        //入驻步骤2
        'businessLicenceType.require' => '请选择执照类型',
        'businessLicence.require' => '请输入营业执照注册号',
        'legalPersonName.require' => '请输入法定代表人姓名',
        'businessAreaPath0.require' => '请选择营业执照所在地',
        'licenseAddress.require' => '请输入营业执照详细地址',
        'establishmentDate.require' => '请选择成立日期',
        'businessStartDate.require' => '请输入营业期限开始日期',
        'businessEndDate.checkBusinessEndDate' => '请输入营业期限结束日期',
        'isLongbusinessDate.in' => '无效的营业期限参数',
        'registeredCapital.require' => '请输入注册资本',
        'empiricalRange.require' => '请输入经营范围',
        'areaIdPath0.require' => '请选择公司所在地',
        'shopCompany.require' => '请输入公司名称',
        'shopAddress.require' => '请输入公司详细地址',
        'shopTel.require' => '请输入公司电话',
        'shopkeeper.require' => '请输入公司紧急联系人',
        'telephone.require' => '请输入公司紧急联系人电话',
        'legalCertificateType.require' => '请选择法人代表证件类型',
        'legalCertificate.require' => '请输入法定代表人证件号',
        'legalCertificateStartDate.require' => '请选择法定代表人证件有效期开始日期',
        'legalCertificateEndDate.checkLegalCertificateEndDate' => '请选择法定代表人证件有效期结束日期',
        'isLonglegalCertificateDate.in' => '无效的代表人证件有效期参数',
        'legalCertificateImg.require' => '请上传法人证件电子版',
        'businessLicenceImg.require' => '请上传营业执照电子版',
        'bankAccountPermitImg.require' => '请上传银行开户许可证电子版',
        'organizationCode.require' => '请输入组织机构代码',
        'organizationCodeStartDate.require' => '请输入组织机构代码证有效期开始日期',
        'organizationCodeEndDate.checkOrganizationCodeEndDate' => '请输入组织机构代码证有效期结束日期',
        'isLongOrganizationCodeDate.in' => '无效的组织机构代码证有效期参数',
        'organizationCodeImg.require' => '请上传组织机构代码证电子版',
        //入驻步骤3
        'taxpayerType.require' => '请选择纳税人类型',
        'taxpayerNo.require' => '请输入纳税人识别号',
        'taxRegistrationCertificateImg.require' => '请上传税务登记证电子版',
        'taxpayerQualificationImg.require' => '请上传一般纳税人资格证电子版',
        'bankUserName.require' => '请输入持卡人名称',
		'bankUserName.max' => '持卡人名称长度不能能超过50个字符',
        'bankNo.require' => '请选择银行账号',
        'bankId.require' => '请选择结算银行',
        'bankAreaId.require' => '请选择开户所地区',
        //入驻步骤4
        'shopName.require' => '请输入店铺名称',
        'shopImg.require' => '请上传店铺图标',
        'goodsCatIds.require' => '请选择经营类目',
        'isInvoice.in' => '无效的发票类型',
        'invoiceRemarks.checkInvoiceRemark' => '请输入发票说明',
        'serviceStartTime.require' => '请选择服务开始时间',
        'longitude.checkLocation' => '请选择经纬度',
        'latitude.checkLocation' => '请选择经纬度',
        'mapLevel.checkLocation' => '请选择经纬度',
        'serviceEndTime.require' => '请选择服务结束时间'
	];

 
    public $scene = [
        'editInfo'  =>['shopImg','isInvoice','serviceStartTime','serviceEndTime'],
        'editBank'  =>['bankId','bankAreaId','bankNo','bankUserName'],
        'applyStep1'=>['applyLinkMan','applyLinkTel','applyLinkEmail','isInvestment','investmentStaff'],
        'applyStep2'=>['businessLicenceType','businessLicence','legalPersonName','businessAreaPath0','licenseAddress','establishmentDate','businessStartDate','businessEndDate','isLongbusinessDate','registeredCapital','empiricalRange','areaIdPath0','shopCompany','shopAddress','shopTel','shopkeeper','telephone','shopEmergencyLinkMan','legalCertificateType','legalCertificate','legalCertificateStartDate','legalCertificateEndDate','isLonglegalCertificateDate','legalCertificateImg','businessLicenceImg','bankAccountPermitImg','organizationCode','organizationCodeStartDate','organizationCodeEndDate','isLongOrganizationCodeDate','organizationCodeImg','longitude','latitude','mapLevel'],
        'applyStep3'=>['taxpayerType','taxpayerNo','taxRegistrationCertificateImg','taxpayerQualificationImg','bankUserName','bankNo','bankId','bankAreaId'],
        'applyStep4'=>['shopName','shopImg','goodsCatIds','isInvoice','invoiceRemarks','serviceStartTime','serviceEndTime']
    ]; 
    
    protected function checkInvoiceRemark($value){
    	$isInvoice = input('post.isInvoice/d',0);
    	$key = Input('post.invoiceRemarks');
    	return ($isInvoice==1 && $key=='')?'请输入发票说明':true;
    }

    protected function checkInvestment($value){
        $isInvestment = input('post.isInvestment/d',0);
        $key = Input('post.investmentStaff');
        return ($isInvestment==1 && $key=='')?'请输入商城招商人员姓名':true;
    }

    protected function checkBusinessEndDate($value){
        $isLongbusinessDate = input('post.isLongbusinessDate/d',0);
        $key = Input('post.businessEndDate');
        return ($isLongbusinessDate==0 && $key=='')?'请输入营业期限结束日期':true;
    }
    protected function checkLegalCertificateEndDate($value){
        $isLonglegalCertificateDate = input('post.isLonglegalCertificateDate/d',0);
        $key = Input('post.legalCertificateEndDate');
        return ($isLonglegalCertificateDate==0 && $key=='')?'请选择法定代表人证件有效期结束日期':true;
    }
    protected function checkOrganizationCodeEndDate($value){
        $isLonglegalCertificateDate = input('post.isLongOrganizationCodeDate/d',0);
        $key = Input('post.organizationCodeEndDate');
        return ($isLonglegalCertificateDate==0 && $key=='')?'请输入组织机构代码证有效期结束日期':true;
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