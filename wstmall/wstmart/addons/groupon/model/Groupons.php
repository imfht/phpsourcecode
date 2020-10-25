<?php
namespace addons\groupon\model;
use think\addons\BaseModel as Base;
use wstmart\common\model\GoodsCats;
use think\Db;
use wstmart\common\model\LogSms;
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
 * 团购插件
 */
class Groupons extends Base{
	protected $pk = 'grouponId';
	public function getConfigs(){
		$data = cache('groupon_sms');
		if(!$data){
			$rs = Db::name('addons')->where('name','Groupon')->field('config')->find();
		    $data =  json_decode($rs['config'],true);
		    cache('groupon_sms',$data,31622400);
		}
		return $data;
	}
    /***
     * 安装插件
     */
    public function installMenu(){
    	Db::startTrans();
		try{
			$hooks = ['afterEditGoods','afterCancelOrder'];
			$this->bindHoods("Groupon", $hooks);
			//管理员后台
			$rs = Db::name('menus')->insert(["parentId"=>93,"menuName"=>"团购活动","menuSort"=>1,"dataFlag"=>1,"isShow"=>1,"menuMark"=>"groupon"]);
			if($rs!==false){
				$datas = [];
				$parentId = Db::name('menus')->getLastInsID();
				$datas[] = ["menuId"=>$parentId,"privilegeCode"=>"GROUPON_TGHD_00","privilegeName"=>"查看团购活动","isMenuPrivilege"=>1,"privilegeUrl"=>"/addon/groupon-goods-pageByAdmin","otherPrivilegeUrl"=>"/addon/groupon-goods-pageQueryByAdmin,/addon/groupon-goods-pageAuditQueryByAdmin","dataFlag"=>1,"isEnable"=>1];
				$datas[] = ["menuId"=>$parentId,"privilegeCode"=>"GROUPON_TGHD_04","privilegeName"=>"团购商品操作","isMenuPrivilege"=>0,"privilegeUrl"=>"","otherPrivilegeUrl"=>"/addon/groupon-goods-allow,/addon/groupon-goods-illegal","dataFlag"=>1,"isEnable"=>1];
				$datas[] = ["menuId"=>$parentId,"privilegeCode"=>"GROUPON_TGHD_03","privilegeName"=>"删除团购商品","isMenuPrivilege"=>0,"privilegeUrl"=>"/addon/groupon-goods-delByAdmin","otherPrivilegeUrl"=>"","dataFlag"=>1,"isEnable"=>1];
				Db::name('privileges')->insertAll($datas);
			}
			
			$now = date("Y-m-d H:i:s");
			//商家中心
			Db::name('home_menus')->insert(["parentId"=>77,"menuName"=>"团购活动","menuUrl"=>"/addon/groupon-shops-groupon","menuOtherUrl"=>"/addon/groupon-shops-groupon,/addon/groupon-shops-pageQuery,/addon/groupon-shops-searchGoods,/addon/groupon-shops-edit,/addon/groupon-shops-toEdit,/addon/groupon-shops-del","menuType"=>1,"isShow"=>1,"menuSort"=>1,"dataFlag"=>1,"createTime"=>$now,"menuMark"=>"groupon"]);
			$this->addMobileBtn();
			installSql("groupon");
			Db::commit();
			return true;
		}catch (\Exception $e) {
	 		Db::rollback();
	  		return false;
	   	}
    }

    /**
	 * 删除菜单
	 */
	public function uninstallMenu(){
		Db::startTrans();
		try{
			$hooks = ['afterEditGoods','afterCancelOrder'];
			$this->unbindHoods("Groupon", $hooks);
			Db::name('menus')->where(["menuMark"=>"groupon"])->delete();
			Db::name('home_menus')->where(["menuMark"=>"groupon"])->delete();
			Db::name('privileges')->where("privilegeCode","like","GROUPON_%")->delete();
            //删除微信参数数据
			$tplMsgIds = Db::name('template_msgs')->where([['tplCode','in',explode(',','GROUPON_GOODS_ALLOW,GROUPON_GOODS_REJECT,WX_GROUPON_GOODS_ALLOW,WX_GROUPON_GOODS_REJECT')]])
			  ->column('id');
			if((int)WSTConf('CONF.wxenabled')==1)Db::name('wx_template_params')->where([['parentId','in',$tplMsgIds]])->delete();
			uninstallSql("groupon");//传入插件名
			$this->delMobileBtn();
			Db::commit();
			return true;
		}catch (\Exception $e) {
	 		Db::rollback();
	  		return false;
	   	}
	}

	/**
	 * 菜单显示隐藏
	 */
	public function toggleShow($isShow = 1){
		Db::startTrans();
		try{
			Db::name('menus')->where(["menuMark"=>"groupon"])->update(["isShow"=>$isShow]);
			Db::name('home_menus')->where(["menuMark"=>"groupon"])->update(["isShow"=>$isShow]);
			Db::name('navs')->where(["navUrl"=>"index.php/addon/groupon-goods-lists.html"])->update(["isShow"=>$isShow]);
			if($isShow==1){
				$this->addMobileBtn();
			}else{
				$this->delMobileBtn();
			}
			Db::commit();
			return true;
		}catch (\Exception $e) {
	 		Db::rollback();
	  		return false;
	   	}
	}
	
	public function addMobileBtn(){
	
		$data = array();
		$data["btnName"] = "团购活动";
		$data["btnSrc"] = 0;
		$data["btnUrl"] = "addon/groupon-goods-molists.html";
		$data["btnImg"] = "addons/groupon/view/mobile/index/img/groupon.png";
		$data["addonsName"] = "Groupon";
		$data["btnSort"] = 6;
		Db::name('mobile_btns')->insert($data);
	
		$data = array();
		$data["btnName"] = "团购活动";
		$data["btnSrc"] = 1;
		$data["btnUrl"] = "addon/groupon-goods-wxlists.html";
		$data["btnImg"] = "addons/groupon/view/wechat/index/img/groupon.png";
		$data["addonsName"] = "Groupon";
		$data["btnSort"] = 6;
		Db::name('mobile_btns')->insert($data);


		// app端
		if(WSTDatas('ADS_TYPE',4)){
			$data = array();
			$data["btnName"] = "团购活动";
			$data["btnSrc"] = 3;
			$data["btnUrl"] = "wst://Groupon";
			$data["btnImg"] = "addons/groupon/view/app/img/groupon.png";
			$data["addonsName"] = "Groupon";
			$data["btnSort"] = 5;
			Db::name('mobile_btns')->insert($data);
		}
	
	}
	
	public function delMobileBtn(){
		Db::name('mobile_btns')->where(["addonsName"=>"Groupon"])->delete();
	
	}

	/**
	 * 改变团购信息
	 */
	public function changeGroupon($params){
		$goodsId = (int)$params['goodsId'];
		$date = date('Y-m-d H:i:s');
		Db::name('groupons')
		  ->where(" endTime >='".$date."' and dataFlag=1 and goodsId=".$goodsId)
		  ->update(['grouponStatus'=>0]);
	}

	/**
	 * 取消团购订单
	 */
	public function cancelOrder($params){
		$orderId = (int)$params['orderId'];
		$order = Db::name('orders')->where('orderId',$orderId)->field('orderCode,extraJson,orderCodeTargetId')->find();
        if($order['orderCode']=='groupon'){
            $goods = Db::name('order_goods')->alias('og')
                       ->join('__GOODS__ g','og.goodsId=g.goodsId','inner')
					   ->where('orderId',$orderId)->field('og.*')
					   ->find();
            //处理虚拟产品
			if($goods['goodsType']==1){
	            $extraJson = json_decode($goods['extraJson'],true);
	            foreach ($extraJson as  $ecard) {
	                Db::name('goods_virtuals')->where('id',$ecard['cardId'])
	                      ->update(['orderId'=>0,'orderNo'=>'','isUse'=>0]);
	            }
	            $counts = Db::name('goods_virtuals')->where(['dataFlag'=>1,'goodsId'=>$goods['goodsId'],'isUse'=>0])->count();
	            Db::name('goods')->where('goodsId',$goods['goodsId'])->setField('goodsStock',$counts);
			}
			//修改团购库存
			$this->where('grouponId',$order['orderCodeTargetId'])->setDec('orderNum',$goods['goodsNum']);
        }
	}
	
	

    /**
     * 商家获取团购列表
     */
	public function pageQueryByShop($sId=0){
		$goodsName =input('goodsName');
		$grouponStatus=input('grouponStatus');
		$shopId =($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$where = [];
		$where[] = ['g.shopId','=',$shopId];
		$where[] = ['gu.dataFlag','=',1];
		$where[] = ['g.dataFlag','=',1];
		$where[] = ['g.isSale','=',1];
		if($goodsName !='')$where[] = ['g.goodsName|g.goodsSn', 'like', '%'.$goodsName.'%'];
		if($grouponStatus!='')$where[]=['gu.grouponStatus','=',$grouponStatus];
        $page =  $this->alias('gu')
                      ->join('__GOODS__ g','g.goodsId=gu.goodsId','left')
                      ->where($where)->order('gu.createTime desc')
                      ->field('g.goodsName,g.goodsSn,g.goodsImg,gu.*')
                      ->order('updateTime desc')
                      ->paginate(input('limit/d'))->toArray();
        if(count($page['data'])>0){
        	$time = time();
        	foreach($page['data'] as $key =>$v){
        		$page['data'][$key]['goodsImg'] = WSTImg($v['goodsImg']); 
        		if(WSTStrtotime($v['startTime'])<=$time && WSTStrtotime($v['endTime'])>=$time){
        			$page['data'][$key]['status'] = 1; 
        		}else if(WSTStrtotime($v['startTime'])>$time){
                    $page['data'][$key]['status'] = 0; 
        		}else{
        			$page['data'][$key]['status'] = -1; 
        		}
        		
        	}
        }
        $page['status'] = 1;
        return $page;
	}

    /**
     * 搜索商品
     */
    public function searchGoods($sId=0){
    	$shopId =($sId==0)?(int)session('WST_USER.shopId'):$sId;
    	$shopCatId1 = (int)input('post.shopCatId1');
    	$shopCatId2 = (int)input('post.shopCatId2');
    	$goodsName = input('post.goodsName');
    	$where = [];
    	$where[] = ['goodsStatus','=',1];
    	$where[] = ['dataFlag','=',1];
    	$where[] = ['isSale','=',1];
    	$where[] = ['shopId','=',$shopId];
    	if($shopCatId1>0)$where[] = ['shopCatId1','=',$shopCatId1];
    	if($shopCatId2>0)$where[] = ['shopCatId2','=',$shopCatId2];
    	if($goodsName!='')$where[] = ['goodsName','like','%'.$goodsName.'%'];
    	$rs = Db::name('goods')->where($where)->field('goodsName,goodsId,marketPrice,shopPrice,goodsType')->select();
        return WSTReturn('',1,$rs);
    }
	/**
     * 获取商品类别
	 */
	public function getGoodsCats(){
		$rs = Db::name('goods_cats')
		        ->where(['dataFlag'=>1,'isShow'=>1])
		        ->order('catSort asc')
		        ->field('parentId pid,catId id,catName text')
		        ->select();
		return WSTReturn('',1,$rs);
	}
	/**
	 *  获取团购商品
	 */
	public function getById($id,$sId=0){
		$where = [];
		$where['g.shopId'] =($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$where['gu.grouponId'] = $id;
		$where['gu.dataFlag'] = 1;
		$where['g.dataFlag'] = 1;
		return $this->alias('gu')->join('__GOODS__ g','g.goodsId=gu.goodsId','left')->where($where)->field('g.goodsName,g.marketPrice,g.shopPrice,gu.*')->find();
	}

	/**
	 * 新增团购
	 */
	public function add($sId=0){
		$data = input('post.');
		$goods = model('common/Goods')->get((int)$data['goodsId']);
		if(empty($goods))return WSTReturn('商品不存在');
		if((float)$data['grouponPrice']<=0)return WSTReturn('团购价格必须大于0');
        if((int)$data['grouponNum']<=0)return WSTReturn('团购数量必须大于0');
        if((int)$data['grouponNum'] < (int)$data['limitNum'])return WSTReturn('每人最多可团购数不能大于团购数量');
		if($data['startTime']=='' || $data['endTime']=='')return WSTReturn('请选择有效团购时间');
		if(strtotime($data['startTime']) >= strtotime($data['endTime']))return WSTReturn('团购开始时间必须比团购结束时间早');
		//判断是否已经存在同时间的团购
		$where = [];
		$where['goodsId'] = $data['goodsId'];
		$where['dataFlag'] = 1;
		$whereOr = ' ( ("'.date('Y-m-d H:i:s',strtotime($data['startTime'])).'" between startTime and endTime) or ( "'.date('Y-m-d H:i:s',strtotime($data['endTime'])).'" between startTime and endTime) ) ';
		$rn = $this->where($where)->where($whereOr)->Count();
		if($rn>0)return WSTReturn('该商品已存在另外一个相同时段的团购活动中');
		WSTUnset($data,'grouponId,cat_0,illegalRemarks');
		$data['shopId'] =($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$data['dataFlag'] = 1;
		$data['orderNum'] = 0;
		$data['grouponStatus'] = 0;
		$data['updateTime'] = date('Y-m-d H:i:s');
		$data['createTime'] = date('Y-m-d H:i:s');
		$result = $this->allowField(true)->save($data);
		if(false !== $result){
			return WSTReturn('新增成功',1);
		}
		return WSTReturn('新增失败');
	}

	/**
	 * 编辑商品 
	 */
	public function edit($sId=0){
		$data = input('post.');
		$goods = model('common/Goods')->get((int)$data['goodsId']);
		if(empty($goods))return WSTReturn('商品不存在');
		if((int)$data['grouponPrice']<=0)return WSTReturn('团购价格必须大于0');
		if((int)$data['grouponNum']<=0)return WSTReturn('团购数量必须大于0');
        if((int)$data['grouponNum'] < (int)$data['limitNum'])return WSTReturn('每人最多可团必须小于团购数量');
		if($data['startTime']=='' || $data['endTime']=='')return WSTReturn('请选择有效团购时间');
		if(strtotime($data['startTime']) >= strtotime($data['endTime']))return WSTReturn('团购开始时间必须必团购结束时间早');
		//判断是否已经存在同时间的团购
		$grouponId = $data['grouponId'];
		$shopId =($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$where = [];
		$where[] = ['goodsId','=',$data['goodsId']];
		$where[] = ['grouponId','<>',$data['grouponId']];
		$where[] = ['dataFlag','=',1];
		$whereOr = ' ( ("'.date('Y-m-d H:i:s',strtotime($data['startTime'])).'" between startTime and endTime) or ( "'.date('Y-m-d H:i:s',strtotime($data['endTime'])).'" between startTime and endTime) ) ';
		$rn = $this->where($where)->where($whereOr)->Count();
		if($rn>0)return WSTReturn('该商品已存在另外一个相同时段的团购活动中');
		WSTUnset($data,'grouponId,shopId,dataFlag,createTime,cat_0,illegalRemarks,orderNum');
		$data['grouponStatus'] = 0;
		$data['updateTime'] = date('Y-m-d H:i:s');
		$result = $this->allowField(true)->update($data,['grouponId'=>$grouponId,'shopId'=>$shopId]);
		if(false !== $result){
			return WSTReturn('编辑成功',1);
		}
		return WSTReturn('编辑失败');
	}

	/**
	 * 删除团购
	 */
	public function del($sId=0){
		$id = (int)input('id');
		$shopId = ($sId==0)?(int)session('WST_USER.shopId'):$sId;
		$data = [];
		$data['shopId'] = $shopId;
		$data['grouponId'] = $id;
        $rs = $this->update(['dataFlag'=>-1],$data);
        return WSTReturn('删除成功',1);
	}


	/***
	 * 获取前台团购列表
	 */
	public function pageQuery(){
		$goodsCatId = (int)input('catId');
		$goodsName = input('goodsName');
		$areaId = (int)input('areaId');
		$where = [];
		if($goodsCatId>0){
			$gc = new GoodsCats();
			$goodsCatIds = $gc->getParentIs($goodsCatId);
			$where[] = ['goodsCatIdPath','like',implode('_',$goodsCatIds).'_%'];
		}
		if($goodsName!='')$where[] = ['goodsName','like','%'.$goodsName.'%'];
		$page = Db::name('groupons')->alias('gu')->join('__GOODS__ g','gu.goodsId=g.goodsId','inner')
		          ->where('g.dataFlag=1 and g.isSale=1 and g.goodsStatus=1 and gu.dataFlag=1 and gu.grouponStatus=1')
		          ->where($where)
		          ->field('g.goodsName,g.goodsImg,g.marketPrice,gu.*')
		          ->order('gu.updateTime desc,gu.startTime asc,grouponId desc')
		          ->paginate(input('pagesize/d',16))->toArray();
		if(count($page)>0){
			$time = time();
			foreach($page['data'] as $key =>$v){
				$page['data'][$key]['goodsImg'] = WSTImg($v['goodsImg']); 
				$page['data'][$key]['zhekou'] = round($v['grouponPrice']/$v['marketPrice']*10,1); 
				if(strtotime($v['startTime'])<=$time && strtotime($v['endTime'])>=$time){
        			$page['data'][$key]['status'] = 1; 
        		}else if(strtotime($v['startTime'])>$time){
                    $page['data'][$key]['status'] = 0; 
        		}else{
        			$page['data'][$key]['status'] = -1; 
        		}
        		if($v['orderNum']>=$v['grouponNum']){
        			$page['data'][$key]['status'] = -1;
        		}
			}
		}
		return $page;
	}

	/**
	 * 获取团购详情
	 */
	public function getBySale($grouponId,$uId=0){
        $userId = (int)session('WST_USER.userId');
		$key = input('key');
		$where = ['dataFlag'=>1,'grouponId'=>$grouponId];
		$gu = $this->where($where)->find();
		$viKey = WSTShopEncrypt($gu['shopId']);
        if($key!=''){	
            if($viKey!=$key && $gu['grouponStatus']!=1)return [];
        }else{
        	if($gu['grouponStatus']!=1)return [];
        }
		$goodsId = $gu['goodsId'];
		if(empty($gu))return [];
		$gu = $gu->toArray();
		$rs = Db::name('goods')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->find();
		if(!empty($rs)){
			$rs['goodsDesc'] = htmlspecialchars_decode($rs['goodsDesc']);
			$rs['goodsDesc'] = str_replace('${DOMAIN}',WSTConf('CONF.resourcePath'),$rs['goodsDesc']);
			Db::name('goods')->where('goodsId',$goodsId)->setInc('visitNum',1);
			$rs = array_merge($rs,$gu);
			$time = time();
			if(strtotime($rs['startTime'])<=$time && strtotime($rs['endTime'])>=$time){
        		$rs['status'] = 1; 
        	}else if(strtotime($rs['startTime'])>$time){
                $rs['status'] = 0; 
        	}else{
        	    $rs['status'] = -1; 
        	}
        	if($rs['orderNum']>=$rs['grouponNum'])$rs['status'] = -1;
			$rs['read'] = false;
			$rs['canBuyNum']=$rs['limitNum'];
            if($userId&&$userId>0){
                $myOrder=Db::name('orders')->where(['orderCode'=>'groupon','orderCodeTargetId'=>$grouponId,'dataFlag'=>1,'userId'=>$userId])->whereNotIn('orderStatus',-1)->column("orderId");//获取个人参与此团购的所有订单
                $myOrderNum=Db::name('order_goods')->where("orderId","in",$myOrder)->sum("goodsNum");//获取个人参与此团购的总数
                if($myOrderNum>$rs['limitNum']){
                    $rs['status'] = -1;

                }
                $rs['canBuyNum']=$rs['limitNum']-$myOrderNum;
            }
			//判断是否可以公开查看
			if($rs['isSale']==0 || $rs['goodsStatus']==0 )return [];
			if($key!='')$rs['read'] = true;
			//获取店铺信息
			$rs['shop'] = model('common/shops')->getShopInfo((int)$rs['shopId']);

			if(empty($rs['shop']))return [];
			$goodsCats = Db::name('cat_shops')->alias('cs')->join('__GOODS_CATS__ gc','cs.catId=gc.catId and gc.dataFlag=1','left')->join('__SHOPS__ s','s.shopId = cs.shopId','left')
			->where('cs.shopId',$rs['shopId'])->field('cs.shopId,s.shopTel,gc.catId,gc.catName')->select();
			$rs['shop']['catId'] = $goodsCats[0]['catId'];
			$rs['shop']['shopTel'] = $goodsCats[0]['shopTel'];
			$cat = [];
			foreach ($goodsCats as $v){
				$cat[] = $v['catName'];
			}
			$rs['shop']['cat'] = implode('，',$cat);
			
			$gallery = [];
			$gallery[] = $rs['goodsImg'];
			if($rs['gallery']!=''){
				$tmp = explode(',',$rs['gallery']);
				$gallery = array_merge($gallery,$tmp);
			}
			$rs['gallery'] = $gallery;
			if($rs['isSpec']==1){
				//获取销售规格
				$sales = Db::name('goods_specs')->where(['goodsId'=>$goodsId,'isDefault'=>1])->field('id,isDefault,productNo,specIds,marketPrice,specPrice,specStock')->find();
				$specIds = [];
				if(!empty($sales)){
					$str = explode(':',$sales['specIds']);
					foreach ($str as $skey => $sv) {
						if(!in_array($sv,$specIds))$specIds[] = $sv;
					}
					sort($str);
					unset($sales['specIds']);
					$rs['saleSpec'][implode(':',$str)] = $sales;
				}
				//获取默认规格值
				$specs = Db::name('spec_cats')->alias('gc')
				           ->join('__SPEC_ITEMS__ sit','gc.catId=sit.catId','inner')
				           ->where(['sit.goodsId'=>$goodsId,'gc.isShow'=>1,'sit.dataFlag'=>1])
				           ->field('gc.isAllowImg,gc.catName,sit.catId,sit.itemId,sit.itemName,sit.itemImg')
				           ->order('gc.isAllowImg desc,gc.catSort asc,gc.catId asc')->select();                     
				foreach ($specs as $key =>$v){
					if(in_array($v['itemId'],$specIds)){
						$rs['spec'][$v['catId']]['name'] = $v['catName'];
						$rs['spec'][$v['catId']]['list'][] = $v;
					}
				}

			}
			//获取商品属性
			$rs['attrs'] = Db::name('attributes')->alias('a')->join('goods_attributes ga','a.attrId=ga.attrId','inner')
			                   ->where(['a.isShow'=>1,'dataFlag'=>1,'goodsId'=>$goodsId])->field('a.attrName,ga.attrVal')
			                   ->order('attrSort asc')->select();
			//获取商品评分
			$rs['scores'] = Db::name('goods_scores')->where('goodsId',$goodsId)->field('totalScore,totalUsers')->find();
			$rs['scores']['totalScores'] = ($rs['scores']['totalScore']==0)?5:WSTScore($rs['scores']['totalScore'],$rs['scores']['totalUsers'],5,0,3);
			WSTUnset($rs, 'totalUsers');
			//关注
			$f = model('common/Favorites');
			$rs['favShop'] = $f->checkFavorite($rs['shopId'],1,$uId);
			$rs['favGood'] = $f->checkFavorite($goodsId,0,$uId);


			// 获取一条商品评价
			$appr = model('app/GoodsAppraises')
								->alias('ga')
								->join('users U','ga.userId=U.userId')
								->field('U.loginName,U.userPhoto,ga.content')
								->where(['goodsId'=>$goodsId,'U.dataFlag'=>1,'ga.dataFlag'=>1])
								->find();
			if(!empty($appr)){
				// 若未设置头像,则取商城默认头像
				$appr['userPhoto'] = ($appr['userPhoto']!='')?$appr['userPhoto']:WSTConf('CONF.userLogo');
				// 过滤html标签
				$appr['content'] = strip_tags($appr['content']);
				// 处理匿名
				$start = floor((strlen($appr['loginName'])/2))-1;
				$appr['loginName'] = substr_replace($appr['loginName'],'**',$start,2);
			}
			$rs['goodsAppr'] = $appr;
		}
		return $rs;
	}


	/**
     * 下单
     */
	public function addCart($uId=0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		if($userId==0)return WSTReturn('您尚未登录系统，请先登录系统');
		$id = (int)input('post.id');
		$cartNum = (int)input('post.buyNum',1);
		$cartNum = ($cartNum>0)?$cartNum:1;
		$goodsSpecId = 0;
		//验证传过来的商品是否合法
		$chk = $this->checkGoodsSaleSpec($id);
		if($chk['status']==-1)return $chk;
		//检测库存是否足够
		if($chk['data']['stock']<$cartNum)return WSTReturn("团购商品失败，商品库存不足", -1);
        $carts = [];
        $carts['grouponId'] = $id;
        $carts['cartNum'] = $cartNum;
        session('GROUPON_CARTS',$carts);
        return WSTReturn("团购商品成功", 1);
	}
	/**
	 * 验证商品是否合法
	 */
	public function checkGoodsSaleSpec($grouponId){
		$goods = $this->alias('gu')->join('__GOODS__ g','gu.goodsId=g.goodsId','inner')
		              ->where(['g.goodsStatus'=>1,'g.dataFlag'=>1,'g.isSale'=>1,'gu.dataFlag'=>1,'gu.grouponId'=>$grouponId,'gu.grouponStatus'=>1])
		              ->field('g.goodsId,isSpec,goodsType,gu.grouponNum,gu.orderNum,gu.startTime,gu.endTime')
		              ->find();
		if(empty($goods))return WSTReturn("添加失败，无效的商品信息", -1);
		//判断团购是否过期
		$time = time();
		if(strtotime($goods['startTime']) > $time)return WSTReturn('对不起，团购活动尚未开始');
		if(strtotime($goods['endTime']) < $time)return WSTReturn('对不起，您来晚了，团购活动已结束');
		$goodsId = $goods['goodsId'];
		$goodsStock = (int)$goods['grouponNum']-(int)$goods['orderNum'];
		//有规格的话查询规格是否正确
		if($goods['isSpec']==1){
			$specs = Db::name('goods_specs')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->field('id,isDefault')->select();
			if(count($specs)==0){
				return WSTReturn("添加失败，无效的商品信息", -1);
			}
			$goodsSpecId = 0;
			foreach ($specs as $key => $v){
				if($v['isDefault']==1){
					$goodsSpecId = $v['id'];
					$isFindSpecId = true;
				}
			}
			
			if($goodsSpecId==0)return WSTReturn("添加失败，无效的商品信息", -1);//有规格却找不到规格的话就报错
			return WSTReturn("", 1,['goodsId'=>$goods['goodsId'],'goodsSpecId'=>$goodsSpecId,'stock'=>$goodsStock,'goodsType'=>$goods['goodsType']]);
		}else{
			return WSTReturn("", 1,['goodsId'=>$goods['goodsId'],'goodsSpecId'=>0,'stock'=>$goodsStock,'goodsType'=>$goods['goodsType']]);
		}
	}

	/**
	 * 获取session中购物车列表
	 */
	public function getCarts(){
		$userId = (int)session('WST_USER.userId');
		$tmp_carts = session('GROUPON_CARTS');
		$where = [];
		$where['gu.grouponId'] = $tmp_carts['grouponId'];
		$where['gu.dataFlag'] = 1;
		$where['gu.grouponStatus'] = 1;
		$where['g.goodsStatus'] = 1;
		$where['g.dataFlag'] = 1;
		$where['g.isSale'] = 1;
		$rs = $this->alias('gu')->join('__GOODS__ g','gu.goodsId=g.goodsId','inner')
		           ->join('__SHOPS__ s','s.shopId=gu.shopId','left')
		           ->join('__GOODS_SPECS__ gs','g.goodsId=gs.goodsId and gs.isDefault','left')
		           ->where($where)
		           ->field('s.userId,s.shopId,s.shopName,g.goodsId,s.shopQQ,shopWangWang,g.goodsName,gu.grouponPrice shopPrice,gu.grouponNum goodsStock,gu.orderNum,g.goodsImg,g.goodsCatId,g.goodsType,gs.specIds,gs.id goodsSpecId,gu.startTime,gu.endTime,g.isFreeShipping')
		           ->find();
		if(empty($rs))return ['carts'=>[],'goodsTotalMoney'=>0,'goodsTotalNum'=>0]; 
		$rs = $rs->toArray();
		// 确保goodsSpecId不为null.
		$rs['goodsSpecId'] = (int)$rs['goodsSpecId'];
		$rs['cartNum'] = $tmp_carts['cartNum'];
		$carts = [];
		$goodsTotalNum = 0;
		$goodsTotalMoney = 0;
		if(!isset($carts['goodsMoney']))$carts['goodsMoney'] = 0;
		$carts['isFreeShipping'] = ($rs['isFreeShipping']==1)?true:false;
		$carts['grouponId'] = $tmp_carts['grouponId'];
		$carts['shopId'] = $rs['shopId'];
		$carts['shopName'] = $rs['shopName'];
		$carts['shopQQ'] = $rs['shopQQ'];
		$carts['userId'] = $rs['userId'];
		$carts['shopWangWang'] = $rs['shopWangWang'];
		//判断能否购买，预设allowBuy值为10，为将来的各种情况预留10个情况值，从0到9
		$rs['allowBuy'] = 10;
		if($rs['goodsStock']<0){
			$rs['allowBuy'] = 0;//库存不足
		}else if($rs['goodsStock']<$tmp_carts['cartNum']){
			$rs['allowBuy'] = 1;//库存比购买数小
		}
		$carts['goodsMoney'] = $carts['goodsMoney'] + $rs['shopPrice'] * $rs['cartNum'];
		$goodsTotalMoney = $goodsTotalMoney + $rs['shopPrice'] * $rs['cartNum'];
		$goodsTotalNum = $rs['cartNum'];
		if($rs['specIds']!=''){
			//加载规格值
			$specs = DB::name('spec_items')->alias('s')->join('__SPEC_CATS__ sc','s.catId=sc.catId','left')
			           ->where(['s.goodsId'=>$rs['goodsId'],'s.dataFlag'=>1])
		               ->field('catName,itemId,itemName')
		           	   ->select();
		    if(count($specs)>0){ 
			    $specMap = [];
			    foreach ($specs as $key =>$v){
			    	$specMap[$v['itemId']] = $v;
			    }
				$strName = [];
				if($rs['specIds']!=''){
				    $str = explode(':',$rs['specIds']);
				    foreach ($str as $vv){
				    	if(isset($specMap[$vv]))$strName[] = $specMap[$vv];
				    }
				}
				$rs['specNames'] = $strName;
			}
		}
		unset($rs['shopName']);
		$carts['goods'] = $rs;
		return ['carts'=>$carts,'goodsType'=>$rs['goodsType'],'goodsTotalMoney'=>$goodsTotalMoney,'goodsTotalNum'=>$goodsTotalNum]; 
	}

	/**
	 * 计算订单金额
	 */
	public function getCartMoney($uId=0){
		$data = ['shops'=>[],'totalMoney'=>0,'totalGoodsMoney'=>0];
        $areaId = input('post.areaId2/d',-1);
		//计算各店铺运费及金额
		$deliverType = (int)input('deliverType');
		$carts = $this->getCarts();
		$deliverType = ($carts['goodsType']==1)?1:$deliverType;
		$shopFreight = 0;
		//判断是否包邮
		if($carts['carts']['isFreeShipping']){
			$shopFreight = 0;
		}else{
			if($areaId>0){
				$shopFreight = ($deliverType==1)?0:WSTOrderFreight($carts['carts']['shopId'],$areaId);
			}else{
				$shopFreight = 0;
			}
			
		}

		$data['shops']['freight'] = $shopFreight;
		$data['shops']['shopId'] = $carts['carts']['shopId'];
		$data['shops']['goodsMoney'] = $carts['carts']['goodsMoney'];
		$data['totalGoodsMoney'] = $carts['carts']['goodsMoney'];
		$data['totalMoney'] += $carts['carts']['goodsMoney'] + $shopFreight;
		$data['useScore'] = 0;
		$data['scoreMoney'] = 0;
		//计算积分
		$isUseScore = (int)input('isUseScore');
		if($isUseScore==1){
            $userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
			$useScore = (int)input('useScore');
			$user = model('common/users')->getFieldsById($userId,'userScore');
			if($useScore>$user['userScore'])$useScore = $user['userScore'];
			$moneyToScore = WSTScoreToMoney($data['totalGoodsMoney'],true);
			if($useScore>$moneyToScore)$useScore = $moneyToScore;
			$money = WSTScoreToMoney($useScore);
			$data['useScore'] = $useScore;
			$data['scoreMoney'] = $money;
		}
		$data['realTotalMoney'] = $data['totalMoney'] - $data['scoreMoney'];
		return WSTReturn('',1,$data);
	}

	/**
	 * 虚拟商品下单
	 */
	public function submitByVirtual($carts,$orderSrc = 0,$uId){
        $addressId = 0;
		$isInvoice = ((int)input('post.isInvoice')!=0)?1:0;
		$invoiceClient = ($isInvoice==1)?input('post.invoiceClient'):'';
		$payType = 1;
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		if($userId==0)return WSTReturn('您尚未登录系统，请先登录系统');
		$isUseScore = (int)input('isUseScore');
		$useScore = (int)input('useScore');
		//计算出订单应该分配的金额和积分
		$scoreMoney = model('common/orders')->getOrderScoreMoney($isUseScore,$useScore,$uId);
		//生成订单
		Db::startTrans();
		try{
			$goods = $carts['carts']['goods'];
			$carts = $carts['carts'];
			//给用户分配卡券
			$cards = model('common/GoodsVirtuals')->where(['goodsId'=>$goods['goodsId'],'dataFlag'=>1,'shopId'=>$goods['shopId'],'isUse'=>0])->lock(true)->limit($goods['cartNum'])->select();
			if(count($cards)<$goods['cartNum'])return WSTReturn("下单失败，团购商品库存不足");
            //再次检查团购是否已满
            $tmpGroupon = $this->where(['grouponId'=>$carts['grouponId']])->lock(true)->field('grouponNum,limitNum')->find();
            $myOrder=Db::name('orders')->where(['orderCode'=>'groupon','orderCodeTargetId'=>$carts['grouponId'],'dataFlag'=>1,'userId'=>$userId])->whereNotIn('orderStatus',-1)->column("orderId");//获取个人参与此团购的所有订单
            $myOrderNum=Db::name('order_goods')->where("orderId","in",$myOrder)->sum("goodsNum");//获取个人参与此团购的总数
            $nums=$myOrderNum+$goods['cartNum'];
            if($tmpGroupon['limitNum']<$nums)return WSTReturn("下单失败，购买数量超过限额");
			//修改库存
			Db::name('goods')->where('goodsId',$goods['goodsId'])->setDec('goodsStock',$goods['cartNum']);
			Db::name('goods')->where('goodsId',$goods['goodsId'])->setInc('saleNum',1);
			$orderunique = WSTOrderQnique();
			
			$orderNo = WSTOrderNo(); 
			$orderScore = 0;
			//创建订单
			$order = [];
			$order['orderNo'] = $orderNo;
			$order['userId'] = $userId;
			$order['orderType'] = 1;
			$order['areaId'] = 0;
			$order['userName'] = '';
			$order['userAddress'] = '';
			$order['shopId'] = $carts['shopId'];
			$order['payType'] = $payType;
			$order['goodsMoney'] = $carts['goodsMoney'];
			//计算运费和总金额
			$order['deliverType'] = 1;
			$order['deliverMoney'] = 0;
			$order['totalMoney'] = $order['goodsMoney'];
            //积分支付-计算分配积分和金额
            $order['scoreMoney'] = 0;
			$order['useScore'] = 0;
			if($scoreMoney['useMoney']>0){
				$order['scoreMoney'] = $scoreMoney['useMoney'];
				$order['useScore'] = $scoreMoney['useScore'];
			}
			//实付金额要减去积分兑换的金额
			$order['realTotalMoney'] = $order['totalMoney'] - $order['scoreMoney'];
			$order['needPay'] = $order['realTotalMoney'];
			$order['orderCode'] = 'groupon';
			$order['orderCodeTargetId'] = $carts['grouponId'];
			$order['extraJson'] = json_encode(['grouponId'=>$carts['grouponId']]);
            if($order['needPay']>0){
                $order['orderStatus'] = -2;//待付款
				$order['isPay'] = 0; 
            }else{
                $order['orderStatus'] = 0;//待发货
				$order['isPay'] = 1; 
				$order['payTime'] = date('Y-m-d H:i:s'); 
            }
			//积分
			$orderScore = 0;
			//如果开启下单获取积分则有积分
			if(WSTConf('CONF.isOrderScore')==1){
				$orderScore = WSTMoneyGiftScore($order['goodsMoney']);
			}
			$order['orderScore'] = $orderScore;
			$order['isInvoice'] = $isInvoice;
			$order['invoiceJson'] = model('common/invoices')->getInviceInfo((int)input('param.invoiceId'));// 发票信息
			$order['invoiceClient'] = $invoiceClient;
			$order['orderRemarks'] = input('post.remark_'.$carts['shopId']);
			$order['orderunique'] = $orderunique;
			$order['orderSrc'] = $orderSrc;
			$order['dataFlag'] = 1;
			$order['payRand'] = 1;
			$order['createTime'] = date('Y-m-d H:i:s');
			$m = model('common/orders');
			$result = $m->data($order,true)->isUpdate(false)->allowField(true)->save($order);
			if(false !== $result){
				$orderId = $m->orderId;
				//标记虚拟卡券为占用状态
				$goodsCards = [];
			    foreach ($cards as $key => $card) {
				    $card->isUse = 1;
				    $card->orderId = $orderId;
				    $card->orderNo = $orderNo;
				    $card->save();
				    $goodsCards[] = ['cardId'=>$card->id];
			    }
				$goods = $carts['goods'];
				//创建订单商品记录
				$orderGgoods = [];
				$orderGoods['orderId'] = $orderId;
				$orderGoods['goodsType'] = 1;
				$orderGoods['goodsId'] = $goods['goodsId'];
				$orderGoods['goodsNum'] = $goods['cartNum'];
				$orderGoods['goodsPrice'] = $goods['shopPrice'];
				$orderGoods['goodsSpecId'] = $goods['goodsSpecId'];
				if(!empty($goods['specNames'])){
					$specNams = [];
					foreach ($goods['specNames'] as $pkey =>$spec){
						$specNams[] = $spec['catName'].'：'.$spec['itemName'];
					}
					$orderGoods['goodsSpecNames'] = implode('@@_@@',$specNams);
				}else{
					$orderGoods['goodsSpecNames'] = '';
				}
				$orderGoods['goodsName'] = $goods['goodsName'];
				$orderGoods['goodsImg'] = $goods['goodsImg'];
				$orderGoods['commissionRate'] = WSTGoodsCommissionRate($goods['goodsCatId']);
				$orderGoods['extraJson'] = json_encode($goodsCards);
				Db::name('order_goods')->insert($orderGoods);
				//计算订单佣金
				$commissionFee = 0;
				if((float)$orderGoods['commissionRate']>0){
					$commissionFee += round($orderGoods['goodsPrice']*1*$orderGoods['commissionRate']/100,2);
				}
				model('common/orders')->where('orderId',$orderId)->update(['commissionFee'=>$commissionFee]);
				//修改团购数量
				$this->where('grouponId',$carts['grouponId'])->setInc('orderNum',$goods['cartNum']);
				//创建积分流水--如果有抵扣积分就肯定是开启了支付支付
				if($order['useScore']>0){
					$score = [];
				    $score['userId'] = $userId;
					$score['score'] = $order['useScore'];
					$score['dataSrc'] = 1;
					$score['dataId'] = $orderId;
					$score['dataRemarks'] = "交易订单【".$orderNo."】使用积分".$order['useScore']."个";
					$score['scoreType'] = 0;
					model('common/UserScores')->add($score);
				}
                    
				//建立订单记录
				$logOrder = [];
				$logOrder['orderId'] = $orderId;
				$logOrder['orderStatus'] = -2;
				$logOrder['logContent'] = "下单成功，等待用户支付";
				$logOrder['logUserId'] = $userId;
				$logOrder['logType'] = 0;
				$logOrder['logTime'] = date('Y-m-d H:i:s');
				Db::name('log_orders')->insert($logOrder);
				if($payType==1 && $order['needPay']==0){
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 0;
					$logOrder['logContent'] = "订单已支付，下单成功";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
				}
				//给商家发送消息
				$tpl = WSTMsgTemplates('ORDER_SUBMIT');
		        if($tpl['tplContent']!='' && $tpl['status']=='1'){
		            $find = ['${ORDER_NO}'];
		            $replace = [$orderNo];
		           
		            $msg = array();
		            $msg["shopId"] = $carts['shopId'];
		            $msg["tplCode"] = $tpl["tplCode"];
		            $msg["msgType"] = 1;
		            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
		            $msg["msgJson"] = ['from'=>1,'dataId'=>$orderId];
		            model("common/MessageQueues")->add($msg);
		        }
		        //判断是否需要发送管理员短信
	            $tpl = WSTMsgTemplates('PHONE_ADMIN_SUBMIT_ORDER');
	            if((int)WSTConf('CONF.smsOpen')==1 && (int)WSTConf('CONF.smsSubmitOrderTip')==1 && $tpl['tplContent']!='' && $tpl['status']=='1'){
					$params = ['tpl'=>$tpl,'params'=>['ORDER_NO'=>$orderNo]];
					$staffs = Db::name('staffs')->where([['staffId','in',explode(',',WSTConf('CONF.submitOrderTipUsers'))],['staffStatus','=',1],['dataFlag','=',1]])->field('staffPhone')->select();
					for($i=0;$i<count($staffs);$i++){
						if($staffs[$i]['staffPhone']=='')continue;
						$m = new LogSms();
				        $rv = $m->sendAdminSMS(0,$staffs[$i]['staffPhone'],$params,'submit','');
				    }
	            }
		        //微信消息
		        if((int)WSTConf('CONF.wxenabled')==1){
		            $params = [];
		            $params['ORDER_NO'] = $orderNo;
	                $params['ORDER_TIME'] = date('Y-m-d H:i:s');             
	                $goodsNames = $goods['goodsName']."*".$goods['cartNum'];
		            $params['GOODS'] = $goodsNames;
		            $params['MONEY'] = $order['realTotalMoney'] + $order['scoreMoney'];
		            $params['ADDRESS'] = '';
		            $params['PAY_TYPE'] = WSTLangPayType($order['payType']);
			        
			        $msg = array();
					$tplCode = "WX_ORDER_SUBMIT";
					$msg["shopId"] = $carts['shopId'];
		            $msg["tplCode"] = $tplCode;
		            $msg["msgType"] = 4;
		            $msg["paramJson"] = ['CODE'=>$tplCode,'URL'=>Url('wechat/orders/sellerorder','',true,true),'params'=>$params];
		            $msg["msgJson"] = "";
		            model("common/MessageQueues")->add($msg);
			        //判断是否需要发送给管理员消息
		            if((int)WSTConf('CONF.wxSubmitOrderTip')==1){
		                $params = [];
			            $params['ORDER_NO'] = $orderNo;
		                $params['ORDER_TIME'] = date('Y-m-d H:i:s');             
		                $goodsNames = $goods['goodsName']."*".$goods['cartNum'];
			            $params['GOODS'] = $goodsNames;
			            $params['MONEY'] = $order['realTotalMoney'] + $order['scoreMoney'];
			            $params['ADDRESS'] = '';
			            $params['PAY_TYPE'] = WSTLangPayType($order['payType']);
			            WSTWxBatchMessage(['CODE'=>'WX_ADMIN_ORDER_SUBMIT','userType'=>3,'userId'=>explode(',',WSTConf('CONF.submitOrderTipUsers')),'params'=>$params]);
		            }
			    }
				//已付款的虚拟商品
				if($order['needPay']==0){
					model('common/orders')->handleVirtualGoods($orderId);
				}
			}
			Db::commit();
			//删除session的购物车商品
			session('GROUPON_CARTS',null);
			return WSTReturn("提交订单成功", 1,$orderunique);
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('提交订单失败',-1);
        }
	}
	/**
	 * 实物商品下单
	 */
	public function submitByEntity($carts,$orderSrc = 0,$uId=0){
		$addressId = (int)input('post.s_addressId');
		$deliverType = ((int)input('post.deliverType')!=0)?1:0;
		$isInvoice = ((int)input('post.isInvoice')!=0)?1:0;
		$invoiceClient = ($isInvoice==1)?input('post.invoiceClient'):'';
		$payType = ((int)input('post.payType')!=0)?1:0;
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		if($userId==0)return WSTReturn('您尚未登录系统，请先登录系统');
		$isUseScore = (int)input('isUseScore');
		$useScore = (int)input('useScore');
        //再次检查团购是否已满
        $goods = $carts['carts']['goods'];
        $tmpGroupon = $this->where(['grouponId'=>$carts['carts']['grouponId']])->lock(true)->field('grouponNum,limitNum')->find();
        $myOrder=Db::name('orders')->where(['orderCode'=>'groupon','orderCodeTargetId'=>$carts['carts']['grouponId'],'dataFlag'=>1,'userId'=>$userId])->whereNotIn('orderStatus',-1)->column("orderId");//获取个人参与此团购的所有订单
        $myOrderNum=Db::name('order_goods')->where("orderId","in",$myOrder)->sum("goodsNum");//获取个人参与此团购的总数
        $nums=$myOrderNum+$goods['cartNum'];
		if($tmpGroupon['limitNum']<$nums)return WSTReturn("下单失败，购买数量超过限额");

		if($deliverType==0){// 配送方式为快递，必须有用户地址
			//检测地址是否有效
			$address = Db::name('user_address')->where(['userId'=>$userId,'addressId'=>$addressId,'dataFlag'=>1])->find();
			if(empty($address)){
				return WSTReturn("无效的用户地址");
			}
		    $areaIds = [];
	        $areaMaps = [];
	        $tmp = explode('_',$address['areaIdPath']);
	        $address['areaId2'] = $tmp[1];//记录配送城市
	        foreach ($tmp as $vv){
	         	if($vv=='')continue;
	         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
	        }
	        if(!empty($areaIds)){
		         $areas = Db::name('areas')->where([['dataFlag','=',1],['areaId','in',$areaIds]])->field('areaId,areaName')->select();
		         foreach ($areas as $v){
		         	 $areaMaps[$v['areaId']] = $v['areaName'];
		         }
		         $tmp = explode('_',$address['areaIdPath']);
		         $areaNames = [];
			     foreach ($tmp as $vv){
		         	 if($vv=='')continue;
		         	 $areaNames[] = $areaMaps[$vv];
		         	 $address['areaName'] = implode('',$areaNames);
		         }
	        }
			$address['userAddress'] = $address['areaName'].$address['userAddress'];
			WSTUnset($address, 'isDefault,dataFlag,createTime,userId');
		}else{
			$address = [];
			$address['areaId'] = 0;
			$address['userName'] = '';
			$address['userAddress'] = '';
		}


		//计算出订单应该分配的金额和积分
		$scoreMoney = model('common/orders')->getOrderScoreMoney($isUseScore,$useScore,$uId);
		//生成订单
		Db::startTrans();
		try{
			$orderunique = WSTOrderQnique();
			$carts = $carts['carts'];
			$orderNo = WSTOrderNo(); 
			$orderScore = 0;
			//创建订单
			$order = [];
			$order = array_merge($order,$address);
			$order['orderNo'] = $orderNo;
			$order['userId'] = $userId;
			$order['shopId'] = $carts['shopId'];
			$order['payType'] = $payType;
			$order['goodsMoney'] = $carts['goodsMoney'];
			//计算运费和总金额
			$order['deliverType'] = $deliverType;
			if($carts['isFreeShipping']){
                $order['deliverMoney'] = 0;
			}else{
			    $order['deliverMoney'] = ($deliverType==1)?0:WSTOrderFreight($carts['shopId'],$order['areaId2']);
			}
			$order['totalMoney'] = $order['goodsMoney']+$order['deliverMoney'];
            //积分支付-计算分配积分和金额
            $order['scoreMoney'] = 0;
			$order['useScore'] = 0;
			if($scoreMoney['useMoney']>0){
				$order['scoreMoney'] = $scoreMoney['useMoney'];
				$order['useScore'] = $scoreMoney['useScore'];
			}
			//实付金额要减去积分兑换的金额
			$order['realTotalMoney'] = $order['totalMoney'] - $order['scoreMoney'];
			$order['needPay'] = $order['realTotalMoney'];
			$order['orderCode'] = 'groupon';
			$order['orderCodeTargetId'] = $carts['grouponId'];
			$order['extraJson'] = json_encode(['grouponId'=>$carts['grouponId']]);
            if($payType==1){
                if($order['needPay']>0){
                    $order['orderStatus'] = -2;//待付款
				    $order['isPay'] = 0; 
                }else{
                    $order['orderStatus'] = 0;//待发货
				    $order['isPay'] = 1;
				    $order['payTime'] = date('Y-m-d H:i:s');  
                }
			}else{
				$order['orderStatus'] = 0;//待发货
				if($order['needPay']==0){
					$order['isPay'] = 1; 
					$order['payTime'] = date('Y-m-d H:i:s'); 
				}
			}
			//积分
			$orderScore = 0;
			//如果开启下单获取积分则有积分
			if(WSTConf('CONF.isOrderScore')==1){
				$orderScore = WSTMoneyGiftScore($order['goodsMoney']);
			}
			$order['orderScore'] = $orderScore;
			$order['isInvoice'] = $isInvoice;
			$order['invoiceJson'] = model('common/invoices')->getInviceInfo((int)input('param.invoiceId'));// 发票信息
			$order['invoiceClient'] = $invoiceClient;
			$order['orderRemarks'] = input('post.remark_'.$carts['shopId']);
			$order['orderunique'] = $orderunique;
			$order['orderSrc'] = $orderSrc;
			$order['dataFlag'] = 1;
			$order['payRand'] = 1;
			$order['createTime'] = date('Y-m-d H:i:s');
			$m = model('common/orders');

			$result = $m->data($order,true)->isUpdate(false)->allowField(true)->save($order);
			
			if(false !== $result){
				$orderId = $m->orderId;
				$goods = $carts['goods'];
				//创建订单商品记录
				$orderGgoods = [];
				$orderGoods['orderId'] = $orderId;
				$orderGoods['goodsId'] = $goods['goodsId'];
				$orderGoods['goodsNum'] = $goods['cartNum'];
				$orderGoods['goodsPrice'] = $goods['shopPrice'];
				$orderGoods['goodsSpecId'] = $goods['goodsSpecId'];
				if(!empty($goods['specNames'])){
					$specNams = [];
					foreach ($goods['specNames'] as $pkey =>$spec){
						$specNams[] = $spec['catName'].'：'.$spec['itemName'];
					}
					$orderGoods['goodsSpecNames'] = implode('@@_@@',$specNams);
				}else{
					$orderGoods['goodsSpecNames'] = '';
				}
				$orderGoods['goodsName'] = $goods['goodsName'];
				$orderGoods['goodsImg'] = $goods['goodsImg'];
				$orderGoods['commissionRate'] = WSTGoodsCommissionRate($goods['goodsCatId']);
				Db::name('order_goods')->insert($orderGoods);
                //计算订单佣金
				$commissionFee = 0;
				if((float)$orderGoods['commissionRate']>0){
					$commissionFee += round($orderGoods['goodsPrice']*$orderGoods['goodsNum']*$orderGoods['commissionRate']/100,2);
				}
				model('common/orders')->where('orderId',$orderId)->update(['commissionFee'=>$commissionFee]);

				//修改团购数量
				$this->where('grouponId',$carts['grouponId'])->setInc('orderNum',$goods['cartNum']);
				//创建积分流水--如果有抵扣积分就肯定是开启了支付支付
				if($order['useScore']>0){
					$score = [];
				    $score['userId'] = $userId;
					$score['score'] = $order['useScore'];
					$score['dataSrc'] = 1;
					$score['dataId'] = $orderId;
					$score['dataRemarks'] = "交易订单【".$orderNo."】使用积分".$order['useScore']."个";
					$score['scoreType'] = 0;
					model('common/UserScores')->add($score);
				}
                    
				//建立订单记录
				$logOrder = [];
				$logOrder['orderId'] = $orderId;
				$logOrder['orderStatus'] = ($payType==1 && $order['needPay']==0)?-2:$order['orderStatus'];
				$logOrder['logContent'] = ($payType==1)?"下单成功，等待用户支付":"下单成功";
				$logOrder['logUserId'] = $userId;
				$logOrder['logType'] = 0;
				$logOrder['logTime'] = date('Y-m-d H:i:s');
				Db::name('log_orders')->insert($logOrder);
				if($payType==1 && $order['needPay']==0){
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 0;
					$logOrder['logContent'] = "订单已支付，下单成功";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
				}
				//给店铺增加提示消息
				$tpl = WSTMsgTemplates('ORDER_SUBMIT');
		        if($tpl['tplContent']!='' && $tpl['status']=='1'){
		            $find = ['${ORDER_NO}'];
		            $replace = [$orderNo];
		           
		        	$msg = array();
		            $msg["shopId"] = $carts['shopId'];
		            $msg["tplCode"] = $tpl["tplCode"];
		            $msg["msgType"] = 1;
		            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
		            $msg["msgJson"] = ['from'=>1,'dataId'=>$orderId];
		            model("common/MessageQueues")->add($msg);
		        }
		        //判断是否需要发送管理员短信
	            $tpl = WSTMsgTemplates('PHONE_ADMIN_SUBMIT_ORDER');
	            if((int)WSTConf('CONF.smsOpen')==1 && (int)WSTConf('CONF.smsSubmitOrderTip')==1 && $tpl['tplContent']!='' && $tpl['status']=='1'){
				    $params = ['tpl'=>$tpl,'params'=>['ORDER_NO'=>$orderNo]];
				    $staffs = Db::name('staffs')->where([['staffId','in',explode(',',WSTConf('CONF.submitOrderTipUsers'))],['staffStatus','=',1],['dataFlag','=',1]])->field('staffPhone')->select();
				    for($i=0;$i<count($staffs);$i++){
						if($staffs[$i]['staffPhone']=='')continue;
						$m = new LogSms();
				        $rv = $m->sendAdminSMS(0,$staffs[$i]['staffPhone'],$params,'submit','');
				    }
	            }
		        //微信消息
		        if((int)WSTConf('CONF.wxenabled')==1){
		            $params = [];
		            $params['ORDER_NO'] = $orderNo;
	                $params['ORDER_TIME'] = date('Y-m-d H:i:s');             
	                $goodsNames = $goods['goodsName']."*".$goods['cartNum'];
		            $params['GOODS'] = $goodsNames;
		            $params['MONEY'] = $order['realTotalMoney'] + $order['scoreMoney'];
		            $params['ADDRESS'] = $order['userAddress']." ".$order['userName'];
		            $params['PAY_TYPE'] = WSTLangPayType($order['payType']);
			        
			        $msg = array();
					$tplCode = "WX_ORDER_SUBMIT";
					$msg["shopId"] = $carts['shopId'];
		            $msg["tplCode"] = $tplCode;
		            $msg["msgType"] = 4;
		            $msg["paramJson"] = ['CODE'=>$tplCode,'URL'=>Url('wechat/orders/sellerorder','',true,true),'params'=>$params];
		            $msg["msgJson"] = "";
		            model("common/MessageQueues")->add($msg);
			        //判断是否需要发送给管理员消息
		            if((int)WSTConf('CONF.wxSubmitOrderTip')==1){
		                $params = [];
			            $params['ORDER_NO'] = $orderNo;
		                $params['ORDER_TIME'] = date('Y-m-d H:i:s');             
		                $goodsNames = $goods['goodsName']."*".$goods['cartNum'];
			            $params['GOODS'] = $goodsNames;
			            $params['MONEY'] = $order['realTotalMoney'] + $order['scoreMoney'];
			            $params['ADDRESS'] = $order['userAddress']." ".$order['userName'];
			            $params['PAY_TYPE'] = WSTLangPayType($order['payType']);
			            WSTWxBatchMessage(['CODE'=>'WX_ADMIN_ORDER_SUBMIT','userType'=>3,'userId'=>explode(',',WSTConf('CONF.submitOrderTipUsers')),'params'=>$params]);
		            }
			    }
			}
			Db::commit();
			//删除session的购物车商品
			session('GROUPON_CARTS',null);
			return WSTReturn("提交订单成功", 1,$orderunique);
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('提交订单失败',-1);
        }
	}
   
    /**
	 * 下单
	 */
	public function submit($orderSrc = 0,$uId=0){
		//检测购物车
		$carts = $this->getCarts();
		if(empty($carts['carts']))return WSTReturn("请选择要购买的商品");
		//检测时间过了没有
		$time = time();
		if(strtotime($carts['carts']['goods']['startTime']) > $time)return WSTReturn('对不起，团购活动尚未开始');
		if(strtotime($carts['carts']['goods']['endTime']) < $time)return WSTReturn('很抱歉，您来晚了，团购活动已结束');
		$checkNum = $carts['carts']['goods']['goodsStock']-$carts['carts']['goods']['orderNum'];
		if($checkNum<$carts['goodsTotalNum'])return WSTReturn("团购商品失败，商品剩余库存为".$checkNum);
		if($carts['goodsType']==1){
            return $this->submitByVirtual($carts,$orderSrc,$uId);
		}else{
            return $this->submitByEntity($carts,$orderSrc,$uId);
		}
	}


	/**
	 * 管理员查看团购列表
	 */
	public function pageQueryByAdmin($grouponStatus){
		$goodsName = input('goodsName');
		$shopName = input('shopName');
		$areaIdPath = input('areaIdPath');
		$goodsCatIdPath = input('goodsCatIdPath');
		$where[] = ['gu.dataFlag','=',1];
		$where[] = ['grouponStatus','=',$grouponStatus];
		if($goodsName !='')$where[] = ['g.goodsName|g.goodsSn','like','%'.$goodsName.'%'];
		if($shopName !='')$where[] = ['s.shopName|s.shopSn','like','%'.$shopName.'%'];
		if($areaIdPath !='')$where[] = ['s.areaIdPath','like',$areaIdPath."%"];
		if($goodsCatIdPath !='')$where[] = ['g.goodsCatIdPath','like',$goodsCatIdPath."%"];
        $page =  $this->alias('gu')->join('__GOODS__ g','g.goodsId=gu.goodsId and g.isSale=1 and g.dataFlag=1','inner')
                      ->join('__SHOPS__ s','s.shopId=gu.shopId','left')
                      ->where($where)->order('gu.createTime desc')->field('g.goodsName,g.goodsSn,gu.*,g.goodsImg,s.shopId,s.shopName')
                      ->order('gu.updateTime desc')
                      ->paginate(input('limit/d'))->toArray();
        if(count($page['data'])>0){
        	$time = time();
        	foreach($page['data'] as $key =>$v){
        		$page['data'][$key]['goodsImg'] = WSTImg($v['goodsImg']);
        		$page['data'][$key]['verfiycode'] = WSTShopEncrypt($v['shopId']);
        		if(strtotime($v['startTime'])<=$time && strtotime($v['endTime'])>=$time){
        			$page['data'][$key]['status'] = 1; 
        		}else if(strtotime($v['startTime'])>$time){
                    $page['data'][$key]['status'] = 0; 
        		}else{
        			$page['data'][$key]['status'] = -1; 
        		}
        	}
        }
        return $page;
	}

	/**
	* 设置商品违规状态
	*/
	public function illegal(){
		$illegalRemarks = input('post.illegalRemarks');		
		$id = (int)input('post.id');
		if($illegalRemarks=='')return WSTReturn("请输入违规原因");
		//判断商品状态
		$rs = $this->alias('gu')
		           ->join('__GOODS__ g','gu.goodsId=g.goodsId','inner')
		           ->join('__SHOPS__ s','g.shopId=s.shopId','left')
		           ->where('grouponId',$id)
		           ->field('gu.grouponId,g.shopId,s.userId,g.goodsName,g.goodsSn,gu.grouponStatus,g.goodsId')->find();
		if((int)$rs['grouponId']==0)return WSTReturn("无效的商品");
		if((int)$rs['grouponStatus']==-1)return WSTReturn("操作失败，商品状态已发生改变，请刷新后再尝试");
		Db::startTrans();
		try{
			$res = $this->where('grouponId',$id)->update(['grouponStatus'=>-1,'illegalRemarks'=>$illegalRemarks]);
			if($res!==false){
				//发送一条商家信息
				$tpl = WSTMsgTemplates('GROUPON_GOODS_REJECT');
		        if($tpl['tplContent']!='' && $tpl['status']=='1'){
		            $find = ['${GOODS}','${GOODS_SN}','${TIME}','${REASON}'];
		            $replace = [$rs['goodsName'],$rs['goodsSn'],date('Y-m-d H:i:s'),$illegalRemarks];
		           
		            $msg = array();
		            $msg["shopId"] = $rs['shopId'];
		            $msg["tplCode"] = $tpl["tplCode"];
		            $msg["msgType"] = 1;
		            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
		            $msg["msgJson"] = ['from'=>7,'dataId'=>$id];
		            model("common/MessageQueues")->add($msg);
		        } 
		        if((int)WSTConf('CONF.wxenabled')==1){
					$params = [];
					$params['GOODS'] = $rs['goodsName'];
					$params['GOODS_SN'] = $rs['goodsSn'];
					$params['TIME'] = date('Y-m-d H:i:s'); 
					$params['REASON'] = $illegalRemarks;          
					
					$msg = array();
					$tplCode = "WX_GROUPON_GOODS_REJECT";
					$msg["shopId"] = $rs['shopId'];
		            $msg["tplCode"] = $tplCode;
		            $msg["msgType"] = 4;
		            $msg["paramJson"] = ['CODE'=>$tplCode,'params'=>$params];
		            $msg["msgJson"] = "";
		            model("common/MessageQueues")->add($msg);
				}
				Db::commit();
				return WSTReturn('操作成功',1);
			}
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('操作失败',-1);
	}
   /**
	* 通过商品审核
	*/
	public function allow(){	
		$id = (int)input('post.id');
		//判断商品状态
		$rs = $this->alias('gu')
		           ->join('__GOODS__ g','gu.goodsId=g.goodsId','inner')
		           ->join('__SHOPS__ s','g.shopId=s.shopId','left')
		           ->where('grouponId',$id)
		           ->field('gu.grouponId,g.shopId,s.userId,g.goodsName,g.goodsSn,gu.grouponStatus,g.goodsId')->find();
		if((int)$rs['grouponId']==0)return WSTReturn("无效的商品");
		if((int)$rs['grouponStatus']!=0)return WSTReturn("操作失败，商品状态已发生改变，请刷新后再尝试");
		Db::startTrans();
		try{
			$res = $this->where('grouponId',$id)->update(['grouponStatus'=>1]);
			if($res!==false){
				//发送一条商家信息
				$tpl = WSTMsgTemplates('GROUPON_GOODS_ALLOW');
		        if($tpl['tplContent']!='' && $tpl['status']=='1'){
		            $find = ['${GOODS}','${GOODS_SN}','${TIME}'];
		            $replace = [$rs['goodsName'],$rs['goodsSn'],date('Y-m-d H:i:s')];
		           
		            $msg = array();
		            $msg["shopId"] = $rs['shopId'];
		            $msg["tplCode"] = $tpl["tplCode"];
		            $msg["msgType"] = 1;
		            $msg["content"] = str_replace($find,$replace,$tpl['tplContent']);
		            $msg["msgJson"] = ['from'=>7,'dataId'=>$id];
		            model("common/MessageQueues")->add($msg);
		        } 
		        if((int)WSTConf('CONF.wxenabled')==1){
					$params = [];
					$params['GOODS'] = $rs['goodsName'];
					$params['GOODS_SN'] = $rs['goodsSn'];
					$params['TIME'] = date('Y-m-d H:i:s');          
					
					$msg = array();
					$tplCode = "WX_GROUPON_GOODS_ALLOW";
					$msg["shopId"] = $rs['shopId'];
		            $msg["tplCode"] = $tplCode;
		            $msg["msgType"] = 4;
		            $msg["paramJson"] = ['CODE'=>$tplCode,'params'=>$params];
		            $msg["msgJson"] = "";
		            model("common/MessageQueues")->add($msg);
				}
				Db::commit();
				return WSTReturn('操作成功',1);
			}
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('操作失败',-1);
	}

    /**
	 * 删除团购
	 */
	public function delByAdmin(){
		$id = (int)input('id');
		$data = [];
		$data['grouponId'] = $id;
        $rs = $this->update(['dataFlag'=>-1],$data);
        return WSTReturn('删除成功',1);
	}

	/**
	 * 查询商品订单
	 */
	public function pageQueryByGoods(){
	    $grouponId = (int)input('grouponId');
		$orderNo = input('post.orderNo');
		$payType = (int)input('post.payType');
		$deliverType = (int)input('post.deliverType');
		$shopId = (int)session('WST_USER.shopId');
		$where[] = ['shopId','=',$shopId];
		$where[] = ['dataFlag','=',1];
		$where[] = ['orderCode','=','groupon'];
		if($grouponId>0)$where[] = ['orderCodeTargetId','=',$grouponId];
		if($orderNo!=''){
			$where[] = ['orderNo','like',"%$orderNo%"];
		}
		if($payType > -1){
			$where[] = ['payType','=',$payType];
		}
		if($deliverType > -1){
			$where[] = ['deliverType','=',$deliverType];
		}
		$page = Db::name('orders')->alias('o')->where($where)
		      ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
		      ->field('o.orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,orderStatus,deliverType,deliverMoney,isAppraise,isRefund
		              ,payType,payFrom,userAddress,orderStatus,isPay,isAppraise,userName,orderSrc,o.createTime,orf.id refundId')
			  ->order('o.createTime', 'desc')
			  ->paginate()->toArray();
	    if(count($page['data'])>0){
	    	 $orderIds = [];
	    	 foreach ($page['data'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = Db::name('order_goods')->where([['orderId','in',$orderIds]])->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	 $v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	 $goodsMap[$v['orderId']][] = $v;
	    	 }
	    	 foreach ($page['data'] as $key => $v){
	    	 	 $page['data'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['data'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['data'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['data'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return WSTReturn('',1,$page);
	}

}

