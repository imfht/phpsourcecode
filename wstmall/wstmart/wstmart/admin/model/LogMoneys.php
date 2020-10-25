<?php
namespace wstmart\admin\model;
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
 * 资金流水日志业务处理
 */
class LogMoneys extends Base{
	/**
	 * 用户资金列表 
	 */
	public function pageQueryByUser(){
		$key = input('key');
		// 排序
		$sort = input('sort');
		$order = [];
		if($sort!=''){
			$sortArr = explode('.',$sort);
			$order[$sortArr[0]] = $sortArr[1];
		}
		$where[] = ['dataFlag','=',1];
        $where[] = ['loginName','like','%'.$key.'%'];
		return model('users')->where($where)->field('loginName,userId,userName,userMoney,rechargeMoney,lockMoney')->order($order)->paginate(input('limit/d'));
	}
	/**
	 * 商家资金列表 
	 */
	public function pageQueryByShop(){
		$key = input('key');
		$where[] = ['u.dataFlag','=',1];
		$where[] = ['s.dataFlag','=',1];
        $where[] = ['loginName','like','%'.$key.'%'];
		return Db::name('shops')->alias('s')->join('__USERS__ u','s.userId=u.userId','inner')->where($where)->field('loginName,shopId,shopName,shopMoney,s.rechargeMoney,s.lockMoney')->paginate(input('limit/d'));
	}
	/**
	 * 供货商资金列表 
	 */
	public function pageQueryBySupplier(){
		$key = input('key');
		$where[] = ['u.dataFlag','=',1];
		$where[] = ['s.dataFlag','=',1];
        $where[] = ['loginName','like','%'.$key.'%'];
		return Db::name('suppliers')->alias('s')->join('__USERS__ u','s.userId=u.userId','inner')->where($where)->field('loginName,supplierId,supplierName,supplierMoney,s.rechargeMoney,s.lockMoney')->paginate(input('limit/d'));
	}
	
	/**
	 * 获取用户信息
	 */
	public function getUserInfoByType(){
		$type = (int)input('type',0);
		$id = (int)input('id');
		$data = [];
        if($type==1){
            $data = Db::name('shops')->alias('s')->join('__USERS__ u','s.userId=u.userId','inner')->where('shopId',$id)->field('shopId as userId,shopName as userName,loginName,1 as userType')->find();
        }else if($type==3){
            $data = Db::name('suppliers')->alias('s')->join('__USERS__ u','s.userId=u.userId','inner')->where('supplierId',$id)->field('supplierId as userId,supplierName as userName,loginName,3 as userType')->find();
        }else{
            $data = model('users')->where('userId',$id)->field('loginName,userId,userName,0 as userType')->find();
        }
        return $data;
	}

    /**
	 * 分页
	 */
	public function pageQuery($moneySrc = -100000){
		$key = input('key');
		$userType = input('type');
		$userId = input('id');
		$startDate = input('startDate');
		$endDate = input('endDate');
		$where = [];
		if($moneySrc!=-100000)$where[] = ['dataSrc','=',$moneySrc];
		if($startDate!='')$where[] = ['l.createTime','>=',$startDate." 00:00:00"];
		if($endDate!='')$where[] = [' l.createTime','<=',$endDate." 23:59:59"];
		if($userType!='')$where[] = ['l.targetType','=',$userType];
		if($userId!='')$where[] = ['l.targetId','=',$userId];
		if($key!='')$where[] = ['u.loginName','like','%'.$key.'%'];
		if($userType==3){
			$page = $this->alias('l')
					 ->join('__USERS__ u','l.targetId=u.userId and l.targetType=0 ','left')
					 ->join('__SUPPLIERS__ s','l.targetId=s.supplierId and l.targetType=3 ','left')
					 ->where($where)->field('l.*,u.loginName,s.supplierName shopName')->order('l.id', 'desc')
					 ->paginate(input('l.limit/d'))->toArray();
		}else{
			$page = $this->alias('l')
					 ->join('__USERS__ u','l.targetId=u.userId and l.targetType=0 ','left')
					 ->join('__SHOPS__ s','l.targetId=s.shopId and l.targetType=1 ','left')
					 ->where($where)->field('l.*,u.loginName,s.shopName')->order('l.id', 'desc')
					 ->paginate(input('l.limit/d'))->toArray();
		}
		
		if(count($page['data'])>0){
			foreach ($page['data'] as $key => $v) {
				$page['data'][$key]['loginName'] = ($v['targetType']==1)?$v['shopName']:$v['loginName'];
				$page['data'][$key]['dataSrc'] = WSTLangMoneySrc($v['dataSrc']);
			}
		}
		return $page;
	}

	/**
     * 新增记录
     */
    public function add($log){
          $log['createTime'] = date('Y-m-d H:i:s');
          $this->create($log);
          if($log['moneyType']==1){
              if($log['targetType']==1){
	      	      Db::name('shops')->where(["shopId"=>$log['targetId']])->setInc('shopMoney',$log['money']);
		      }else if($log['targetType']==3){
	      	      Db::name('suppliers')->where(["supplierId"=>$log['targetId']])->setInc('supplierMoney',$log['money']);
		      }else{
		      	  Db::name('users')->where(["userId"=>$log['targetId']])->setInc('userMoney',$log['money']);
		      }
          }else{
              if($log['targetType']==1){
	      	      Db::name('shops')->where(["shopId"=>$log['targetId']])->setDec('shopMoney',$log['money']);
		      }else if($log['targetType']==3){
	      	      Db::name('suppliers')->where(["supplierId"=>$log['targetId']])->setDec('supplierMoney',$log['money']);
		      }else{
		      	  Db::name('users')->where(["userId"=>$log['targetId']])->setDec('userMoney',$log['money']);
		      }
          }
    }

    /**
     * 新增记录
     */
    public function addByAdmin(){
    	$data = [];
    	$data['targetType'] = (int)input('targetType');
    	$data['targetId'] = (int)input('targetId');
    	$data['money'] = (float)input('money');
        $data['dataSrc'] = 10001;
        $data['dataId'] = 0;
        $data['moneyType'] = (int)input('moneyType');
        $data['remark'] = input('remark');
        $data['dataFlag'] = 1;
        $data['createTime'] = date('Y-m-d H:i:s');
        //判断用户身份
        if($data['targetType']==1){
        	$rs = Db::name('shops')->where(["shopId"=>$data['targetId'],'dataFlag'=>1])->find();
        }else if($data['targetType']==3){
        	$rs = Db::name('suppliers')->where(["supplierId"=>$data['targetId'],'dataFlag'=>1])->find();
        }else{
            $rs = Db::name('users')->where(['userId'=>$data['targetId'],'dataFlag'=>1])->find();
        }
        if(empty($rs))return WSTReturn('无效的会员');
        if(!in_array($data['moneyType'],[0,1]))return WSTReturn('无效的资金类型');
        if($data['money']<0.01)return WSTReturn('变动资金必须大于0.01');
        Db::startTrans();
		try{
			$result = $this->insert($data);
			if(false !== $result){
		        if($data['moneyType']==1){
		            if($data['targetType']==1){
			      	      Db::name('shops')->where(["shopId"=>$data['targetId']])->setInc('shopMoney',$data['money']);
				    }else if($data['targetType']==3){
			      	      Db::name('suppliers')->where(["supplierId"=>$data['targetId']])->setInc('supplierMoney',$data['money']);
				    }else{
				      	  Db::name('users')->where(["userId"=>$data['targetId']])->setInc('userMoney',$data['money']);
				    }
		          }else{
		            if($data['targetType']==1){
			      	      Db::name('shops')->where(["shopId"=>$data['targetId']])->setDec('shopMoney',$data['money']);
				    }else if($data['targetType']==3){
			      	      Db::name('suppliers')->where(["supplierId"=>$data['targetId']])->setDec('supplierMoney',$data['money']);
				    }else{
				      	  Db::name('users')->where(["userId"=>$data['targetId']])->setDec('userMoney',$data['money']);
				    }
		        }
		    }
            Db::commit();
			return WSTReturn('操作成功',1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1);
		}
    }

    public function phaseSummary(){
    	$startTime = '';
    	$entTime = '';
    	$type = (int)input("type");
    	if($type==1){//今日
    		$today = date("Y-m-d");
    		$startTime = $today." 00:00:00";
    		$entTime = $today." 23:59:59";
    	}else if($type==2){//七日
    		$startTime = date("Y-m-d H:i:s",strtotime("-7 day"));
    		$entTime = date("Y-m-d H:i:s");
    	}else if($type==3){//本月
    		$startTime = date("Y-m-01 00:00:00");
    		$entTime = date("Y-m-t 23:59:59");
    	}
    	$isOpenSupplier = (int)WSTConf('CONF.isOpenSupplier');
    	$data = [];
    	if($type==0){
    		$where = [];
	    	$where[] = ['dataFlag','=', 1];
	    	$rs = Db::name("users")->where($where)->field("sum(userMoney) userMoney,sum(lockMoney) lockMoney")->find();
	    	$data['totalUserMoney'] = WSTBCMoney($rs['userMoney'],$rs['lockMoney']);

	    	$where = [];
	    	$where[] = ['dataFlag','=', 1];
	    	$rs = Db::name("shops")->where($where)->field("sum(shopMoney) shopMoney,sum(lockMoney) lockMoney")->find();
	    	$data['totalShopMoney'] = WSTBCMoney($rs['shopMoney'],$rs['lockMoney']);
	    	if($isOpenSupplier==1){
	    		$where = [];
		    	$where[] = ['dataFlag','=', 1];
		    	$rs = Db::name("suppliers")->where($where)->field("sum(supplierMoney) supplierMoney,sum(lockMoney) lockMoney")->find();
		    	$data['totalSupplierMoney'] = WSTBCMoney($rs['supplierMoney'],$rs['lockMoney']);
	    	}
	    	$where = [];
	    	$where[] = ['dataFlag','=', 1];
	    	$totalScore = Db::name("users")->where($where)->sum("userScore");
	    	$data['totalScore'] = $totalScore;
    	}else{
    		//充值金额
	    	$where = [];
	    	$where[] = ['moneyType','=',1];
	    	if($type>0 && $type<=3)$where[] = ['createTime','between',[$startTime,$entTime]];
	    	$where[] = ['payType','in',['alipays','weixinpays','app_weixinpays']];
	    	$data['rechangeMoney'] = Db::name("log_moneys")->where($where)->sum("money");
	    	//赠送金额
	    	$where = [];
	    	$where[] = ['moneyType','=',1];
	    	if($type>0 && $type<=3)$where[] = ['createTime','between',[$startTime,$entTime]];
	    	$where[] = ['giveMoney','>',0];
	    	$data['giveMoney'] = Db::name("log_moneys")->where($where)->sum("giveMoney");
	    	//年费金额
	    	$where = [];
	        $where[] = ['dataFlag','=', 1];
	        $where[] = ['isRefund','=', 0];
	        if($type>0 && $type<=3)$where[] = ['createTime','between',[$startTime,$entTime]];
	        $renewMoney =  Db::name("shop_fees")->where($where)->sum("money");
	        if($isOpenSupplier==1){
	        	$supplierRenewMoney =  Db::name("supplier_fees")->where($where)->sum("money");
	        	$renewMoney = WSTBCMoney($renewMoney,$supplierRenewMoney);
	        }
	        $data['renewMoney'] = $renewMoney;
	    	//提现金额
	    	$where = [];
	        $where[] = ['cashSatus','=', 1];
	        if($type>0 && $type<=3)$where[] = ['createTime','between',[$startTime,$entTime]];
	        $data['cashDraw'] =  Db::name("cash_draws")->where($where)->sum("money");
	    	//退款金额
	    	$totalRefundMoney = 0;
	    	$where = [];
	        $where[] = ['refundStatus','=', 2];
	        if($type>0 && $type<=3)$where[] = ['refundTime','between',[$startTime,$entTime]];
	        $shopRefundMoney =  Db::name("order_refunds")->where($where)->sum("backMoney");
	        if($isOpenSupplier==1){
		        $where = [];
		        $where[] = ['refundStatus','=', 2];
		        if($type>0 && $type<=3)$where[] = ['refundTime','between',[$startTime,$entTime]];
		        $supplierRefundMoney =  Db::name("supplier_order_refunds")->where($where)->sum("backMoney");
		        $totalRefundMoney = WSTBCMoney($shopRefundMoney,$supplierRefundMoney);
		    }else{
		    	$totalRefundMoney = $shopRefundMoney;
		    }
	        $data['refundMoney'] =  $totalRefundMoney;
	    	//赠送积分
	    	$where = [];
	        $where[] = ['scoreType','=', 1];
	        if($type>0 && $type<=3)$where[] = ['createTime','between',[$startTime,$entTime]];
	        $data['giveScore'] =  Db::name("user_scores")->where($where)->sum("score");
	    	//积分兑换
	    	$where = [];
	        $where[] = ['scoreType','=', 0];
	        if($type>0 && $type<=3)$where[] = ['createTime','between',[$startTime,$entTime]];
	        $data['exchangeScore'] =  Db::name("user_scores")->where($where)->sum("score");
	        //订单佣金
	    	$where = [];
	        $where[] = ['settlementStatus','=', 1];
	        if($type>0 && $type<=3)$where[] = ['createTime','between',[$startTime,$entTime]];
	        $sqla = Db::name('settlements')->field('commissionFee')->where($where)->buildSql();
            if($isOpenSupplier==1){
                $sqla = Db::name('supplier_settlements')->field('commissionFee')->where($where)->unionAll($sqla)->buildSql();
            }
	        $data['commission'] =  Db::table($sqla." st")->sum("commissionFee");;
    	}
    	

        return $data;
    }

    /**
	 * 充值分页【系统入账】
	 */
	public function rechangePageQuery($moneySrc = -100000){
		$key = input('key');
		$userType = input('type');
		$startDate = input('startDate');
		$endDate = input('endDate');
		$where = [];
		$where[] = ['moneyType','=',1];
		$where[] = ['payType','in',['alipays','weixinpays','app_weixinpays']];
		if($startDate!='')$where[] = ['l.createTime','>=',$startDate." 00:00:00"];
		if($endDate!='')$where[] = [' l.createTime','<=',$endDate." 23:59:59"];
		if($userType!='')$where[] = ['l.targetType','=',$userType];
		if($key!='')$where[] = ['u.loginName','like','%'.$key.'%'];
		$isOpenSupplier = WSTConf('CONF.isOpenSupplier');
		$dbo = $this->alias('l')
				 ->join('__USERS__ u','l.targetId=u.userId and l.targetType=0 ','left')
				 ->join('__SHOPS__ s','l.targetId=s.shopId and l.targetType=1 ','left');
		$fields = 'l.*,u.loginName,s.shopName';
		if($isOpenSupplier==1){
			$dbo = $dbo->join('__SUPPLIERS__ su','l.targetId=su.supplierId and l.targetType=3 ','left');
			$fields = 'l.*,u.loginName,s.shopName,su.supplierName';
		}
		$totalRechangeMoney = $dbo->where($where)->sum('l.money');
		$page = $dbo->where($where)->field($fields)->order('l.id', 'desc')
				 ->paginate(input('l.limit/d'))->toArray();
		
		
		if(count($page['data'])>0){
			foreach ($page['data'] as $key => $v) {
				if($v['targetType']==1){
					$page['data'][$key]['loginName'] = $v['shopName'];
				}else if($v['targetType']==3){
					$page['data'][$key]['loginName'] = $v['supplierName'];
				}else{
					$page['data'][$key]['loginName'] = $v['loginName'];
				}
				$page['data'][$key]['totalRechangeMoney'] = $totalRechangeMoney;
				$page['data'][$key]['dataSrc'] = WSTLangMoneySrc($v['dataSrc']);
			}
		}
		return $page;
	}


}
