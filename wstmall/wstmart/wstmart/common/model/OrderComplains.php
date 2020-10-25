<?php
namespace wstmart\common\model;
use wstmart\common\validate\OrderComplains as Validate;
use think\Db;
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
 * 订单投诉类
 */
class OrderComplains extends Base{
	protected $pk = 'complainId';
	 /**
	  * 获取用户投诉列表
	  */
	public function queryUserComplainByPage($uId=0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$orderNo = (int)Input('orderNo');
		$where = [];
		$where[] = ['o.userId',"=",$userId];
		if($orderNo>0){
			$where[] = ['o.orderNo','like',"%$orderNo%"];
		}
		$rs = $this->alias('oc')
				   ->field('oc.complainId,o.orderId,o.orderNo,s.shopId,s.shopName,oc.complainContent,oc.complainStatus,oc.complainTime,o.orderCode')
				   ->join('__SHOPS__ s','oc.respondTargetId=s.shopId','left')
				   ->join('__ORDERS__ o','oc.orderId=o.orderId and o.dataFlag=1','inner')
				   ->order('oc.complainId desc')
				   ->where($where)
				   ->paginate()->toArray();

		foreach($rs['data'] as $k=>$v){
			if($v['complainStatus']==0){
				$rs['data'][$k]['complainStatus'] = '等待处理';
			}elseif($v['complainStatus']==1){
				$rs['data'][$k]['complainStatus'] = '等待被投诉方回应';
			}elseif($v['complainStatus']==2 || $v['complainStatus']==3 ){
				$rs['data'][$k]['complainStatus'] = '等待仲裁';
			}elseif($v['complainStatus']==4){
				$rs['data'][$k]['complainStatus'] = '已仲裁';
			}
			$rs['data'][$k]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
		}
		if($rs !== false){
			return WSTReturn('',1,$rs);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/**
	 * 获取订单信息
	 */
	public function getOrderInfo($uId=0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$orderId = (int)Input('orderId');
        $where = [];
		//判断是否提交过投诉
		$rs = $this->alreadyComplain($orderId,$userId);
		$data = array('complainStatus'=>1);
		if($rs['complainId']==''){
			$where['o.orderId'] = $orderId;
			$where['o.userId'] = $userId;
			//获取订单信息
			$order = db('orders')->alias('o')
			 						 ->field('o.realTotalMoney,o.orderNo,o.orderId,o.createTime,o.deliverMoney,s.shopName,s.shopId')
									 ->join('__SHOPS__ s','o.shopId=s.shopId','left')
									 ->where($where)
									 ->find();
			if($order){
				//获取相关商品
			    $goods = $this->getOrderGoods($orderId);
				$order["goodsList"] = $goods;
			}
			$data['order'] = $order;
			$data['complainStatus'] = 0;
		}
		
        return $data;
	}
	// 判断是否已经投诉过
	public function alreadyComplain($orderId,$userId){
		return $this->field('complainId')->where("orderId=$orderId and complainTargetId=$userId")->find();
	}
	//获取相关商品
	public function getOrderGoods($orderId){
	  $rs = db('goods')->alias('g')
						->field('og.orderId, og.goodsId ,g.goodsSn, og.goodsName , og.goodsPrice shopPrice,og.goodsImg,og.goodsNum,og.goodsSpecNames,og.goodsCode')
						->join('__ORDER_GOODS__ og','g.goodsId = og.goodsId','inner')
						->where("og.orderId=$orderId")
						->select();
        foreach ($rs as $key => $v) {
            $shotGoodsSpecNames = [];
            if ($v['goodsSpecNames'] != "") {
                $v['goodsSpecNames'] = str_replace('：', ':', $v['goodsSpecNames']);
                $goodsSpecNames = explode('@@_@@', $v['goodsSpecNames']);

                foreach ($goodsSpecNames as $key2 => $spec) {
                    $obj = explode(":", $spec);
                    $shotGoodsSpecNames[] = $obj[1];
                }
            }
            $rs[$key]['goodsSpecNames'] = implode('，',$shotGoodsSpecNames);
        }
        return $rs;
	}

	/**
	 * 保存订单投诉信息
	 */
	public function saveComplain($uId=0){

		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$data['orderId'] = (int)input('orderId');
        //判断订单是否该用户的
		$order = db('orders')->field('orderId,shopId,orderNo')->where("userId=$userId")->find($data['orderId']);
		if(!$order){
			return WSTReturn('无效的订单信息',-1);
		}

		//判断是否提交过投诉
		$rs = $this->alreadyComplain($data['orderId'],$userId);

		if((int)$rs['complainId']>0){
			return WSTReturn("该订单已进行了投诉,请勿重复提交投诉信息",-1);
		}
		Db::startTrans();
		try{
			$data['complainTargetId'] = $userId;
			$data['respondTargetId'] = $order['shopId'];
			$data['complainStatus'] = 0;
			$data['complainType'] = (int)input('complainType');
			$data['complainTime'] = date('Y-m-d H:i:s');
			$data['complainAnnex'] = input('complainAnnex');
			$data['complainContent'] = input('complainContent');
			$validate = new Validate;
			if (!$validate->scene('add')->check($data)) {
				return WSTReturn($validate->getError());
			}else{
				$rs = $this->save($data);
			}
			if($rs !==false){
				WSTUseResource(0, $this->complainId, $data['complainAnnex']);
				//判断是否需要发送管理员短信
				$tpl = WSTMsgTemplates('PHONE_ADMIN_COMPLAINT_ORDER');
				if((int)WSTConf('CONF.smsOpen')==1 && (int)WSTConf('CONF.smsComplaintOrderTip')==1 &&  $tpl['tplContent']!='' && $tpl['status']=='1'){
					$params = ['tpl'=>$tpl,'params'=>['ORDER_NO'=>$order['orderNo']]];
					$staffs = Db::name('staffs')->where([['staffId','in',explode(',',WSTConf('CONF.complaintOrderTipUsers'))],['staffStatus','=',1],['dataFlag','=',1]])->field('staffPhone')->select();
					for($i=0;$i<count($staffs);$i++){
						if($staffs[$i]['staffPhone']=='')continue;
						$m = new LogSms();
						$rv = $m->sendAdminSMS(0,$staffs[$i]['staffPhone'],$params,'saveComplain','');
					}
				}
				//微信消息
				if((int)WSTConf('CONF.wxenabled')==1){
					//判断是否需要发送给管理员消息
		            if((int)WSTConf('CONF.wxComplaintOrderTip')==1){
		            	$remark = WSTDatas('ORDER_COMPLAINT',(int)input('complainType'));
		                $params = [];
						$params['ORDER_NO'] = $order['orderNo'];
					    $params['REMARK'] = "【".$remark['dataName']."】".WSTMSubstr(input('complainContent'),0,20,'utf-8','...');           
						$params['LOGIN_NAME'] = session('WST_USER.loginName');
			            WSTWxBatchMessage(['CODE'=>'WX_ADMIN_ORDER_COMPLAINT','userType'=>3,'userId'=>explode(',',WSTConf('CONF.complaintOrderTipUsers')),'params'=>$params]);
		            }
				}
				Db::commit();
				return WSTReturn('投诉已提交，请留意商城通知信息',1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
		}catch (\Exception $e) {
		    Db::rollback();
	    }
	    return WSTReturn('投诉失败',-1);
	}

	/**
	 * 获取投诉详情
	 */
	public function getComplainDetail($userType = 0,$sId = 0, $uId = 0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$shopId = ($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$id = (int)Input('id');
		if($userType==0){
			$where['complainTargetId']=$userId;
		}else{
			$where['needRespond'] = 1;
			$where['respondTargetId'] = $shopId;
		}

		//获取订单信息
		$where['complainId'] = $id;
		$rs = $this->alias('oc')
				   ->field('oc.*,o.realTotalMoney,o.orderNo,o.orderId,o.createTime,o.deliverMoney,s.shopName,s.shopId')
				   ->join('__ORDERS__ o','oc.orderId=o.orderId','inner')
				   ->join('__SHOPS__ s','o.shopId=s.shopId')
				   ->where($where)->find();
		if($rs){
			if($rs['complainAnnex']!='')$rs['complainAnnex'] = explode(',',$rs['complainAnnex']);
			if($rs['respondAnnex']!='')$rs['respondAnnex'] = explode(',',$rs['respondAnnex']);

			//获取相关商品
			$goods = $this->getOrderGoods($rs['orderId']);
			$rs["goodsList"] = $goods;
		}
        return $rs;
	}






	/************************************* 商家 *********************************************/
	/**
	  * 获取商家被投诉列表
	  */
	public function queryShopComplainByPage($sId=0){
		$shopId = ($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$orderNo = (int)Input('orderNo');
		$where = [];
		if($orderNo!=''){
			$where[] = ['o.orderNo','like',"%$orderNo%"];
		}
		$where[] = ['oc.needRespond','=',1];
		$where[] = ['o.dataFlag','=',1];
		$where[] = ['oc.respondTargetId','=',$shopId];
		$rs = $this->alias('oc')
				   ->field('oc.complainId,o.orderId,o.orderNo,u.userName,u.loginName,oc.complainContent,oc.complainStatus,oc.complainTime,o.orderCode')
				   ->join('__USERS__ u','oc.complainTargetId=u.userId','left')
				   ->join('__ORDERS__ o','oc.orderId=o.orderId')
				   ->where($where)
				   ->order('oc.complainId desc')
				   ->paginate(input('post.limit/d'))
				   ->toArray();
		foreach($rs['data'] as $k=>$v){
			if($v['complainStatus']==0){
				$rs['data'][$k]['complainStatus'] = '等待处理';
			}elseif($v['complainStatus']==1){
				$rs['data'][$k]['complainStatus'] = '等待被投诉方回应';
				$rs['data'][$k]['needReply'] = 1;
			}elseif($v['complainStatus']==2 || $v['complainStatus']==3 ){
				$rs['data'][$k]['complainStatus'] = '等待仲裁';
			}elseif($v['complainStatus']==4){
				$rs['data'][$k]['complainStatus'] = '已仲裁';
			}
			$rs['data'][$k]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
		}
		return WSTReturn('ok',1,$rs);
	}
	/**
	 * 保存订单应诉信息
	 */
	public function saveRespond($sId=0){
		$shopId = ($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$complainId = (int)Input('complainId');
		//判断是否提交过应诉和是否有效的投诉信息
		$complainRs = $this->field('needRespond,complainStatus,orderId,complainType,complainContent')->where("complainId=$complainId AND respondTargetId=$shopId")->find();
		trace('````['.$this->getLastSql(),'error');
		trace('````['.json_encode($complainRs),'error');
        if((int)$complainRs['needRespond']!=1){
			return WSTReturn('无效的投诉信息',-1);
		}
		if((int)$complainRs['complainStatus']!=1){
			return WSTReturn('该投诉订单已进行了应诉,请勿重复提交应诉信息',-1);
		}
		Db::startTrans();
		try{
			$data['complainStatus'] = 3;
			$data['respondTime'] = date('Y-m-d H:i:s');
			$data['respondAnnex'] = Input('respondAnnex');
			$data['respondContent'] = Input('respondContent');
			$validate = new Validate;
			if (!$validate->scene('respond')->check($data)) {
				return WSTReturn($validate->getError());
			}else{
				$rs = $this->where('complainId='.$complainId)->update($data);
			}
			if($rs !==false){
				WSTUseResource(0, $complainId, $data['respondAnnex']);
				$order = Db::name('orders')->alias('o')->join('__USERS__ u','u.userId=o.userId')
				           ->where('orderId',$complainRs['orderId'])
				           ->field('o.orderNo,u.loginName')->find();
				//判断是否需要发送管理员短信
				$tpl = WSTMsgTemplates('PHONE_ADMIN_COMPLAINT_ORDER');
				if((int)WSTConf('CONF.smsOpen')==1 && (int)WSTConf('CONF.smsComplaintOrderTip')==1 &&  $tpl['tplContent']!='' && $tpl['status']=='1'){
					$params = ['tpl'=>$tpl,'params'=>['ORDER_NO'=>$order['orderNo']]];
					$staffs = Db::name('staffs')->where([['staffId','in',explode(',',WSTConf('CONF.complaintOrderTipUsers'))],['staffStatus','=',1],['dataFlag','=',1]])->field('staffPhone')->select();
					for($i=0;$i<count($staffs);$i++){
						if($staffs[$i]['staffPhone']=='')continue;
						$m = new LogSms();
						$rv = $m->sendAdminSMS(0,$staffs[$i]['staffPhone'],$params,'saveRespond','');
					}
				}
				//微信消息
				if((int)WSTConf('CONF.wxenabled')==1){
					//判断是否需要发送给管理员消息
		            if((int)WSTConf('CONF.wxComplaintOrderTip')==1){
		            	$remark = WSTDatas('ORDER_COMPLAINT',$complainRs['complainType']);
		                $params = [];
						$params['ORDER_NO'] = $order['orderNo'];
					    $params['REMARK'] = "【".$remark['dataName']."】".WSTMSubstr($complainRs['complainContent'],0,20,'utf-8','...');           
						$params['LOGIN_NAME'] = $order['loginName'];
			            WSTWxBatchMessage(['CODE'=>'WX_ADMIN_ORDER_COMPLAINT','userType'=>3,'userId'=>explode(',',WSTConf('CONF.complaintOrderTipUsers')),'params'=>$params]);
		            }
				}
				Db::commit();
				return WSTReturn('应诉成功',1);
			}
		}catch (\Exception $e) {
		    Db::rollback();
	    }
	    return WSTReturn('投诉失败',-1);


	}
	
	
}
