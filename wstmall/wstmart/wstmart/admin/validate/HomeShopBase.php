<?php 
namespace wstmart\admin\validate;
use think\Db;
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
 * 商家入驻字段验证器
 */
class HomeShopBase extends Validate{
    public function setRuleAndMessage($data){
        // 先过滤默认的验证规则
        unset($data['investmentStaff'],$data['invoiceRemarks']);
        $checkField = [];
        foreach($data as $k => $v){
            $field = Db::name('shop_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->find();
            if($field['isRequire']==1){
                $this->checkFields[] = $k;
                switch($field['fieldType']){
                    case 'input':
                        $this->rule[$k] = 'require';
                        $this->message[$k.'.require'] = '请输入'.$field['fieldTitle'];
                        break;
                    case 'select':
                        $this->rule[$k] = 'require';
                        $this->message[$k.'.require'] = '请选择'.$field['fieldTitle'];
                        break;
                    case 'radio':
                        $fieldAttr = explode(',',$field['fieldAttr']);
                        $fieldAttrArray = [];
                        foreach($fieldAttr as $v1){
                            $fieldAttrValue = explode('||',$v1);
                            $fieldAttrArray[] = $fieldAttrValue[0];
                        }
                        $fieldAttrStr = implode(',',$fieldAttrArray);
                        $this->rule[$k] = 'in:'.$fieldAttrStr;
                        $this->message[$k.'.in'] = '无效的'.$field['fieldTitle'].'参数';
                        break;
                    case 'checkbox':
                        if($field['fieldAttr']!='custom'){
                            $fieldAttr = explode(',',$field['fieldAttr']);
                            $fieldAttrArray = [];
                            foreach($fieldAttr as $v1){
                                $fieldAttrValue = explode('||',$v1);
                                $fieldAttrArray[] = $fieldAttrValue[0];
                            }
                            $fieldAttrStr = implode(',',$fieldAttrArray);
                            $this->rule[$k] = 'in:'.$fieldAttrStr;
                            $this->message[$k.'.in'] = '无效的'.$field['fieldTitle'];
                        }else{
                            $this->rule[$k] = 'require';
                            $this->message[$k.'.require'] = '请选择'.$field['fieldTitle'];
                        }
                        break;
                    case 'other':
                        switch($field['fieldAttr']){
                            case 'area':
                                $this->rule[$k] = 'require';
                                $this->message[$k.'.require'] = '请选择'.$field['fieldTitle'];
                                break;
                            case 'file':
                                $this->rule[$k] = 'require';
                                $this->message[$k.'.require'] = '请上传'.$field['fieldTitle'];
                                break;
                            case 'date':
                                $this->rule[$k] = 'require';
                                if($field['dateRelevance']){
                                    $this->message[$k.'.require'] = '请输入'.$field['fieldTitle'].'开始日期';
                                    $dateRelevance = explode(',',$field['dateRelevance']);
                                    $res = $this->checkFieldEndDate($dateRelevance[0],$dateRelevance[1]);
                                    // 判断时期字段有无勾选长期，res===false代表没有勾选长期
                                    if($res === false){
                                        $this->rule[$dateRelevance[0]] = "require";
                                        $this->message[$dateRelevance[0].'.require'] = '请输入'.$field['fieldTitle'].'结束日期';
                                    }
                                }else{
                                    $this->message[$k.'.require'] = '请输入'.$field['fieldTitle'];
                                }
                                break;
                            case 'time':
                                $this->rule[$k] = 'require';
                                $this->message[$k.'.require'] = '请选择'.$field['fieldTitle'];
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        $this->scene['add'] = $this->checkFields;
    }
	public $rule = [
        'investmentStaff' => 'checkInvestment:1',
        'invoiceRemarks' => 'checkInvoiceRemark:1',
        'longitude' => 'checkLocation',
        'latitude' => 'checkLocation',
        'mapLevel' => 'checkLocation',
    ];
	
	public $message  =  [
        'investmentStaff.checkInvestment' => '请输入商城招商人员姓名',
        'invoiceRemarks.checkInvoiceRemark' => '请输入发票说明',
        'longitude.checkLocation' => '请选择经纬度',
        'latitude.checkLocation' => '请选择经纬度',
        'mapLevel.checkLocation' => '请选择经纬度',
	];

    public $scene = [
        'add'=>[]
    ];

    public $checkFields = [

    ];

    public function checkFieldEndDate($endDate,$isLong){
        $isLong = input("post.$isLong",0);
        $key = input("post.$endDate");
        return ($isLong==0 && $key=='')? false : true;
    }
    
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