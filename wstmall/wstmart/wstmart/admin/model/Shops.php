<?php
namespace wstmart\admin\model;
use think\Db;
use wstmart\admin\validate\HomeShopBase as VShopBase;
use think\Loader;
use Env;
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
 * 店铺业务处理
 */
class Shops extends Base{
	protected $pk = 'shopId';
	/**
	 * 分页
	 */
	public function pageQuery($shopStatus=1){
		$areaIdPath = input('areaIdPath');
		$shopName = input('shopName');
		$isInvestment = (int)input('isInvestment/d',-1);
        $tradeId = (int)input('tradeId');
		$where = [];
		$where[] = ['s.dataFlag','=',1];
		$where[] = ['s.applyStatus','=',2];
		$where[] = ['s.shopStatus','=',$shopStatus];
        if($tradeId>0)$where[] = ['s.tradeId','=',$tradeId];
		if(in_array($isInvestment,[0,1]))$where[] = ['ss.isInvestment','=',$isInvestment];
		if($shopName!='')$where[] = ['shopName','like','%'.$shopName.'%'];
		if($areaIdPath !='')$where[] = ['areaIdPath','like',$areaIdPath."%"];
		$sort = input('sort');
		$order = [];
		if($sort!=''){
			$sortArr = explode('.',$sort);
			$order = $sortArr[0].' '.$sortArr[1];
		}
		$page =  Db::table('__SHOPS__')->alias('s')
                    ->join("trades t","s.tradeId=t.tradeId","left")
                    ->join('__AREAS__ a2','s.areaId=a2.areaId','left')
			       ->join('__USERS__ u','u.userId=s.userId','left')
			       ->join('__SHOP_EXTRAS__ ss','s.shopId=ss.shopId','left')
			       ->where($where)
			       ->field('u.loginName,s.shopId,shopSn,shopName,t.tradeName,a2.areaName,shopkeeper,telephone,shopAddress,shopCompany,shopAtive,shopStatus,expireDate')
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
		$shopName = input('shopName');
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
		if($shopName!='')$where[] = ['shopName','like','%'.$shopName.'%'];
		if($areaIdPath !='')$where[] = ['areaIdPath','like',$areaIdPath."%"];
		return Db::table('__SHOPS__')->alias('s')
                ->join("trades t","s.tradeId=t.tradeId","left")
                ->join('__AREAS__ a2','s.areaId=a2.areaId','left')
		       ->join('__SHOP_EXTRAS__ ss','s.shopId=ss.shopId','left')
		       ->join('__USERS__ u','u.userId=s.userId','left')
		       ->where($where)
		       ->field('u.loginName,s.shopId,applyLinkMan,applyLinkTel,investmentStaff,isInvestment,shopName,t.tradeName,a2.areaName,shopAddress,shopCompany,applyTime,applyStatus')
		       ->order('s.shopId desc')->paginate(input('limit/d'));
	}

	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
	    if($id==1)return WSTReturn('无法删除自营店铺');
		Db::startTrans();
        try{
	        $shop = $this->get($id);
	        $shop->dataFlag = -1;
	        $result = $shop->save();
	        WSTUnuseResource('shops','shopImg',$id);
	        // 店铺申请表的图片标记为删除
	        $imgArr = model('shopExtras')->field('legalCertificateImg,businessLicenceImg,bankAccountPermitImg,organizationCodeImg,taxRegistrationCertificateImg,taxpayerQualificationImg')->where(['shopId'=>$id])->find();
	        WSTUnuseResource($imgArr->getData());
            if(false !== $result){
            	//删除推荐店铺
            	Db::name('recommends')->where(['dataSrc'=>1,'dataId'=>$id])->delete();
            	//删除店铺与商品分类的关系
            	Db::name('cat_shops')->where(['shopId'=>$id])->delete();
            	//删除用户店铺身份
        	    Db::name('users')->where(['userId'=>$shop->userId])->update(['dataFlag'=>-1]);
        	    //删除店铺角色
        	    Db::name('shop_roles')->where(['shopId'=>$id])->update(['dataFlag'=>-1]);
        	    //删除店铺职员
        	    Db::name('shop_users')->where(['shopId'=>$id])->update(['dataFlag'=>-1]);
            	//下架及下架商品
        	    model('goods')->delByshopId($id);
        	    //删除店铺钩子事件
        	    hook('afterChangeShopStatus',['shopId'=>$id]);
        	    Db::commit();
        	    return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}
	/**
	 * 根据根据userId删除店铺
	 */
	public function delByUserId($userId){
		if($userId==1)return WSTReturn('无法删除自营店铺');
	    $shop = $this->where('userId',$userId)->find();
	    if(!$shop)return;
	    $shop->dataFlag = -1;
	    $result = $shop->save();
	    WSTUnuseResource('shops','shopImg',$shop->shopId);
        if(false !== $result){
            //删除推荐店铺
            Db::name('recommends')->where(['dataSrc'=>1,'dataId'=>$shop->shopId])->delete();
            //删除店铺与商品分类的关系
            Db::name('cat_shops')->where(['shopId'=>$shop->shopId])->delete();
            //下架及删除商品
        	model('goods')->delByshopId($shop->shopId);
        	//删除店铺角色
    	    Db::name('shop_roles')->where(['shopId'=>$shop->shopId])->update(['dataFlag'=>-1]);
    	    //删除店铺职员
    	    Db::name('shop_users')->where(['shopId'=>$shop->shopId])->update(['dataFlag'=>-1]);
        	//删除店铺钩子事件
        	hook('afterChangeShopStatus',['shopId'=>$shop->shopId]);
        	return WSTReturn("删除成功", 1);
        }
        return WSTReturn('删除失败',-1);
	}
	/**
     * 获取商家入驻资料
     */
    public function getById($id){
        $shop = $this->alias('s')->join('__SHOP_EXTRAS__ ss','s.shopId=ss.shopId','inner')
                   ->join('__USERS__ u','u.userId=s.userId','inner')
                   ->where('s.shopId',$id)
                   ->find()
                   ->toArray();
        //获取认证类型
	    $shopAccreds = Db::name('shop_accreds')->where('shopId',$id)->select();
	    $shop['accreds'] = [];
		foreach ($shopAccreds as $v){
			$shop['accreds'][$v['accredId']] = true;
		}
        //获取经营范围
		$goodscats = Db::name('cat_shops')->where('shopId',$id)->select();
		$shop['catshops'] = [];
		foreach ($goodscats as $v){
			$shop['catshops'][$v['catId']] = true;
		}
		return $shop;
    }
    
	/**
	 * 生成店铺编号
	 * @param $key 编号前缀,要控制不要超过int总长度，最好是一两个字母
	 */
	public function getShopSn($key = ''){
		$rs = $this->Max(Db::raw("REPLACE(shopSn,'S','')+''"));
		if($rs==''){
			return $key.'000000001';
		}else{
			for($i=0;$i<1000;$i++){
			   $num = (int)str_replace($key,'',$rs);
			   $shopSn = $key.sprintf("%09d",($num+1));
			   $ischeck = $this->checkShopSn($shopSn);
			   if(!$ischeck)return $shopSn;
			}
			return '';//一直都检测到那就不要强行添加了
		}
	}
	
	/**
	 * 检测店铺编号是否存在
	 */
	public function checkShopSn($shopSn,$shopId=0){
		$dbo = $this->where(['shopSn'=>$shopSn,'dataFlag'=>1]);
		if($shopId>0)$dbo->where('shopId','<>',$shopId);
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
        $shopId = (int)$data['shopId'];
		$shops = $this->get($shopId);
		if(empty($shops))return WSTReturn('操作失败，该入驻申请不存在');
		if($shops->applyStatus==2)return WSTReturn('该入驻申请已通过',1);
        //新增入驻申请
        //先遍历前台传来的data,根据shop_base表判断是属于shops表还是shop_extras表，分别用两个数组保存
        $shopsData = [];
        $shopExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadShopsImgPath = [];
        $uploadShopExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('shop_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isShopsTable,dateRelevance,isShow,isRequire')->find();
            if($field['isShopsTable']==1){
                // 属于shops表
                $shopsData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($shopsData[$k]);
                    if(!empty($areaIds))$shopsData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopsImgPath[] = $data[$k];
                }
            }else{
                // 属于shop_extras表
                $shopExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($shopExtrasData[$k]);
                    if(!empty($areaIds))$shopExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopExtrasImgPath[] = $data[$k];
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
        foreach($shopExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($shopExtrasData[$k]);
            }
        }

        $validate = new VShopBase();
        $validate->setRuleAndMessage($shopsData);
        $validate->setRuleAndMessage($shopExtrasData);

        //判断经营范围
        $goodsCatIds = input('post.goodsCatIds');
        $accredIds = input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');

        $data['applyStatus'] = ($data['applyStatus']==2)?2:-1;
        if($data['applyStatus']!=2 && $data['applyDesc']==''){
            return WSTReturn('请输入审核不通过原因');
        }
        if($data['applyStatus']==2){
        	$tuser = Db::name('users')->where('userId',$shops->userId)->field("userType")->find();
        	if($tuser['userType']>0){
        		return WSTReturn('该用户已开通供货商身份，不能再开通商家！');
        	}
        }
        Db::startTrans();
        try{
	        //保存店铺基础信息
            $shopsData['shopId'] = $shopId;
            $shopsData['applyStatus'] = $data['applyStatus'];
            $shopsData['applyDesc'] = $data['applyDesc'];
            //检测店铺编号是否存在
            if($data['shopSn']==''){
            	$shopsData['shopSn'] = $this->getShopSn('S');
            }else{
            	if(!$this->checkShopSn($data['shopSn'],$shopId)){
            		$shopsData['shopSn'] = $data['shopSn'];
            	}else{
                    return WSTReturn('该店铺编号已存在');
            	}
            }
            $shopExtrasData['shopId'] = $shopId;
            if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());

	        WSTUnset($data,'id,shopId,userId,dataFlag,createTime,goodsCatIds,accredIds,isSelf');
	        if($data['applyStatus']==2 && $data['shopSn']=='')$shopsData['shopSn'] = $this->getShopSn('S');
            $this->allowField(true)->save($shopsData,['shopId'=>$shopId]);
            foreach($uploadShopsImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $this->shopId, $v ,'shops');
            }
            //更改用户身份
            if($data['applyStatus']==2){
                Db::name('users')->where('userId',$shops->userId)->update(['userType'=>1]);
            }
            $seModel = model('ShopExtras');
            $seModel->allowField(true)->save($shopExtrasData,['shopId'=>$shopId]);
            $extraId = $seModel->where(['shopId'=>$shopId])->value('id');// 获取主键
            foreach($uploadShopExtrasImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $extraId, $v ,'shopextras');
            }

		    //经营范围
		    Db::name('cat_shops')->where('shopId','=',$shopId)->delete();
		    $goodsCats = explode(',',$goodsCatIds);
		    foreach ($goodsCats as $key =>$v){
		        if((int)$v>0){
		        	Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$v]);
		        }
		    }
		    //认证类型
		    Db::name('shop_accreds')->where('shopId','=',$shopId)->delete();
	        if($accredIds!=''){
	            $accreds = explode(',',$accredIds);
		        foreach ($accreds as $key =>$v){
			        if((int)$v>0){
			        	Db::name('shop_accreds')->insert(['shopId'=>$shopId,'accredId'=>$v]);
			        }
			    }
	        }
            if($shops->applyStatus!=$data['applyStatus'])$this->sendMessages($shopId,$shops->userId,$data,'handleApply');

	        if($data['applyStatus']==2){
	        	//建立店铺配置信息
		        $sc = [];
		        $sc['shopId'] = $shopId;
		        Db::name('ShopConfigs')->insert($sc);
		        $su = [];
	        	$su["shopId"] = $shopId;
	        	$su["userId"] = $shops->userId;
	        	$su["roleId"] = 0;
	        	Db::name('shop_users')->insert($su);
		        //建立店铺评分记录
				$ss = [];
				$ss['shopId'] = $shopId;
				Db::name('shop_scores')->insert($ss);
	        }

	        Db::commit();
            if($data['applyStatus']==2){
                return WSTReturn("操作成功", 1);
            }else{
                $refundRes = '';
                // 用户还未支付
                if($shops->isPay==0)return WSTReturn("操作成功",1);
                // 审核不通过，将年费退款给用户
                if($shops->isRefund==1)return WSTReturn("操作成功，年费已退款");
                $shopFee = Db::name('shop_fees')->where(['shopId'=>$shopId,'dataFlag'=>1,'isRefund'=>0])->find();
                $logs = Db::name('log_moneys')->where(['id'=>$shopFee['logMoneyId']])->find();
                $obj = array();
                $obj['userId'] = $shops->userId;
                $obj['orderNo'] = $logs['dataId'];
                $obj['money'] = $logs['money'];
                if($logs['payType']=='alipays'){
                    $obj['tradeNo'] = $logs['tradeNo'];
                    $am = model("admin/Alipays");
                    $refundRes = $am->enterRefund($obj);
                }elseif($logs['payType']=='weixinpays'){
                    $obj['tradeNo'] = $logs['tradeNo'];
                    $wm = model("admin/Weixinpays");
                    $refundRes = $wm->enterRefund($obj);
                }else{
                    $wm = model("admin/Wallets");
                    $refundRes = $wm->enterRefund($obj);
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
	public function sendMessages($shopId,$userId,$data,$method){
	    $user = model('users')->get($userId);
	    $shops = model('shops')->get($shopId);
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
		        WSTSendMsg($userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>$shopId]);
		    }
		    //微信消息
		    if((int)WSTConf('CONF.wxenabled')==1){
			    $params = [];
			    $params['SHOP_NAME'] = $shops['shopName'];
				$params['APPLY_TIME'] = $shops['applyTime'];
				$params['NOW_TIME'] = date('Y-m-d H:i:s');
				$params['REASON'] = "申请入驻成功";
				WSTWxMessage(['CODE'=>'WX_SHOP_OPEN_SUCCESS','userId'=>$userId,'params'=>$params]);
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
		        WSTSendMsg($userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>$shopId]);
		    }
		    //微信消息
			if((int)WSTConf('CONF.wxenabled')==1){
				$params = [];
				$params['SHOP_NAME'] = $shops['shopName'];
				$params['APPLY_TIME'] = $shops['applyTime'];
				$params['NOW_TIME'] = date('Y-m-d H:i:s');
				$params['REASON'] = $data['applyDesc'];
				WSTWxMessage(['CODE'=>'WX_SHOP_OPEN_FAIL','userId'=>$userId,'params'=>$params]);
			} 
	    }
	}
	/**
	 * 删除申请
	 */
	public function delApply(){
	    $id = input('post.id/d');
	    $shop = $this->get($id);
	    if($shop->applyStatus==2)return WSTReturn('通过申请的店铺不允许删除');
		Db::startTrans();
        try{
            //删除店铺信息
            Db::name('cat_shops')->where(['shopId'=>$id])->delete();
            Db::name('shop_extras')->where(['shopId'=>$id])->delete();
            Db::name('shops')->where(['shopId'=>$id])->delete();
            WSTUnuseResource('shops','shopImg',$id);
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
        // 先遍历前台传来的data,根据shop_base表判断是属于shops表还是shop_extras表，分别用两个数组保存
        $shopsData = [];
        $shopExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadShopsImgPath = [];
        $uploadShopExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('shop_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isShopsTable,dateRelevance,isShow,isRequire')->find();
            if($field['isShopsTable']==1){
                // 属于shops表
                $shopsData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($shopsData[$k]);
                    if(!empty($areaIds))$shopsData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopsImgPath[] = $data[$k];
                }
            }else{
                // 属于shop_extras表
                $shopExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($shopExtrasData[$k]);
                    if(!empty($areaIds))$shopExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopExtrasImgPath[] = $data[$k];
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
        foreach($shopExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($shopExtrasData[$k]);
            }
        }

        $validate = new VShopBase();
        $validate->setRuleAndMessage($shopsData);
        $validate->setRuleAndMessage($shopExtrasData);
        $shopsData['applyStatus'] = 2;
        if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());

	    WSTUnset($data,'id,shopId,userId,dataFlag,createTime,goodsCatIds,accredIds,isSelf');
        //判断经营范围
        $goodsCatIds = input('post.goodsCatIds');
        $accredIds = input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');
        if(input('expireDate')=='')return WSTReturn('请选择店铺到期日期');
        $shopsData['expireDate'] = input('expireDate');
        $shopName = input("shopName");
        $tsp = Db::name("shops")->where(["dataFlag"=>1,'shopName'=>$shopName])->find();
        if(!empty($tsp)){
        	return WSTReturn('该店铺名称已存在，请重新填写');
        }
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
		    	$user["userType"] = 1;
		    	$user['createTime'] = date('Y-m-d H:i:s');
	            model('users')->save($user);
		        $userId = model('users')->userId;
		    }else{
		    	$userId = (int)input('post.shopUserId/d');
		    	//检查用户是否可用
		    	$shopUser = model('users')->where(['userId'=>$userId,'dataFlag'=>1])->find();
		    	if(empty($shopUser))return WSTReturn('无效的账号信息');
		    	if($shopUser['userType']>0 && $shopUser['userType']!=1)return WSTReturn($shopUser['userType'].'所关联账号已关联其他xx角色类型，不能关联店铺');
		    	$tmpShop = $this->where(['dataFlag'=>1,'userId'=>$userId])->find();
		    	if(!empty($tmpShop))return WSTReturn('所关联账号已有店铺信息');
		    	$shopUser->userType = 1;
		    	$shopUser->save();
		    }
	        if($userId>0){
	        	//创建商家基础信息
	        	$shopsData['userId'] = $userId;
	        	$shopsData['applyTime'] = date('Y-m-d H:i:s');
	        	$shopsData['createTime'] = date('Y-m-d');
	        	$shopsData['shopSn'] = ($data['shopSn']=='')?$this->getShopSn('S'):$data['shopSn'];
	            $this->allowField(true)->save($shopsData);
	            $shopId = $this->shopId;
	            foreach($uploadShopsImgPath as $k => $v){
	                //启用上传图片
	                WSTUseResource(0, $shopId, $v ,'shops');
	            }
	            $shopExtrasData['shopId'] = $shopId;
	            $seModel = model('ShopExtras');
	            $seModel->allowField(true)->save($shopExtrasData);
	            $extraId = $seModel->where(['shopId'=>$shopId])->value('id');// 获取主键
	            foreach($uploadShopExtrasImgPath as $k => $v){
	                //启用上传图片
	                WSTUseResource(0, $extraId, $v ,'shopextras');
	            }

	            //经营范围
			    Db::name('cat_shops')->where('shopId','=',$shopId)->delete();
			    $goodsCats = explode(',',$goodsCatIds);
			    foreach ($goodsCats as $key =>$v){
			        if((int)$v>0){
			        	Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$v]);
			        }
			    }
			    //认证类型
			    Db::name('shop_accreds')->where('shopId','=',$shopId)->delete();
		        if($accredIds!=''){
		            $accreds = explode(',',$accredIds);
			        foreach ($accreds as $key =>$v){
				        if((int)$v>0){
				        	Db::name('shop_accreds')->insert(['shopId'=>$shopId,'accredId'=>$v]);
				        }
				    }
		        }
		        //建立店铺配置信息
			    $sc = [];
			    $sc['shopId'] = $shopId;
			    Db::name('ShopConfigs')->insert($sc);
			    $su = [];
		        $su["shopId"] = $shopId;
		        $su["userId"] = $userId;
		        $su["roleId"] = 0;
		        Db::name('shop_users')->insert($su);
			    //建立店铺评分记录
				$ss = [];
			    $ss['shopId'] = $shopId;
				Db::name('shop_scores')->insert($ss);
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
		$shopId = input('post.shopId/d',0);
		$shops = $this->get($shopId);
		if(empty($shops) || $shops->dataFlag!=1)return WSTReturn('店铺不存在');
		//先遍历前台传来的data,根据shop_base表判断是属于shops表还是shop_extras表，分别用两个数组保存
        $shopsData = [];
        $shopExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadShopsImgPath = [];
        $uploadShopExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('shop_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isShopsTable,dateRelevance,isShow,isRequire')->find();
            if($field['isShopsTable']==1){
                // 属于shops表
                $shopsData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaId = $shopsData[$k];
                    $areaIds = model('Areas')->getParentIs($shopsData[$k]);
                    if(!empty($areaIds))$shopsData[$k] = implode('_',$areaIds)."_";
                    if($field['fieldName'] == 'areaIdPath')$shopsData['areaId'] = $areaId;
                    if($field['fieldName'] == 'bankAreaIdPath')$shopsData['bankAreaId'] = $areaId;
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopsImgPath[] = $data[$k];
                }
            }else{
                // 属于shop_extras表
                $shopExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($shopExtrasData[$k]);
                    if(!empty($areaIds))$shopExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopExtrasImgPath[] = $data[$k];
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
        foreach($shopExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($shopExtrasData[$k]);
            }
        }

        $validate = new VShopBase();
        $validate->setRuleAndMessage($shopsData);
        $validate->setRuleAndMessage($shopExtrasData);

        //判断经营范围
        $goodsCatIds = input('post.goodsCatIds');
        $accredIds = input('post.accredIds');
        if($goodsCatIds=='')return WSTReturn('请选择经营范围');
        if(input('expireDate')=='')return WSTReturn('请选择店铺到期日期');
        $shopsData['expireDate'] = input('expireDate');

        Db::startTrans();
        try{
        	//检测店铺编号是否存在
            if($data['shopSn']==''){
            	$shopsData['shopSn'] = $this->getShopSn('S');
            }else{
            	if(!$this->checkShopSn($data['shopSn'],$shopId)){
            		$shopsData['shopSn'] = $data['shopSn'];
            	}else{
                    return WSTReturn('该店铺编号已存在');
            	}
            }
            $shopsData['shopId'] = $shopId;
            $shopsData['shopStatus'] = ((int)input('shopStatus')==1)?1:-1;
            if($shopsData['shopStatus']==0){
            	$shopsData['statusDesc'] = input('statusDesc');
            	if($shopsData['statusDesc']=='')return WSTReturn('请输入停止原因');
            }

            $shopExtrasData['shopId'] = $shopId;
            if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
	        WSTUnset($data,'id,shopId,userId,dataFlag,createTime,goodsCatIds,accredIds,isSelf');
            $this->allowField(true)->save($shopsData,['shopId'=>$shopId]);
            foreach($uploadShopsImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $this->shopId, $v ,'shops');
            }
            $seModel = model('ShopExtras');
            $seModel->allowField(true)->save($shopExtrasData,['shopId'=>$shopId]);
            $extraId = $seModel->where(['shopId'=>$shopId])->value('id');// 获取主键
            foreach($uploadShopExtrasImgPath as $k => $v){
                //启用上传图片
                WSTUseResource(0, $extraId, $v ,'shopextras');
            }
		    //经营范围
		    Db::name('cat_shops')->where('shopId','=',$shopId)->delete();
		    $goodsCats = explode(',',$goodsCatIds);
		    foreach ($goodsCats as $key =>$v){
		        if((int)$v>0){
		        	Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$v]);
		        }
		    }
		    //认证类型
		    Db::name('shop_accreds')->where('shopId','=',$shopId)->delete();
	        if($accredIds!=''){
	            $accreds = explode(',',$accredIds);
		        foreach ($accreds as $key =>$v){
			        if((int)$v>0){
			        	Db::name('shop_accreds')->insert(['shopId'=>$shopId,'accredId'=>$v]);
			        }
			    }
	        }
		    if((int)input('shopStatus')!=1){
		        //店铺状态不正常就停用所有的商品
		        model('goods')->unsaleByshopId($shopId);
		    } 
		    //改变店铺钩子事件
        	hook('afterChangeShopStatus',['shopId'=>$shopId]);
	        Db::commit();
	        return WSTReturn("编辑成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	/**
	* 获取所有店铺id
	*/
	public function getAllShopUserId(){
		return $this->where(['dataFlag'=>1,'shopStatus'=>1])->column('userId');
	}
	
	/**
	 * 搜索经验范围的店铺
	 */
	public function searchQuery(){
		$goodsCatatId = (int)input('post.goodsCatId');
		if($goodsCatatId<=0)return [];
		$key = input('post.key');
		$where = [];
		$where[] = ['dataFlag','=',1];
		$where[] = ['shopStatus','=',1];
		$where[] = ['catId','=',$goodsCatatId];
		if($key!='')$where[] = ['shopName|shopSn','like','%'.$key.'%'];
		return $this->alias('s')->join('__CAT_SHOPS__ cs','s.shopId=cs.shopId','inner')
		            ->where($where)->field('shopName,s.shopId,shopSn')->select();
	}
	
    /**
	 * 自营自动登录
	 */
	public function selfLogin($id){
        $shopId = $id;
        $userid = $this->where(["dataFlag"=>1, "shopStatus"=>1,"shopId"=>$shopId])->field('userId')->find();
        if(!empty($userid['userId'])){
            $userId = $userid['userId'];
            //获取用户信息
            $u = new Users();
            $rs = $u->getById($userId);
            //获取用户等级
            $rrs = WSTUserRank($rs['userTotalScore']);
            $rs['rankId'] = $rrs['rankId'];
            $rs['rankName'] = $rrs['rankName'];
            $rs['userrankImg'] = $rrs['userrankImg'];
            $ip = request()->ip();
            $u->where(["userId"=>$userId])->update(["lastTime"=>date('Y-m-d H:i:s'),"lastIP"=>$ip]);
            //加载店铺信息
            $shops= new Shops();
            $shop = $shops->where(["userId"=>$userId,"dataFlag" =>1])->find();
            $shop['SHOP_MASTER'] = true;
            $shopMenuMaps = model("common/users")->shopMenuMaps();
            $shop["shopMenuMaps"] = $shopMenuMaps;
            if(!empty($shop))$rs = array_merge($shop->toArray(),$rs->toArray());
            //记录登录日志
            $data = array();
            $data["userId"] = $userId;
            $data["loginTime"] = date('Y-m-d H:i:s');
            $data["loginIp"] = $ip;
            Db::name('log_user_logins')->insert($data);
            if($rs['userPhoto']=='')$rs['userPhoto'] = WSTConf('CONF.userLogo');
            $rs["roleId"] = 0;
            session('WST_USER',$rs);
            hook('afterUserLogin',['user'=>$rs]);
            return WSTReturn("","1");
        }
        return WSTReturn("",-1);
    }


	/*
	 * 入驻审核不通过，处理退款（退款到支付宝成功回调方法）
	 */
	public function completeEnterRefund($obj){
        Db::startTrans();
        try{
            // 更新店铺的到期日期、退款状态
            $shop = $this->where(['userId'=>$obj['userId']])->find();
            $shopExpireDate = $shop["expireDate"];
            $newExpireDate = date('Y-m-d',strtotime("$shopExpireDate -1 year"));
            $shopsData['expireDate'] = $newExpireDate;
            $shopsData['isRefund'] = 1;
            $this->where(['userId'=>$obj['userId']])->update($shopsData);
            // 更新缴费记录
            Db::name('shop_fees')->where(['shopId'=>$shop['shopId'],'dataFlag'=>1,'isRefund'=>0])->update(['isRefund'=>1]);
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
        $shopName = input("shopName");
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $where[] = ['f.dataFlag','=', 1];
        if($shopName!="")$where[] = ['s.shopName','like', '%'.$shopName.'%'];
        $rs =  Db::name("shop_fees")
            ->alias('f')
            ->join('__SHOPS__ s','s.userId=f.userId','left')
            ->order('id','desc')
            ->whereTime('f.createTime','between',[$start,$end])
            ->where(['f.isRefund'=>0])
            ->where($where)
            ->field("sum(f.money) totalRenewMoney")
            ->find();
        $totalRenewMoney = (float)$rs["totalRenewMoney"];
        $page =  Db::name("shop_fees")
            ->alias('f')
            ->join('__SHOPS__ s','s.userId=f.userId','left')
            ->order('f.id','desc')
            ->whereTime('f.createTime','between',[$start,$end])
            ->where($where)
            ->field('f.*,s.shopName')
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
        $rs =  Db::name("shop_fees")
            ->alias('f')
            ->join('__SHOPS__ s','s.userId=f.userId','left')
            ->order('f.id','desc')
            ->whereTime('f.createTime','between',[$start,$end])
            ->where($where)
            ->field('f.*,s.shopName')
            ->select();
        $rs1 =  Db::name("shop_fees")
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
            ->setCellValue('A2', '店铺名称')
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
                ->setCellValue('A'.$i, $rs[$row]['shopName'])
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


    /**
     * 缴纳年费记录
     */
    public function statRenewMoneyByPage(){
        $isOpenSupplier = (int)WSTConf('CONF.isOpenSupplier');
        $type = (int)input("type");
        $shopName = input("key");
        $startDate = input('startDate');
        $endDate = input('endDate');
        $where = [];
        $where[] = ['f.dataFlag','=', 1];
        $where[] = ['f.isRefund','=', 0];
        if($startDate!='')$where[] = ['f.createTime','>=',$startDate.' 00:00:00'];
        if($endDate!='')$where[] = ['f.createTime','<=',$endDate.' 23:59:59'];
        if($shopName!="")$where[] = ['shopName','like', '%'.$shopName.'%'];
        if($type==0){
            $sqla = Db::name("shop_fees")->alias('f')
                    ->join('shops s','s.shopId=f.shopId','left')
                    ->where($where)
                    ->field("f.id,f.userId,f.money,f.isRefund,f.remark,f.tradeNo,f.startDate,f.endDate,f.createTime,s.shopName,'1' type")
                    ->buildSql();
            $sqlb = Db::name("shop_fees")->alias('f')->join('shops s','s.shopId=f.shopId','left')->field("f.id,f.money")->where($where)->buildSql();
            if($isOpenSupplier==1){
                $sqla = Db::name("supplier_fees")->alias('f')
                    ->join('suppliers s','s.supplierId=f.supplierId','left')
                    ->where($where)
                    ->field("f.id,f.userId,f.money,f.isRefund,f.remark,f.tradeNo,f.startDate,f.endDate,f.createTime,s.supplierName shopName,'3' type")
                    ->unionAll($sqla)
                    ->buildSql();
                $sqlb = Db::name("supplier_fees")->alias('f')->join('suppliers s','s.supplierId=f.supplierId','left')->field("f.id,f.money")->where($where)->unionAll($sqlb)->buildSql();
            }

            $totalRenewMoney = Db::table($sqlb." f")->sum('money');
            $page = Db::table($sqla." f")
                    ->order('f.id','desc')
                    ->paginate(input('limit/d'))->toArray();
           
        }else if($type==1){//商家
            $rs =  Db::name("shop_fees")
                ->alias('f')
                ->join('shops s','s.shopId=f.shopId','left')
                ->order('id','desc')
                ->where($where)
                ->field("sum(f.money) totalRenewMoney")
                ->find();
            $totalRenewMoney = (float)$rs["totalRenewMoney"];
            $page =  Db::name("shop_fees")
                ->alias('f')
                ->join('shops s','s.shopId=f.shopId','left')
                ->order('f.id','desc')
                ->where($where)
                ->field("f.id,f.userId,f.money,f.isRefund,f.remark,f.tradeNo,f.startDate,f.endDate,f.createTime,s.shopName,'1' type")
                ->paginate(input('limit/d'))->toArray();
        }else if($type==3 && $isOpenSupplier==1){//供货商
            $rs =  Db::name("supplier_fees")
                ->alias('f')
                ->join('suppliers s','s.supplierId=f.supplierId','left')
                ->order('id','desc')
                ->where($where)
                ->field("sum(f.money) totalRenewMoney")
                ->find();
            $totalRenewMoney = (float)$rs["totalRenewMoney"];
            $page =  Db::name("supplier_fees")
                ->alias('f')
                ->join('suppliers s','s.supplierId=f.supplierId','left')
                ->order('f.id','desc')
                ->where($where)
                ->field("f.id,f.userId,f.money,f.isRefund,f.remark,f.tradeNo,f.startDate,f.endDate,f.createTime,s.supplierName shopName,'3' type")
                ->paginate(input('limit/d'))->toArray();
        }
        
        if(count($page['data'])>0){
            foreach ($page['data'] as $key => $v) {
                $page['data'][$key]['totalRenewMoney'] = $totalRenewMoney;
            }
        }
        return $page;
    }
}
