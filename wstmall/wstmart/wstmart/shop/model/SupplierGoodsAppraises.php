<?php
namespace wstmart\shop\model;
use wstmart\shop\validate\SupplierGoodsAppraises as Validate;
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
 * 评价类
 */
use think\Db;
class SupplierGoodsAppraises extends Base{
	public function queryByPage($sId=0){
		$supplierId = ($sId==0)?(int)session('WST_USER.supplierId'):$sId;

		$where = [];
		$where[] = ['g.goodsStatus',"=",1];
		$where[] = ['g.dataFlag',"=",1];
		$where[] = ['g.isSale',"=",1];
		$c1Id = (int)input('cat1');
		$c2Id = (int)input('cat2');
		$goodsName = input('goodsName');
		if($goodsName != ''){
			$where[] = ['g.goodsName','like',"%$goodsName%"];
		}
		if($c2Id!=0 && $c1Id!=0){
			$where[] = ['g.supplierCatId2',"=",$c2Id];
		}else if($c1Id!=0){
			$where[] = ['g.supplierCatId1',"=",$c1Id];
		}
		$where[] = ['g.supplierId',"=",$supplierId];


		$data = Db::name("supplier_goods g")
					  ->field('u.userPhoto, u.loginName, g.goodsId,g.goodsImg,g.goodsName,ga.supplierReply,ga.id gaId,ga.replyTime,ga.goodsScore,ga.serviceScore,ga.timeScore,ga.content,ga.images,u.loginName')
					  ->join('supplier_goods_appraises ga','g.goodsId=ga.goodsId','inner')
					  ->join('__USERS__ u','u.userId=ga.userId','inner')
					  ->order('ga.id desc')
					  ->where($where)
					  ->paginate()->toArray();
		if(!empty($rs['data'])){
			foreach($rs['data'] as $k=>&$v){
				// 解义
				$v['content'] = htmlspecialchars_decode($v['content']);
				$v['supplierReply'] = htmlspecialchars_decode($v['supplierReply']);
				// 处理匿名
				if($v['userId']>0){
					// 替换中间两个字符
					$start = floor((strlen($v['loginName'])/2))-1;
					$v['loginName'] = mb_convert_encoding(substr_replace($v['loginName'],'**',$start,2),'UTF-8');
					$v['userPhoto'] = WSTUserPhoto($v['userPhoto'], true);
				}
			}
		}
        if($data !== false){
            return WSTReturn('',1,$data);
        }else{
            return WSTReturn($this->getError(),-1);
        }
	}
	/**
	* 用户评价
	*/
	public function userAppraise(){
		$shopId = (int)session('WST_USER.shopId');
		$where = [];
		$where['g.goodsStatus'] = 1;
		$where['g.dataFlag'] = 1;
		$where['g.isSale'] = 1;
		$where['o.shopId'] = $shopId;
		$data = Db::name("supplier_goods g")
					  ->field('g.goodsId,g.goodsImg,g.goodsName,ga.goodsScore,ga.serviceScore,ga.timeScore,ga.content,ga.images,ga.supplierReply,ga.replyTime,s.supplierName,u.userName,o.orderNo')
					  ->join('supplier_goods_appraises ga','g.goodsId=ga.goodsId','inner')
					  ->join('supplier_orders o','o.orderId=ga.orderId','inner')
					  ->join('__USERS__ u','u.userId=ga.userId','inner')
					  ->join('suppliers s','o.supplierId=s.supplierId','inner')
					  ->order('ga.id desc')
					  ->where($where)
					  ->paginate()->toArray();
		if($data !== false){
			return WSTReturn('',1,$data);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
 	/**
	* 添加评价
	*/
	public function add($uId=0){
		//检测订单是否有效
		$orderId = (int)input('orderId');
		$goodsId = (int)input('goodsId');
		$goodsSpecId = (int)input('goodsSpecId');
		$orderGoodsId = (int)input('orderGoodsId');

		// 没有传order_goods表的id
		if($orderGoodsId==0)return WSTReturn('数据出错,请联系管理员');
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		
		$goodsScore = (int)input('goodsScore');
		$timeScore = (int)input('timeScore');
		$serviceScore = (int)input('serviceScore');
		$content = input('content');
		if(isset($content)){
			if(!WSTCheckFilterWords($content,WSTConf("CONF.limitWords"))){
				return WSTReturn("点评内容包含非法字符");
			}
		}
		$orders = model('supplier_orders')->where(['orderId'=>$orderId,'shopId'=>$shopId,'dataFlag'=>1])->field('orderStatus,orderNo,isAppraise,supplierId')->find();
		if(empty($orders))return WSTReturn("无效的订单");
		if($orders['orderStatus']!=2)return WSTReturn("订单状态已改变，请刷新订单后再尝试!");
		//检测商品是否已评价
		$apCount = $this->where(['orderGoodsId'=>$orderGoodsId,'dataFlag'=>1])->count();
		if($apCount>0)return WSTReturn("该商品已评价!");
		Db::startTrans();
		try{	
			//增加订单评价
			$data = [];
			$data['userId'] = $userId;
			$data['goodsSpecId'] = $goodsSpecId;
			$data['goodsId'] = $goodsId;
			$data['supplierId'] = $orders['supplierId'];
			$data['orderId'] = $orderId;
			$data['goodsScore'] = $goodsScore;
			$data['serviceScore'] = $serviceScore;
			$data['timeScore']= $timeScore;
			$data['content'] = $content;
			$data['images'] = input('images');
			$data['createTime'] = date('Y-m-d H:i:s');
			$data['orderGoodsId'] = $orderGoodsId;
			if(empty(WSTConf('CONF.isAppraise'))){
                $data['isShow'] = 0;
			}
			$validate = new Validate;
			if (!$validate->scene('add')->check($data)) {
				return WSTReturn($validate->getError());
			}else{
				$rs = $this->allowField(true)->save($data);
			}
			if($rs !==false){
				$lastId = $this->id;
				WSTUseResource(0, $this->id, $data['images']);
				//增加商品评分

			    if(!empty(WSTConf('CONF.isAppraise'))){
				$prefix = config('database.prefix');
				$updateSql = "update ".$prefix."supplier_goods_scores set 
				             totalScore=totalScore+".(int)($goodsScore+$serviceScore+$timeScore).",
				             goodsScore=goodsScore+".(int)$goodsScore.",
				             serviceScore=serviceScore+".(int)$serviceScore.",
				             timeScore=timeScore+".(int)$timeScore.",
				             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
				             where goodsId=".$goodsId;
				Db::execute($updateSql);
				//增加商品评价数
				Db::name('supplier_goods')->where('goodsId',$goodsId)->setInc('appraiseNum');
				$tScore['totalScore'] = 0;
				$tScore['serviceScore'] = 0;
				$tScore['goodsScore'] = 0;
				$tScore['timeScore'] = 0;
				$where2 = [];
				$where2['supplierId'] = $orders['supplierId'];
				$where2['totalUsers'] = 0;
				Db::name('supplier_scores')->where($where2)->update($tScore);
				//增加店铺评分
				$updateSql = "update ".$prefix."supplier_scores set 
				             totalScore=totalScore+".(int)($goodsScore+$serviceScore+$timeScore).",
				             goodsScore=goodsScore+".(int)$goodsScore.",
				             serviceScore=serviceScore+".(int)$serviceScore.",
				             timeScore=timeScore+".(int)$timeScore.",
				             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
				             where supplierId=".$orders['supplierId'];
				Db::execute($updateSql);
			    }
				// 查询该订单是否已经完成评价,修改orders表中的isAppraise
				$ogRs = Db::name('supplier_order_goods')->alias('og')
				   ->join('supplier_goods_appraises ga','og.orderId=ga.orderId and og.goodsId=ga.goodsId and og.goodsSpecId=ga.goodsSpecId','left')
				   ->where('og.orderId',$orderId)->field('og.id,ga.id gid')->select();
				$isFinish = true;
				foreach ($ogRs as $key => $v){
					if($v['id']>0 && $v['gid']==''){
						$isFinish = false;
						break;
					}
				}
				//订单商品全部评价完则修改订单状态
				if($isFinish){
					//修改订单评价状态
					model('supplierOrders')->where('orderId',$orderId)->update(['isAppraise'=>1]);
				}
				//发送一条商家信息
				$tpl = WSTMsgTemplates('ORDER_APPRAISES');
				$orderGoods = Db::name('supplier_order_goods')->where(['orderId'=>$orderId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId])->field('goodsName')->find();
	           
	            $supplierId = $orders['supplierId'];
	            if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                $find = ['${ORDER_NO}','${GOODS}'];
	                $replace = [$orders['orderNo'],$orderGoods['goodsName']];
	                
	                $msg = array();
		            $msg["supplierId"] = $supplierId;
		            $msg["tplCode"] = $tpl["tplCode"];
		            $msg["msgType"] = 1;
		            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
		            $msg["msgJson"] = ['from'=>6,'dataId'=>$lastId];
		            model("common/SupplierMessageQueues")->add($msg);
	            }
	           
				Db::commit();
				return WSTReturn('评价成功',1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
		}catch (\Exception $e) {
		    Db::rollback();
	        return WSTReturn('评价失败',-1);
	    }

	}
    
}
