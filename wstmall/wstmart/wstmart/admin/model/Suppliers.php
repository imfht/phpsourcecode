<?php
namespace wstmart\admin\model;
use think\Db;
use wstmart\admin\validate\SupplierHomeBase as VSupplierBase;
use think\Loader;
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
 * 供货商业务处理
 */
class Suppliers extends Base{
	protected $pk = 'supplierId';
	/**
	 * 分页
	 */
	public function pageQuery($supplierStatus=1){
		$areaIdPath = input('areaIdPath');
		$supplierName = input('supplierName');
		$isInvestment = (int)input('isInvestment/d',-1);
		$tradeId = (int)input('tradeId');
		$where = [];

		$where[] = ['s.dataFlag','=',1];
		$where[] = ['s.applyStatus','=',2];
		$where[] = ['s.supplierStatus','=',$supplierStatus];
		if($tradeId>0)$where[] = ['s.tradeId','=',$tradeId];
		if(in_array($isInvestment,[0,1]))$where[] = ['ss.isInvestment','=',$isInvestment];
		if($supplierName!='')$where[] = ['supplierName','like','%'.$supplierName.'%'];
		if($areaIdPath !='')$where[] = ['areaIdPath','like',$areaIdPath."%"];
		$sort = input('sort');
		$order = [];
		if($sort!=''){
			$sortArr = explode('.',$sort);
			$order = $sortArr[0].' '.$sortArr[1];
		}
        $page = Db::table('__SUPPLIERS__')->alias('s')
					->join("trades t","s.tradeId=t.tradeId","left")
					->join('__AREAS__ a2','s.areaId=a2.areaId','left')
			       ->join('__USERS__ u','u.userId=s.userId','left')
			       ->join('__SUPPLIER_EXTRAS__ ss','s.supplierId=ss.supplierId','left')
			       ->where($where)
			       ->field('u.loginName,s.supplierId,supplierSn,supplierName,t.tradeName,a2.areaName,supplierkeeper,telephone,supplierAddress,supplierCompany,supplierAtive,supplierStatus,expireDate')
			       ->order($order)
			       ->paginate(input('limit/d'))->toArray();
        if(count($page['data'])>0){
            foreach ($page['data'] as $key => $v){
                $page['data'][$key]['isExpire'] = ((strtotime($v['expireDate'])-strtotime(date('Y-m-d')))<2592000)?true:false;
            }
        }
        return $page;
	}
	/**
	 * 分页
	 */
	public function pageQueryByApply(){
		$areaIdPath = input('areaIdPath');
		$supplierName = input('supplierName');
		$isInvestment = (int)input('isInvestment/d',-1);
		$isApply = (int)input('isApply',-1);
		$tradeId = (int)input('tradeId');
		$where = [];
		$where[] = ['s.dataFlag','=',1];
		$where[] = ['s.applyStatus','in',[-1,0,1]];
		if($tradeId>0)$where[] = ['s.tradeId','=',$tradeId];
		if($isApply==1)$where[] = ['s.applyStatus','=',1];
		if($isApply==0)$where[] = ['s.applyStatus','in',[-1,0]];
		if(in_array($isInvestment,[0,1]))$where[] = ['ss.isInvestment','=',$isInvestment];
		if($supplierName!='')$where[] = ['supplierName','like','%'.$supplierName.'%'];
		if($areaIdPath !='')$where[] = ['areaIdPath','like',$areaIdPath."%"];
		return Db::table('__SUPPLIERS__')->alias('s')
				->join("trades t","s.tradeId=t.tradeId","left")
				->join('__AREAS__ a2','s.areaId=a2.areaId','left')
		       ->join('__SUPPLIER_EXTRAS__ ss','s.supplierId=ss.supplierId','left')
		       ->join('__USERS__ u','u.userId=s.userId','left')
		       ->where($where)
		       ->field('u.loginName,s.supplierId,applyLinkMan,applyLinkTel,investmentStaff,isInvestment,supplierName,t.tradeName,a2.areaName,supplierAddress,supplierCompany,applyTime,applyStatus')
		       ->order('s.supplierId desc')->paginate(input('limit/d'));
	}

	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
		Db::startTrans();
        try{
	        $supplier = $this->get($id);
	        $supplier->dataFlag = -1;
	        $result = $supplier->save();
	        WSTUnuseResource('suppliers','supplierImg',$id);
	        // 供货商申请表的图片标记为删除
	        $imgArr = model('supplierExtras')->field('legalCertificateImg,businessLicenceImg,bankAccountPermitImg,organizationCodeImg,taxRegistrationCertificateImg,taxpayerQualificationImg')->where(['supplierId'=>$id])->find();
	        WSTUnuseResource($imgArr->getData());
            if(false !== $result){
            	//删除供货商与商品分类的关系
            	Db::name('cat_suppliers')->where(['supplierId'=>$id])->delete();
            	//删除用户供货商身份
        	    Db::name('users')->where(['userId'=>$supplier->userId])->update(['dataFlag'=>-1]);
        	    //删除供货商角色
        	    Db::name('supplier_roles')->where(['supplierId'=>$id])->update(['dataFlag'=>-1]);
        	    //删除供货商职员
        	    Db::name('supplier_users')->where(['supplierId'=>$id])->update(['dataFlag'=>-1]);
            	//下架及下架商品
        	    model('supplier_goods')->delBysupplierId($id);
        	    Db::commit();
        	    return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}
	/**
	 * 根据根据userId删除供货商
	 */
	public function delByUserId($userId){
	    $supplier = $this->where('userId',$userId)->find();
	    if(!$supplier)return;
	    $supplier->dataFlag = -1;
	    $result = $supplier->save();
	    WSTUnuseResource('suppliers','supplierImg',$supplier->supplierId);
        if(false !== $result){
            //删除供货商与商品分类的关系
            Db::name('cat_suppliers')->where(['supplierId'=>$supplier->supplierId])->delete();
            //下架及删除商品
        	model('supplier_goods')->delBysupplierId($supplier->supplierId);
        	//删除供货商角色
    	    Db::name('supplier_roles')->where(['supplierId'=>$supplier->supplierId])->update(['dataFlag'=>-1]);
    	    //删除供货商职员
    	    Db::name('supplier_users')->where(['supplierId'=>$supplier->supplierId])->update(['dataFlag'=>-1]);
        	return WSTReturn("删除成功", 1);
        }
        return WSTReturn('删除失败',-1);
	}
	/**
     * 获取商家入驻资料
     */
    public function getById($id){
        $supplier = $this->alias('s')->join('__SUPPLIER_EXTRAS__ ss','s.supplierId=ss.supplierId','inner')
                   ->join('__USERS__ u','u.userId=s.userId','inner')
                   ->where('s.supplierId',$id)
                   ->find()
                   ->toArray();
       
        //获取经营范围
		$goodscats = Db::name('cat_suppliers')->where('supplierId',$id)->select();
		$supplier['catsuppliers'] = [];
		foreach ($goodscats as $v){
			$supplier['catsuppliers'][$v['catId']] = true;
		}
		return $supplier;
    }
    
	/**
	 * 生成供货商编号
	 * @param $key 编号前缀,要控制不要超过int总长度，最好是一两个字母
	 */
	public function getSupplierSn($key = ''){
		$rs = $this->Max(Db::raw("REPLACE(supplierSn,'S','')+''"));
		if($rs==''){
			return $key.'000000001';
		}else{
			for($i=0;$i<1000;$i++){
			   $num = (int)str_replace($key,'',$rs);
			   $supplierSn = $key.sprintf("%09d",($num+1));
			   $ischeck = $this->checkSupplierSn($supplierSn);
			   if(!$ischeck)return $supplierSn;
			}
			return '';//一直都检测到那就不要强行添加了
		}
	}
	
	/**
	 * 检测供货商编号是否存在
	 */
	public function checkSupplierSn($supplierSn,$supplierId=0){
		$dbo = $this->where(['supplierSn'=>$supplierSn,'dataFlag'=>1]);
		if($supplierId>0)$dbo->where('supplierId','<>',$supplierId);
		$num = $dbo->Count();
		if($num==0)return false;
		return true;
	}
	
    /**
	 * 处理申请
	 */
	public function handleApply(){
        $data = input('post.');
        //var_dump($data);die;
        $supplierId = (int)$data['supplierId'];
		$suppliers = $this->get($supplierId);
		if(empty($suppliers))return WSTReturn('操作失败，该入驻申请不存在');
		if($suppliers->applyStatus==2)return WSTReturn('该入驻申请已通过',1);
        //新增入驻申请
        //先遍历前台传来的data,根据supplier_base表判断是属于suppliers表还是supplier_extras表，分别用两个数组保存
        $suppliersData = [];
        $supplierExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadSuppliersImgPath = [];
        $uploadSupplierExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('supplier_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isSuppliersTable,dateRelevance,isShow,isRequire')->find();
            if($field['isSuppliersTable']==1){
                // 属于suppliers表
                $suppliersData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($suppliersData[$k]);
                    if(!empty($areaIds))$suppliersData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSuppliersImgPath[] = $data[$k];
                }
            }else{
                // 属于supplier_extras表
                $supplierExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($supplierExtrasData[$k]);
                    if(!empty($areaIds))$supplierExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSupplierExtrasImgPath[] = $data[$k];
                }
                // 日期字段入库前处理
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'date'){
                    // 当日期字段不是必填项，需删除该字段
                    if($field['isRequire'] == 0){
                        $unsetField[] = $field['fieldName'];
                    }
                    if($field['dateRelevance']){
                        $dateRelevance = explode(',',$field['dateRelevance']);
                        // 如果选择了长期，就删除字段的结束日期
                        if($data[$dateRelevance[1]]==1){
                            $unsetField[] = $dateRelevance[0];
                        }
                    }
                }
                //经营范围
                if(!empty($data['goodsCatIds']))$goodsCats = explode(',',$data['goodsCatIds']);
            }
        }
        // 删除无需入库的字段
        foreach($supplierExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($supplierExtrasData[$k]);
            }
        }

        $validate = new VSupplierBase();
        $validate->setRuleAndMessage($suppliersData);
        $validate->setRuleAndMessage($supplierExtrasData);

        //判断经营范围
        $goodsCatIds = input('post.goodsCatIds');
        $accredIds = input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');

        $data['applyStatus'] = ($data['applyStatus']==2)?2:-1;
        if($data['applyStatus']!=2 && $data['applyDesc']==''){
            return WSTReturn('请输入审核不通过原因');
        }
        if($data['applyStatus']==2){
        	$tuser = Db::name('users')->where('userId',$suppliers->userId)->field("userType")->find();
        	if($tuser['userType']>0){
        		return WSTReturn('该用户已开通商家身份，不能再开通供货商！');
        	}
        }
        Db::startTrans();
        try{
	        //保存供货商基础信息
            $suppliersData['supplierId'] = $supplierId;
            $suppliersData['applyStatus'] = $data['applyStatus'];
            $suppliersData['applyDesc'] = $data['applyDesc'];
            //检测供货商编号是否存在
            if($data['supplierSn']==''){
            	$suppliersData['supplierSn'] = $this->getSupplierSn('S');
            }else{
            	if(!$this->checkSupplierSn($data['supplierSn'],$supplierId)){
            		$suppliersData['supplierSn'] = $data['supplierSn'];
            	}else{
                    return WSTReturn('该供货商编号已存在');
            	}
            }
            $supplierExtrasData['supplierId'] = $supplierId;
            if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());

	        WSTUnset($data,'id,supplierId,userId,dataFlag,createTime,goodsCatIds,accredIds');
	        if($data['applyStatus']==2 && $data['supplierSn']=='')$suppliersData['supplierSn'] = $this->getSupplierSn('S');
            $this->allowField(true)->save($suppliersData,['supplierId'=>$supplierId]);
            foreach($uploadSuppliersImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $this->supplierId, $v ,'suppliers');
            }
            //更改用户身份
            if($data['applyStatus']==2){
                Db::name('users')->where('userId',$suppliers->userId)->update(['userType'=>3]);
            }
            $seModel = model('SupplierExtras');
            $seModel->allowField(true)->save($supplierExtrasData,['supplierId'=>$supplierId]);
            $extraId = $seModel->where(['supplierId'=>$supplierId])->value('id');// 获取主键
            foreach($uploadSupplierExtrasImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $extraId, $v ,'supplierextras');
            }


		    //经营范围
		    Db::name('cat_suppliers')->where('supplierId','=',$supplierId)->delete();
		    $goodsCats = explode(',',$goodsCatIds);
		    foreach ($goodsCats as $key =>$v){
		        if((int)$v>0){
		        	Db::name('cat_suppliers')->insert(['supplierId'=>$supplierId,'catId'=>$v]);
		        }
		    }
		
	        if($data['applyStatus']==2){
	        	//建立供货商配置信息
		        $sc = [];
		        $sc['supplierId'] = $supplierId;
		        Db::name('SupplierConfigs')->insert($sc);
		        $su = [];
	        	$su["supplierId"] = $supplierId;
	        	$su["userId"] = $suppliers->userId;
	        	$su["roleId"] = 0;
	        	Db::name('supplier_users')->insert($su);
		        //建立供货商评分记录
				$ss = [];
				$ss['supplierId'] = $supplierId;
				Db::name('supplier_scores')->insert($ss);
	        }
	        if($suppliers->applyStatus!=$data['applyStatus'])$this->sendMessages($supplierId,$suppliers->userId,$data,'handleApply');
	        Db::commit();
            if($data['applyStatus']==2){
                return WSTReturn("操作成功", 1);
            }else{
                $refundRes = '';
                // 用户还未支付
                if($suppliers->isPay==0)return WSTReturn("操作成功",1);
                // 审核不通过，将年费退款给用户
                if($suppliers->isRefund==1)return WSTReturn("操作成功，年费已退款");
                $supplierFee = Db::name('supplier_fees')->where(['supplierId'=>$supplierId,'dataFlag'=>1,'isRefund'=>0])->find();
                $logs = Db::name('log_moneys')->where(['id'=>$supplierFee['logMoneyId']])->find();
                $obj = array();
                $obj['userId'] = $suppliers->userId;
                $obj['orderNo'] = $logs['dataId'];
                $obj['money'] = $logs['money'];
                if($logs['payType']=='alipays'){
                    $obj['tradeNo'] = $logs['tradeNo'];
                    $am = model("admin/Alipays");
                    $refundRes = $am->supplierEnterRefund($obj);
                }elseif($logs['payType']=='weixinpays'){
                    $obj['tradeNo'] = $logs['tradeNo'];
                    $wm = model("admin/Weixinpays");
                    $refundRes = $wm->supplierEnterRefund($obj);
                }else{
                    $wm = model("admin/Wallets");
                    $refundRes = $wm->supplierEnterRefund($obj);
                }
                return $refundRes;
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('操作失败',-1);
        }
	}

	/**
	 * 发送信息
	 */
	public function sendMessages($supplierId,$userId,$data,$method){
	    $user = model('users')->get($userId);
	    $suppliers = model('suppliers')->get($supplierId);
	    if((int)$data['applyStatus']==2){
            //如果存在手机则发送手机号码提示
		    $tpl = WSTMsgTemplates('PHONE_USER_SHOP_OPEN_SUCCESS');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['applyLinkTel']!=''){
		        $params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf("CONF.mallName"),'LOGIN_NAME'=>$user->loginName]];
		        $rv = model('admin/LogSms')->sendSMS(0,$userId,$data['applyLinkTel'],$params,$method);
		    }
		    //发送邮件
		    $tpl = WSTMsgTemplates('EMAIL_USER_SHOP_OPEN_SUCCESS');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['applyLinkEmail']){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}'];
		        $replace = [$user->loginName,WSTConf("CONF.mallName")];
		        $sendRs = WSTSendMail($data['applyLinkEmail'],'申请入驻审核通过',str_replace($find,$replace,$tpl['content']));
		    }
		    // 会员发送一条商城消息
	        $tpl = WSTMsgTemplates('SHOP_OPEN_SUCCESS');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1'){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}'];
		        $replace = [$user->loginName,WSTConf("CONF.mallName")];
		        WSTSendMsg($userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>$supplierId]);
		    }
		
	    }else{   	
	        //如果存在手机则发送手机号码提示
		    $tpl = WSTMsgTemplates('PHONE_SHOP_OPEN_FAIL');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['applyLinkTel']!=''){
		        $params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf("CONF.mallName"),'REASON'=>$data['applyDesc']]];
		        $rv = model('admin/LogSms')->sendSMS(0,$userId,$data['applyLinkTel'],$params,$method);
		    }
		    //发送邮件
		    $tpl = WSTMsgTemplates('EMAIL_SHOP_OPEN_FAIL');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['applyLinkEmail']){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}','${REASON}'];
		        $replace = [$user->loginName,WSTConf("CONF.mallName"),$data['applyDesc']];
		        $sendRs = WSTSendMail($data['applyLinkEmail'],'申请入驻失败',str_replace($find,$replace,$tpl['content']));
		    }
	    	// 会员发送一条商城消息
	    	$tpl = WSTMsgTemplates('SHOP_OPEN_FAIL');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1'){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}','${REASON}'];
		        $replace = [$user->loginName,WSTConf("CONF.mallName"),$data['applyDesc']];
		        WSTSendMsg($userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>$supplierId]);
		    }
		     
	    }
	}
	/**
	 * 删除申请
	 */
	public function delApply(){
	    $id = input('post.id/d');
	    $supplier = $this->get($id);
	    if($supplier->applyStatus==2)return WSTReturn('通过申请的供货商不允许删除');
		Db::startTrans();
        try{
            //删除供货商信息
            Db::name('cat_suppliers')->where(['supplierId'=>$id])->delete();
            Db::name('supplier_extras')->where(['supplierId'=>$id])->delete();
            Db::name('suppliers')->where(['supplierId'=>$id])->delete();
            WSTUnuseResource('suppliers','supplierImg',$id);
            Db::commit();
            return WSTReturn("删除成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
        //新增入驻申请
        // 先遍历前台传来的data,根据supplier_base表判断是属于suppliers表还是supplier_extras表，分别用两个数组保存
        $suppliersData = [];
        $supplierExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadSuppliersImgPath = [];
        $uploadSupplierExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('supplier_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isSuppliersTable,dateRelevance,isShow,isRequire')->find();
            if($field['isSuppliersTable']==1){
                // 属于suppliers表
                $suppliersData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($suppliersData[$k]);
                    if(!empty($areaIds))$suppliersData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSuppliersImgPath[] = $data[$k];
                }
            }else{
                // 属于supplier_extras表
                $supplierExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($supplierExtrasData[$k]);
                    if(!empty($areaIds))$supplierExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSupplierExtrasImgPath[] = $data[$k];
                }
                // 日期字段入库前处理
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'date'){
                    // 当日期字段不是必填项，需删除该字段
                    if($field['isRequire'] == 0){
                        $unsetField[] = $field['fieldName'];
                    }
                    if($field['dateRelevance']){
                        $dateRelevance = explode(',',$field['dateRelevance']);
                        // 如果选择了长期，就删除字段的结束日期
                        if($data[$dateRelevance[1]]==1){
                            $unsetField[] = $dateRelevance[0];
                        }
                    }
                }
                //经营范围
                if(!empty($data['goodsCatIds']))$goodsCats = explode(',',$data['goodsCatIds']);
            }
        }
        // 删除无需入库的字段
        foreach($supplierExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($supplierExtrasData[$k]);
            }
        }

        $validate = new VSupplierBase();
        $validate->setRuleAndMessage($suppliersData);
        $validate->setRuleAndMessage($supplierExtrasData);
        $suppliersData['applyStatus'] = 2;
        if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());

	    WSTUnset($data,'id,supplierId,userId,dataFlag,createTime,goodsCatIds,accredIds');
        //判断经营范围
        $goodsCatIds = input('post.goodsCatIds');
        $accredIds = input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');
        if(input('expireDate')=='')return WSTReturn('请选择供货商到期日期');
        $suppliersData['expireDate'] = input('expireDate');
        Db::startTrans();
        try{
        	$userId = 0;
        	$isNewUser = (int)input('post.isNew/d');
        	if($isNewUser==1){
	        	//创建用户账号
	        	$user = [];
				$user['loginName'] = input('post.loginName');
				$user['loginPwd'] = input('post.loginPwd');
				$ck = WSTCheckLoginKey($user['loginName']);
				if($ck['status']!=1)return $ck;
				if($user['loginPwd']=='')$user['loginPwd'] = '88888888';
				$loginPwd = $user['loginPwd'];
				$user["loginSecret"] = rand(1000,9999);
		    	$user['loginPwd'] = md5($user['loginPwd'].$user['loginSecret']);
		    	$user["userType"] = 3;
		    	$user['createTime'] = date('Y-m-d H:i:s');
	            model('users')->save($user);
		        $userId = model('users')->userId;
		    }else{
		    	$userId = (int)input('post.supplierUserId/d');
		    	//检查用户是否可用
		    	$supplierUser = model('users')->where(['userId'=>$userId,'dataFlag'=>1])->find();
		    	if(empty($supplierUser))return WSTReturn('无效的账号信息');
		    	if($supplierUser['userType']>0 && $supplierUser['userType']!=3)return WSTReturn('所关联账号已有店铺/门店信息，不能关联供货商');
		    	$tmpSupplier = $this->where(['dataFlag'=>1,'userId'=>$userId])->find();
		    	if(!empty($tmpSupplier))return WSTReturn('所关联账号已有供货商信息');
		    	$supplierUser->userType = 3;
		    	$supplierUser->save();
		    }
	        if($userId>0){
	        	//创建商家基础信息
	        	$suppliersData['userId'] = $userId;
	        	$suppliersData['applyTime'] = date('Y-m-d H:i:s');
	        	$suppliersData['createTime'] = date('Y-m-d');
	        	$suppliersData['supplierSn'] = ($data['supplierSn']=='')?$this->getSupplierSn('S'):$data['supplierSn'];
	            $this->allowField(true)->save($suppliersData);
	            $supplierId = $this->supplierId;
	            foreach($uploadSuppliersImgPath as $k => $v){
	                //启用上传图片
	                WSTUseResource(0, $supplierId, $v ,'suppliers');
	            }
	            $supplierExtrasData['supplierId'] = $supplierId;
	            $seModel = model('SupplierExtras');
	            $seModel->allowField(true)->save($supplierExtrasData);
	            $extraId = $seModel->where(['supplierId'=>$supplierId])->value('id');// 获取主键
	            foreach($uploadSupplierExtrasImgPath as $k => $v){
	                //启用上传图片
	                WSTUseResource(0, $extraId, $v ,'supplierextras');
	            }

	            //经营范围
			    Db::name('cat_suppliers')->where('supplierId','=',$supplierId)->delete();
			    $goodsCats = explode(',',$goodsCatIds);
			    foreach ($goodsCats as $key =>$v){
			        if((int)$v>0){
			        	Db::name('cat_suppliers')->insert(['supplierId'=>$supplierId,'catId'=>$v]);
			        }
			    }
			    
		        //建立供货商配置信息
			    $sc = [];
			    $sc['supplierId'] = $supplierId;
			    Db::name('SupplierConfigs')->insert($sc);
			    $su = [];
		        $su["supplierId"] = $supplierId;
		        $su["userId"] = $userId;
		        $su["roleId"] = 0;
		        Db::name('supplier_users')->insert($su);
			    //建立供货商评分记录
				$ss = [];
			    $ss['supplierId'] = $supplierId;
				Db::name('supplier_scores')->insert($ss);
	            Db::commit();
	        }
	        
	        return WSTReturn("新增成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$data = input('post.');
		$supplierId = input('post.supplierId/d',0);
		$suppliers = $this->get($supplierId);
		if(empty($suppliers) || $suppliers->dataFlag!=1)return WSTReturn('供货商不存在');
		//先遍历前台传来的data,根据supplier_base表判断是属于suppliers表还是supplier_extras表，分别用两个数组保存
        $suppliersData = [];
        $supplierExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadSuppliersImgPath = [];
        $uploadSupplierExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('supplier_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isSuppliersTable,dateRelevance,isShow,isRequire')->find();
            if($field['isSuppliersTable']==1){
                // 属于suppliers表
                $suppliersData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaId = $suppliersData[$k];
                    $areaIds = model('Areas')->getParentIs($suppliersData[$k]);
                    if(!empty($areaIds))$suppliersData[$k] = implode('_',$areaIds)."_";
                    if($field['fieldName'] == 'areaIdPath')$suppliersData['areaId'] = $areaId;
                    if($field['fieldName'] == 'bankAreaIdPath')$suppliersData['bankAreaId'] = $areaId;
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSuppliersImgPath[] = $data[$k];
                }
            }else{
                // 属于supplier_extras表
                $supplierExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($supplierExtrasData[$k]);
                    if(!empty($areaIds))$supplierExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSupplierExtrasImgPath[] = $data[$k];
                }
                // 日期字段入库前处理
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'date'){
                    // 当日期字段不是必填项，需删除该字段
                    if($field['isRequire'] == 0){
                        $unsetField[] = $field['fieldName'];
                    }
                    if($field['dateRelevance']){
                        $dateRelevance = explode(',',$field['dateRelevance']);
                        // 如果选择了长期，就删除字段的结束日期
                        if($data[$dateRelevance[1]]==1){
                            $unsetField[] = $dateRelevance[0];
                        }
                    }
                }
                //经营范围
                if(!empty($data['goodsCatIds']))$goodsCats = explode(',',$data['goodsCatIds']);
            }
        }

        // 删除无需入库的字段
        foreach($supplierExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($supplierExtrasData[$k]);
            }
        }

        $validate = new VSupplierBase();
        $validate->setRuleAndMessage($suppliersData);
        $validate->setRuleAndMessage($supplierExtrasData);

        //判断经营范围
        $goodsCatIds = input('post.goodsCatIds');
        $accredIds = input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');
        if(input('expireDate')=='')return WSTReturn('请选择供货商到期日期');
        $suppliersData['expireDate'] = input('expireDate');

        Db::startTrans();
        try{
        	//检测供货商编号是否存在
            if($data['supplierSn']==''){
            	$suppliersData['supplierSn'] = $this->getSupplierSn('S');
            }else{
            	if(!$this->checkSupplierSn($data['supplierSn'],$supplierId)){
            		$suppliersData['supplierSn'] = $data['supplierSn'];
            	}else{
                    return WSTReturn('该供货商编号已存在');
            	}
            }
            $suppliersData['supplierId'] = $supplierId;
            $suppliersData['supplierStatus'] = ((int)input('supplierStatus')==1)?1:-1;
            if($suppliersData['supplierStatus']==0){
            	$suppliersData['statusDesc'] = input('statusDesc');
            	if($suppliersData['statusDesc']=='')return WSTReturn('请输入停止原因');
            }
            $supplierExtrasData['supplierId'] = $supplierId;
            if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
	        WSTUnset($data,'id,supplierId,userId,dataFlag,createTime,goodsCatIds,accredIds');
            $this->allowField(true)->save($suppliersData,['supplierId'=>$supplierId]);
            foreach($uploadSuppliersImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $this->supplierId, $v ,'suppliers');
            }
            $seModel = model('SupplierExtras');
            $seModel->allowField(true)->save($supplierExtrasData,['supplierId'=>$supplierId]);
            $extraId = $seModel->where(['supplierId'=>$supplierId])->value('id');// 获取主键
            foreach($uploadSupplierExtrasImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $extraId, $v ,'supplierextras');
            }
		    //经营范围
		    Db::name('cat_suppliers')->where('supplierId','=',$supplierId)->delete();
		    $goodsCats = explode(',',$goodsCatIds);
		    foreach ($goodsCats as $key =>$v){
		        if((int)$v>0){
		        	Db::name('cat_suppliers')->insert(['supplierId'=>$supplierId,'catId'=>$v]);
		        }
		    }
		    
		    if((int)input('supplierStatus')!=1){
		         //供货商状态不正常就停用所有的商品
		        model('supplier_goods')->unsaleBysupplierId($supplierId);
		    } 
	        Db::commit();
	        return WSTReturn("编辑成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	/**
	* 获取所有供货商id
	*/
	public function getAllSupplierUserId(){
		return $this->where(['dataFlag'=>1,'supplierStatus'=>1])->column('userId');
	}
	
	/**
	 * 搜索经验范围的供货商
	 */
	public function searchQuery(){
		$goodsCatatId = (int)input('post.goodsCatId');
		if($goodsCatatId<=0)return [];
		$key = input('post.key');
		$where = [];
		$where[] = ['dataFlag','=',1];
		$where[] = ['supplierStatus','=',1];
		$where[] = ['catId','=',$goodsCatatId];
		if($key!='')$where[] = ['supplierName|supplierSn','like','%'.$key.'%'];
		return $this->alias('s')->join('__CAT_SUPPLIERS__ cs','s.supplierId=cs.supplierId','inner')
		            ->where($where)->field('supplierName,s.supplierId,supplierSn')->select();
	}

    /*
     * 入驻审核不通过，处理退款（退款到支付宝成功回调方法）
     */
    public function completeEnterRefund($obj){
        Db::startTrans();
        try{
            // 更新店铺的到期日期、退款状态
            $supplier = $this->where(['userId'=>$obj['userId']])->find();
            $supplierExpireDate = $supplier["expireDate"];
            $newExpireDate = date('Y-m-d',strtotime("$supplierExpireDate -1 year"));
            $suppliersData['expireDate'] = $newExpireDate;
            $suppliersData['isRefund'] = 1;
            $this->where(['userId'=>$obj['userId']])->update($suppliersData);
            // 更新缴费记录
            Db::name('supplier_fees')->where(['supplierId'=>$supplier['supplierId'],'dataFlag'=>1,'isRefund'=>0])->update(['isRefund'=>1]);
            Db::commit();
            return WSTReturn("退款成功",1);
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn("退款失败，请刷新后再重试");
    }

    /**
     * 缴纳年费记录
     */
    public function renewMoneyByPage(){
        $supplierName = input("supplierName");
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $where[] = ['f.dataFlag','=', 1];
        if($supplierName!="")$where[] = ['s.supplierName','like', '%'.$supplierName.'%'];
        $rs =  Db::name("supplier_fees")
            ->alias('f')
            ->join('__SUPPLIERS__ s','s.userId=f.userId','left')
            ->order('id','desc')
            ->whereTime('f.createTime','between',[$start,$end])
            ->where(['f.isRefund'=>0])
            ->where($where)
            ->field("sum(f.money) totalRenewMoney")
            ->find();
        $totalRenewMoney = (float)$rs["totalRenewMoney"];
        $page =  Db::name("supplier_fees")
            ->alias('f')
            ->join('__SUPPLIERS__ s','s.userId=f.userId','left')
            ->order('f.id','desc')
            ->whereTime('f.createTime','between',[$start,$end])
            ->where($where)
            ->field('f.*,s.supplierName')
            ->paginate(input('limit/d'))->toArray();
        if(count($page['data'])>0){
            foreach ($page['data'] as $key => $v) {
                $page['data'][$key]['totalRenewMoney'] = $totalRenewMoney;
            }
        }
        return $page;
    }

    /**
     * 导出缴纳年费记录统计报表excel
     */
    public function toExportRenewMoney(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $where[] = ['f.dataFlag','=', 1];
        $rs =  Db::name("supplier_fees")
            ->alias('f')
            ->join('__SUPPLIERS__ s','s.userId=f.userId','left')
            ->order('f.id','desc')
            ->whereTime('f.createTime','between',[$start,$end])
            ->where($where)
            ->field('f.*,s.supplierName')
            ->select();
        $rs1 =  Db::name("supplier_fees")
            ->order('id','desc')
            ->whereTime('createTime','between',[$start,$end])
            ->where(['dataFlag'=>1,'isRefund'=>0])
            ->field("sum(money) totalRenewMoney")
            ->find();
        $totalRenewMoney = (float)$rs1["totalRenewMoney"];

        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("缴纳年费统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color'=>array(
                    'argb' => 'ffffffff',
                )
            )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A2:G2');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);



        $objPHPExcel->getActiveSheet()
            ->setCellValue('G1', '缴费总金额:'.$totalRenewMoney."元")
            ->setCellValue('A2', '供货商名称')
            ->setCellValue('B2', '缴纳年费描述')
            ->setCellValue('C2', '年费金额')
            ->setCellValue('D2', '外部流水号')
            ->setCellValue('E2', '缴纳时间')
            ->setCellValue('F2', '开始日期')
            ->setCellValue('G2', '结束日期');
        $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleArray);
        $i = 2;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+3;
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A'.$i, $rs[$row]['supplierName'])
                ->setCellValue('B'.$i, ($rs[$row]['isRefund']==1)?$rs[$row]['remark'].'(已退款)':$rs[$row]['remark'])
                ->setCellValue('C'.$i, '￥'.$rs[$row]['money'])
                ->setCellValue('D'.$i, ($rs[$row]['tradeNo']!='')?$rs[$row]['tradeNo']:'-')
                ->setCellValue('E'.$i, $rs[$row]['createTime'])
                ->setCellValue('F'.$i, $rs[$row]['startDate'])
                ->setCellValue('G'.$i, $rs[$row]['endDate']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+2;
        $objPHPExcel->getActiveSheet()->getStyle('A2:G'.$totalRow)->applyFromArray(array(
            'borders' => array (
                'allborders' => array (
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                    'color' => array ('argb' => 'FF000000'),     //设置border颜色
                )
            )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }
}
