<?php
namespace wstmart\admin\model;
use think\Db;
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
 * 定时业务处理
 */
class CronJobs extends Base{
	/**
	 * 管理员登录触发动作
	 */
	public function autoByAdmin(){
		$this->autoCancelNoPay();
		$this->autoReceive();
		$this->autoAppraise();
		$this->autoSendMsg();
		//$this->autoFileXml();
		$this->autoShopSettlement();
        $this->autoChangeShopStatus();
        $this->autoChangeSupplierStatus();
	}
	/**
	 * 取消未支付订单
	 */
	public function autoCancelNoPay(){
		$autoCancelNoPayDays = (int)WSTConf('CONF.autoCancelNoPayDays');
	 	$autoCancelNoPayDays = ($autoCancelNoPayDays>0)?$autoCancelNoPayDays:6;
	 	$lastDay = date("Y-m-d H:i:s",strtotime("-".$autoCancelNoPayDays." hours"));
	 	$orders = Db::name('orders')->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')->where([['o.createTime','<',$lastDay],['o.orderStatus','=',-2],['o.dataFlag','=',1],['o.payType','=',1],['o.isPay','=',0]])->field("o.orderId,o.orderNo,o.userId,o.shopId,o.useScore,s.userId shopUserId,orderCode")->select();
	 	if(!empty($orders)){
	 		$prefix = config('database.prefix');
	 		$orderIds = [];
	 		foreach ($orders as $okey => $order){
	 			$orderIds[] = $order['orderId'];
	 		}
	 		Db::startTrans();
		    try{
		    	//提前锁定订单
		    	Db::name('orders')->where([['orderId','in',$orderIds]])->update(['orderStatus'=>-1,'realTotalMoney'=>0]);
                foreach ($orders as $okey => $order){
                	$shopId = $order['shopId'];
                	$goods = Db::name('order_goods')->alias('og')->join('__GOODS__ g','og.goodsId=g.goodsId','inner')
					           ->where('orderId',$order['orderId'])->field('og.*,g.isSpec')->select();
					foreach ($goods as $k => $v){
						//处理虚拟产品
					    if($v['goodsType']==1){
                            $extraJson = json_decode($v['extraJson'],true);
                            foreach ($extraJson as  $ecard) {
                            	Db::name('goods_virtuals')->where('id',$ecard['cardId'])->update(['orderId'=>0,'orderNo'=>'','isUse'=>0]);
                            }
                            $counts = Db::name('goods_virtuals')->where(['dataFlag'=>1,'goodsId'=>$v['goodsId'],'isUse'=>0])->count();
                            Db::name('goods')->where('goodsId',$v['goodsId'])->update(['goodsStock'=>$counts]);
					    }else{
					    	//只有正常下单的才会修改库存的，其他的任何插件都不会修改库存
					    	if($order['orderCode'] == 'order'){
								//修改库存
								if($v['isSpec']>0){
							        Db::name('goods_specs')->where('id',$v['goodsSpecId'])->setInc('specStock',$v['goodsNum']);
								}
								Db::name('goods')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
					        }
					    }
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $order['orderId'];
					$logOrder['orderStatus'] = -1;
					$logOrder['logContent'] = "订单长时间未支付，系统自动取消订单";
					$logOrder['logUserId'] = $order['userId'];
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
                    //发送消息
	                $tpl = WSTMsgTemplates('ORDER_USER_PAY_TIMEOUT');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                    $find = ['${ORDER_NO}'];
	                    $replace = [$order['orderNo']];
	                    //发送一条用户信息
					    WSTSendMsg($order['userId'],str_replace($find,$replace,$tpl['tplContent']),['from'=>1,'dataId'=>$order['orderId']]);
	                }
                    $tpl = WSTMsgTemplates('ORDER_SHOP_PAY_TIMEOUT');
	                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
	                    $find = ['${ORDER_NO}'];
	                    $replace = [$order['orderNo']];
	                    //发送一条商家信息
					    
	                	$msg = array();
			            $msg["shopId"] = $shopId;
			            $msg["tplCode"] = $tpl["tplCode"];
			            $msg["msgType"] = 1;
			            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']) ;
			            $msg["msgJson"] = ['from'=>1,'dataId'=>$order['orderId']];
			            model("common/MessageQueues")->add($msg);
	                }
	                //微信消息
		            if((int)WSTConf('CONF.wxenabled')==1){
		            	$params = [];
		                $params['ORDER_NO'] = $order['orderNo'];            
	                    WSTWxMessage(['CODE'=>'WX_ORDER_USER_PAY_TIMEOUT','userId'=>$order['userId'],'URL'=>Url('wechat/orders/index','',true,true),'params'=>$params]);
	                   
		            	$msg = array();
		            	$tplCode = "WX_ORDER_SHOP_PAY_TIMEOUT";
						$msg["shopId"] = $shopId;
			            $msg["tplCode"] = $tplCode;
			            $msg["msgType"] = 4;
			            $msg["paramJson"] = ['CODE'=>$tplCode,'URL'=>Url('wechat/orders/sellerorder',['type'=>'abnormal'],true,true),'params'=>$params] ;
			            $msg["msgJson"] = "";
			            model("common/MessageQueues")->add($msg);
		            }
                }

		        Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	 	}
	 	return WSTReturn('操作成功',1);
	}
    /**
	 * 自动好评
	 */
	public function autoAppraise(){
        $autoAppraiseDays = (int)WSTConf('CONF.autoAppraiseDays');
	 	$autoAppraiseDays = ($autoAppraiseDays>0)?$autoAppraiseDays:7;//避免有些客户没有设置值
	 	$lastDay = date("Y-m-d 00:00:00",strtotime("-".$autoAppraiseDays." days"));
	 	$rs = model('orders')->where([['receiveTime','<',$lastDay],['orderStatus','=',2],['dataFlag','=',1],['isAppraise','=',0]])->field("orderId,userId,orderScore,shopId,orderNo")->select();
	 	if(!empty($rs)){
	 		$prefix = config('database.prefix');
	 		$orderIds = [];
	 		foreach ($rs as $okey => $order){
	 			$orderIds[] = $order->orderId;
	 		}
	 		Db::startTrans();
		    try{
		    	//提前锁定订单
		    	Db::name('orders')->where([['orderId','in',$orderIds]])->update(['isAppraise'=>1]);
		    	foreach ($rs as $okey => $order){;
		    	    //获取订单相关的商品
		    	    $ordergoods = Db::name('order_goods')->where('orderId',$order->orderId)->field('id,goodsId,orderId,goodsSpecId')->select();
		    	    foreach($ordergoods as $goods){
						$apCount = Db::name('goods_appraises')->where(['orderGoodsId'=>$goods['id'],'dataFlag'=>1])->count();
		                if($apCount>0)continue;
		    	    	//增加订单评价
						$data = [];
						$data['userId'] = $order->userId;
						$data['goodsSpecId'] = (int)$goods['goodsSpecId'];
						$data['orderGoodsId'] = $goods['id'];
						$data['goodsId'] = $goods['goodsId'];
						$data['shopId'] = $order->shopId;
						$data['orderId'] = $goods['orderId'];
						$data['goodsScore'] = 5;
						$data['serviceScore'] = 5;
						$data['timeScore']= 5;
						$data['content'] = '自动好评';
						$data['createTime'] = date('Y-m-d H:i:s');
						Db::name('goods_appraises')->insert($data);
		    	    }
					//增加商品评分
					$updateSql = "update ".$prefix."goods_scores set 
						             totalScore=totalScore+15,
					             goodsScore=goodsScore+5,
					             serviceScore=serviceScore+5,
					             timeScore=timeScore+5,
					             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
					             where goodsId=".$goods['goodsId'];
					Db::execute($updateSql);
					//增加商品评价数
					Db::name('goods')->where('goodsId',$goods['goodsId'])->setInc('appraiseNum');
					//增加店铺评分
					$updateSql = "update ".$prefix."shop_scores set 
					             totalScore=totalScore+15,
					             goodsScore=goodsScore+5,
					             serviceScore=serviceScore+5,
					             timeScore=timeScore+5,
					             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
					             where shopId=".$order->shopId;
					Db::execute($updateSql);
					// 查询该订单是否已经完成评价,修改orders表中的isAppraise
					$ogRs = Db::name('order_goods')->alias('og')
					   			  ->join('__GOODS_APPRAISES__ ga','og.orderId=ga.orderId and og.goodsId=ga.goodsId and og.goodsSpecId=ga.goodsSpecId','left')
					              ->where('og.orderId',$order->orderId)->field('og.id,ga.id gid')->select();
					$isFinish = true;
					foreach ($ogRs as $vkey => $v){
						if($v['id']>0 && $v['gid']==''){
								$isFinish = false;
								break;
						}
					}
					//订单商品全部评价完则修改订单状态
					if($isFinish){
						if(WSTConf("CONF.isAppraisesScore")==1){
							$appraisesScore = (int)WSTConf('CONF.appraisesScore');
							if($appraisesScore>0){
								//给用户增加积分
								$score = [];
								$score['userId'] = $order->userId;
								$score['score'] = $appraisesScore;
								$score['dataSrc'] = 1;
								$score['dataId'] = $order->orderId;
								$score['dataRemarks'] = "评价订单【".$order->orderNo."】获得积分".$appraisesScore."个";
								$score['scoreType'] = 1;
								$score['createTime'] = date('Y-m-d H:i:s');
								Db::name('user_scores')->insert($score);
								// 增加用户积分
							    model('Users')->where("userId=".$order->userId)->update([
							    	'userScore'=>Db::Raw('userScore+'.$appraisesScore),
							    	'userTotalScore'=>Db::Raw('userTotalScore+'.$appraisesScore)
							    ]);
							}
						}
						model('orders')->where('orderId',$orderId)->update(['isAppraise'=>1]);
					}
				}
		        Db::commit();
				return WSTReturn('操作成功',1);
	 		}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
	 	}
	 	return WSTReturn('操作成功',1);
	}
	/**
	 * 自动确认收货
	 */
	public function autoReceive(){
	 	$autoReceiveDays = (int)WSTConf('CONF.autoReceiveDays');
	 	$autoReceiveDays = ($autoReceiveDays>0)?$autoReceiveDays:10;//避免有些客户没有设置值
	 	$lastDay = date("Y-m-d 00:00:00",strtotime("-".$autoReceiveDays." days"));
	 	$rs = model('orders')->where([['deliveryTime','<',$lastDay],['orderStatus','=',1],['dataFlag','=',1]])->field("orderId,orderNo,shopId,userId,shopId,orderScore,commissionFee")->select();
	 	if(!empty($rs)){
	 		$prefix = config('database.prefix');
	 		Db::startTrans();
		    try{
		    	//结束订单状态
	 			$limitDay = (int)WSTConf('CONF.afterSaleServiceDays');
				// 售后结束时间
				$afterSaleEndTime = date('Y-m-d H:i:s', strtotime("+{$limitDay} day"));
		 		foreach ($rs as $key => $order){
		 			$order->afterSaleEndTime = $afterSaleEndTime;
		 			$order->receiveTime = date('Y-m-d 00:00:00');
		 			$order->orderStatus = 2;
		 			$rsStatus = $order->save();
		 			if(false !== $rsStatus){

		 				//修改商品成交量
						$goodss = Db::name('order_goods')->where('orderId',$order['orderId'])->field('goodsId,goodsNum,goodsSpecId')->select();
						foreach($goodss as $key =>$v){
							Db::name('goods')->where('goodsId',$v['goodsId'])->update([
	                            'saleNum'=>Db::raw('saleNum+'.$v['goodsNum'])
	                        ]);
							if($v['goodsSpecId']>0){
								Db::name('goods_specs')->where('id',$v['goodsSpecId'])->update([
		                            'saleNum'=>Db::raw('saleNum+'.$v['goodsNum'])
		                        ]);
							}
						}
					
		 				hook('afterUserReceive',['orderId'=>$order->orderId]);

				    	//修改商家未计算订单数
						$torder = Db::name('orders')->where("orderId",$order->orderId)->field("orderId,commissionFee")->find();
						Db::name('shops')->where('shopId',$order->shopId)->update([
							'noSettledOrderNum'=>Db::raw('noSettledOrderNum+1'),
							'noSettledOrderFee'=>Db::raw('noSettledOrderFee-'.$torder['commissionFee'])
						]);
					    
		 				
	                    //新增订单日志
						$logOrder = [];
						$logOrder['orderId'] = $order->orderId;
						$logOrder['orderStatus'] = 2;
						$logOrder['logContent'] = "系统自动确认收货";
						$logOrder['logUserId'] = $order->userId;
						$logOrder['logType'] = 0;
						$logOrder['logTime'] = date('Y-m-d H:i:s');
						Db::name('log_orders')->insert($logOrder);

						//发送一条商家信息
						$tpl = WSTMsgTemplates('ORDER_ATUO_RECEIVE');
		                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
		                    $find = ['${ORDER_NO}'];
		                    $replace = [$order['orderNo']];
		                	$msg = array();
				            $msg["shopId"] = $order['shopId'];
				            $msg["tplCode"] = $tpl["tplCode"];
				            $msg["msgType"] = 1;
				            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']) ;
				            $msg["msgJson"] = ['from'=>1,'dataId'=>$order->orderId];
				            model("common/MessageQueues")->add($msg);
		                }
						//给用户增加积分
						if(WSTConf("CONF.isOrderScore")==1){
							$score = [];
							$score['userId'] = $order->userId;
							$score['score'] = $order->orderScore;
							$score['dataSrc'] = 1;
							$score['dataId'] = $order->orderId;
							$score['dataRemarks'] = "交易订单【".$order->orderNo."】获得积分".$order->orderScore."个";
							$score['scoreType'] = 1;
							$score['createTime'] = date('Y-m-d H:i:s');
							model('UserScores')->save($score);
							// 增加用户积分
						    model('Users')->where("userId=".$order->userId)->setInc('userScore',$order->orderScore);
						    // 用户总积分
						    model('Users')->where("userId=".$order->userId)->setInc('userTotalScore',$order->orderScore);
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
	 	return WSTReturn('操作成功',1);
	}
	/**
	 * 发送队列消息
	 */
	public function autoSendMsg(){
		$now = date("Y-m-d H:i:s");
		$list = Db::name("message_queues")->where(["sendStatus"=>0])->order('id','asc')->limit(500)->select();
		$firstTime = date('Y-m-d',strtotime("-2 day"))." 23:59:59";
		Db::startTrans();
		try{
		    foreach ($list as $key => $msg) {
				$msgParams = json_decode($msg["paramJson"],true);
				if($msg["msgType"]==2){//短信消息
					model("admin/LogSms")->sendSMS(0,$msgParams['userId'],$msgParams['userPhone'],$msgParams['params'],$msgParams['method']);
					//门店暂无
				}else if($msg["msgType"]==3){//邮件消息
					//门店暂无
				}else if($msg["msgType"]==4){//微信消息
					WSTWxMessage($msgParams);
				}
				Db::name("message_queues")->where(["id"=>$msg["id"]])->update(["sendStatus"=>1,"sendTime"=>$now]);
		    }
		    //删除昨天的已发送消息
		    Db::name("message_queues")->where([["sendStatus",'=',1],['sendTime','<=',$firstTime]])->delete();
		    Db::commit();
		}catch (\Exception $e) {
	        Db::rollback();
	        return WSTReturn('操作失败',-1);
	    }
		return WSTReturn('操作成功',1);
	}
	/**
	 * 生成sitemap.xml
	 */
	function autoFileXml(){
		header("Content-type: application/xml");
		$routePc = Env::get('root_path')."sitemap/sitemap_pc.xml";
		$routeMobile = Env::get('root_path')."sitemap/sitemap_mobile.xml";
		$dataPc = [];
		$dataMobile = [];
		/* 更新导航 */
		$navs = Db::name("navs")->where(["isShow"=>1])->field('navUrl,navTitle')->limit(500)->select();
		if (!empty($navs)){
			foreach ($navs as $key =>$v){
				$dataPc[] = [
						'url'       => WSTDomain().'/'.$v['navUrl'],
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '1',
				];
			}
		}
		$dataMobile[] = ['url'=> WSTDomain().'/mobile/index/index.html','lastmod'=> date("Y-m-d"),'changefreq'=>'daily','priority'  => '1'];
		/* 更新店铺 */
		$shops = Db::name("shops")->where(["dataFlag"=>1,"shopStatus"=>1,"applyStatus"=>2])->field('shopId,shopName')->limit(500)->select();
		if (!empty($shops)){
			foreach ($shops as $key =>$v){
				$dataPc[] = [
						'url'       => WSTDomain().'/shop-'.$v['shopId'].'.html',
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '0.8',
				];
				$dataMobile[] = [
						'url'       => WSTDomain().'/mhshops-'.$v['shopId'].'.html',
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '0.8',
				];
			}
		}
		/* 更新商品*/
		$goods = Db::name("goods")->where(["dataFlag"=>1,"goodsStatus"=>1,"isSale"=>1])->field('goodsId,goodsName')->limit(500)->select();
		if (!empty($goods)){
			foreach ($goods as $key =>$v){
				$dataPc[] = [
						'url'       => WSTDomain().'/goods-'.$v['goodsId'].'.html',
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '0.8',
				];
				$dataMobile[] = [
						'url'       => WSTDomain().'/mgoods-'.$v['goodsId'].'.html',
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '0.8',
				];
			}
		}
		/* 更新文章 */
		$articles1 = Db::name("articles")->where(["dataFlag"=>1,"isShow"=>1])->where('catId','in','11,12,51,53')->field('articleId,articleTitle')->limit(500)->select();
		if (!empty($articles1)){
			foreach ($articles1 as $key =>$v){
				$dataPc[] = [
						'url'       => WSTDomain().'/news-'.$v['articleId'].'.html',
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '0.8',
				];
				$dataMobile[] = [
						'url'       => WSTDomain().'/mnews/articleId/'.$v['articleId'].'.html',
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '0.8',
				];
			}
		}
		$articles2 = Db::name("articles")->where(["dataFlag"=>1,"isShow"=>1])->where('catId','in','1,5,6,9,10')->field('articleId,articleTitle')->limit(500)->select();
		if (!empty($articles2)){
			foreach ($articles2 as $key =>$v){
				$dataPc[] = [
						'url'       => WSTDomain().'/service-'.$v['articleId'].'.html',
						'lastmod'   => date("Y-m-d"),
						'changefreq'=> 'daily',
						'priority'  => '0.8',
				];
			}
		}
		$sitemapPc = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";
		if (!empty($dataPc)){
			foreach ($dataPc as $item){
				$sitemapPc .= "\r\n<url>\r\n<loc>" . htmlentities($item['url'], ENT_QUOTES) . "</loc>\r\n<lastmod>{$item['lastmod']}</lastmod>\r\n<changefreq>{$item['changefreq']}</changefreq>\r\n<priority>{$item['priority']}</priority>\r\n</url>";
			}
		}
		$sitemapPc .= "\r\n</urlset>";
		file_put_contents($routePc, $sitemapPc);
		$sitemapMobile  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";
		if (!empty($dataMobile)){
			foreach ($dataMobile as $item){
				$sitemapMobile .= "\r\n<url>\r\n<loc>" . htmlentities($item['url'], ENT_QUOTES) . "</loc>\r\n<mobile:mobile type=\"mobile\"/>\r\n<lastmod>{$item['lastmod']}</lastmod>\r\n<changefreq>{$item['changefreq']}</changefreq>\r\n<priority>{$item['priority']}</priority>\r\n</url>";
			}
		}
		$sitemapMobile .= "\r\n</urlset>";
		file_put_contents($routeMobile, $sitemapMobile);
		return WSTReturn('操作成功',1);
	}

	/**
	 * 商家订单自动结算
	 * @return [type] [description]
	 */
	public function autoShopSettlement(){
		$now = date("Y-m-d H:i:s");
    	$where = [];
    	$where[] = ["settlementId","=",0];
    	$where[] = ["afterSaleEndTime","<",$now];
    	$where[] = ["orderStatus","=",2];
    	$olist = Db::name("orders")
		    	->where($where)
		    	->field("orderId,orderNo")
		    	->select();
		if(count($olist)>0){
			$orderIds = [];
			foreach ($olist as $key => $vo) {
				$orderIds[] = $vo["orderId"];
			}
			$where = [];
			$where[] = ["isClose","=",0];
			$where[] = ["serviceStatus","not in",[5,6]];
			$where[] = ["orderId","in",$orderIds];
			$list = Db::name('order_services')
					->where($where)
					->field("orderId,count(orderId) cnt")
					->group("orderId")
					->select();
			$omaps = [];
			foreach ($list as $key => $vo) {
				$omaps[$vo["orderId"]] = $vo["cnt"];
			}
			
			foreach ($olist as $key => $vo) {
				//已过售后期并且没有未处理完的售后单，可进行结算
				if(!isset($omaps[$vo["orderId"]])){
					Db::startTrans();
				    try{
						model('common/Settlements')->speedySettlement($vo["orderId"]);
						Db::commit();
						return WSTReturn('结算成功',1);
			 		}catch (\Exception $e) {
			            Db::rollback();
			            return WSTReturn('结算失败',-1);
			        }
				}
			}
		}
		return WSTReturn('结算成功',1);
	}
	/**
	 * 清除海报图片
	 * @return [type] [description]
	 */
	public function clearPoster(){
		$dirpath = WSTRootPath()."/upload/shares";
		$arr = scandir($dirpath);
		$vmonth = date("Y-m");
		$ctime = time();
		foreach ($arr as $dir1) {
	        // 若名字为.或者..的直接跳过
	        if($dir1 == '.' || $dir1 == '..'){
	            continue;
	        }
	      	WSTDelOtherDir($dirpath."/".$dir1,$vmonth);
	        $arr2 = scandir($dirpath.'/'.$dir1);
	        foreach ($arr2 as $dir2) {
		        // 若名字为.或者..的直接跳过
		        if($dir2 == '.' || $dir2 == '..'){
		            continue;
		        }
		       	$vpath = $dirpath."/".$dir1.'/'.$vmonth;
		        $dh=opendir($vpath);
				while (($file=readdir($dh))!==false) {
					if($file!="." && $file!="..") {
						$vtime = filemtime($vpath."/".$file);
						if ($ctime - $vtime>86400){//清除1天前的文件
							unlink($vpath."/".$file);
						}
				    }
				}	 
				closedir($dh);
		    }
	 	}
		return WSTReturn('清除成功',1);
	}

    /**
     * 店铺到期后自动修改商家为停止状态
     */
    public function autoChangeShopStatus(){
        $shops = Db::name('shops')->field('shopId,expireDate')->where(['shopStatus'=>1,'dataFlag'=>1,'applyStatus'=>2])->select();
        Db::startTrans();
        try{
            foreach($shops as $key => $vo){
                if(WSTStrToTime($vo['expireDate'])<WSTStrToTime(date('Y-m-d')."23:59:59")){
                    Db::name('shops')->where(['shopId'=>$vo['shopId']])->update(['shopStatus'=>-1]);
                    model('goods')->unsaleByshopId($vo['shopId']);
                    //改变店铺钩子事件
        	        hook('afterChangeShopStatus',['shopId'=>$vo['shopId']]);
                }
            }
            Db::commit();
            return WSTReturn('操作成功',1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('操作失败',-1);
        }
    }

    /**
     * 供货商到期后自动修改供货商为停止状态
     */
    public function autoChangeSupplierStatus(){
    	if(WSTConf('CONF.isOpenSupplier')!=1)return;
        $suppliers = Db::name('suppliers')->field('supplierId,expireDate')->where(['supplierStatus'=>1,'dataFlag'=>1,'applyStatus'=>2])->select();
        Db::startTrans();
        try{
            foreach($suppliers as $key => $vo){
                if(WSTStrToTime($vo['expireDate'])<WSTStrToTime(date('Y-m-d')."23:59:59")){
                    Db::name('suppliers')->where(['supplierId'=>$vo['supplierId']])->update(['supplierStatus'=>-1]);
                    model('supplier_goods')->unsaleBysupplierId($vo['supplierId']);
                }
            }
            Db::commit();
            return WSTReturn('操作成功',1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('操作失败',-1);
        }
    }
}