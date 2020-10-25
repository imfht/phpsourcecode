<?php
namespace wstmart\common\model;
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
 * 标签业务处理类
 */
class Tags extends Base{
	/**
	* 单数据库查询
	*/
	public function wstDb($table,$where,$order,$field,$num,$cache){
		$cacheData = cache('TAG_GOODS_'.$table."_".$field."_".$num."_".$cache);
		if($cacheData)return $cacheData;
		$goods = model($table)->field($field)
							  ->where($where)
							  ->order($order)
							  ->limit($num)
							  ->select();
		cache('TAG_GOODS_'.$table."_".$field."_".$num."_".$cache,$goods,$cache);
		return $goods;

	}
	/**
	 * 获取指定商品
	 */
	public function listGoods($type,$catId = 0,$num,$cache = 0){
		$type = strtolower($type);
		if(strtolower($type)=='history'){
			return $this->historyByGoods($num);
		}elseif(strtolower($type)=='guess'){
			return $this->getGuessLike($catId,$num);
		}else{
			return $this->listByGoods($type,$catId,$num,$cache);
		}
	}
	/**
	* 猜你喜欢
	* @param catId:分类id
	* @param num:数据条数
	*/
	public function getGuessLike($catId,$num,$goodsIds=[]){
		$module = request()->module();
		$ids = ($module=='home')?cookie("history_goods"):cookie("wx_history_goods");
		if($module=='app'){
			$ids = $goodsIds;
		}else{
			$ids = ($module=='home')?cookie("history_goods"):cookie("wx_history_goods");
		}
		// 当前无浏览记录，取热销商品
	    $where = [];
	    $where[] = ['isSale','=',1];
	    $where[] = ['goodsStatus','=',1]; 
	    $where[] = ['dataFlag','=',1]; 
	    $where[] = ['goodsId','in',$ids];
        $goods_group = Db::name('goods')
	                   ->where($where)
	                   ->group('goodsCatId')
	                   ->column('goodsCatId'); 
	    $goods = Db::name('goods')->field('goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum,isNew')
	    						  ->where($where)
	    						  ->where([['goodsCatId','in',$goods_group]])
	    						  ->limit(3*$num)
	    						  ->select();
	    if(empty($goods))return $this->listByGoods('hot',$catId,$num);

	    // 从数组中随机取$num个单元
	    shuffle($goods);
	    $goods = array_slice($goods,0,$num);
        return $goods;
	}
	/**
	 * 浏览商品
	 */
	public function historyByGoods($num){
		$hids = $ids = cookie("history_goods");
		if(empty($ids))return [];
	    $where = [];
	    $where[] = ['isSale','=',1];
	    $where[] = ['goodsStatus','=',1]; 
	    $where[] = ['g.dataFlag','=',1]; 
	    $where[] = ['goodsId','in',$ids];
	    $orderBy = "field(`goodsId`,".implode(',',$ids).")";
        $goods = Db::name('goods')->alias('g')->join('__SHOPS__ s','g.shopId=s.shopId')
                   ->where($where)->field('s.shopName,s.shopId,goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum')
                   ->limit($num)
                   ->orderRaw($orderBy)
                   ->select(); 
        $ids = [];
        foreach($goods as $key =>$v){
        	if($v['isSpec']==1)$ids[] = $v['goodsId'];
        }
        if(!empty($ids)){
        	$specs = [];
        	$rs = Db::name('goods_specs gs ')->where([['goodsId','in',$ids],['dataFlag','=',1]])->order('id')->select();
        	foreach ($rs as $key => $v){
        		$specs[$v['goodsId']] = $v;
        	}
        	foreach($goods as $key =>$v){
        		if(isset($specs[$v['goodsId']]))
        		$goods[$key]['specs'] = $specs[$v['goodsId']];
        	}
        }
        return $goods;
	}
	/**
	 * 推荐商品
	 */
	public function listByGoods($type,$catId,$num,$cache = 0){
		if(!in_array($type,[0,1,2,3]))return [];
		$cacheData = cache('TAG_GOODS_'.$type."_".$catId."_".$num."_".$cache);
		if($cacheData)return $cacheData;
		//检测是否有数据
		$types = ['recom'=>0,'new'=>3,'hot'=>1,'best'=>2];
        $where = [];
        $where[] = ['r.dataSrc','=',0];
        $where[] = ['g.isSale','=',1];
        $where[] = ['g.goodsStatus','=',1]; 
        $where[] = ['g.dataFlag','=',1]; 
        $goods=[];
        if($type!='visit'){
	        $where[] = ['r.dataType','=',$types[$type]];
	        $where[] = ['r.goodsCatId','=',(int)$catId];
	        $goods = Db::name('goods')->alias('g')->join('__RECOMMENDS__ r','g.goodsId=r.dataId')
	                   ->join('__SHOPS__ s','g.shopId=s.shopId')
	                   ->where($where)->field('g.goodsTips,s.shopName,s.shopId,g.goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum,isNew')
	                   ->order('r.dataSort asc')->limit($num)->select();
        }
        //判断有没有设置，如果没有设置的话则获取实际的数据
	    if(empty($goods)){
	    	$goodsCatIds = WSTGoodsCatPath($catId);
	    	$types = ['recom'=>'isRecom','new'=>'isNew','hot'=>'isHot','best'=>'isBest'];
	    	$order = ['recom'=>'saleNum desc,goodsId asc',
	    			  'new'=>'saleTime desc,goodsId asc',
	    			  'hot'=>'saleNum desc,goodsId asc',
	    			  'best'=>'saleNum desc,goodsId asc',
	    			  'visit'=>'visitNum desc'
	    			 ];

	    	$where = [];
	        $where[] = ['isSale','=',1];
	        $where[] = ['goodsStatus','=',1]; 
	        $where[] = ['g.dataFlag','=',1]; 

	        if($type!='visit')
	        $where[] = [$types[$type],'=',1];



	        if(!empty($goodsCatIds))$where[] = ['g.goodsCatIdPath','like',implode('_',$goodsCatIds).'_%'];
        	$goods = Db::name('goods')->alias('g')->join('__SHOPS__ s','g.shopId=s.shopId')
                   ->where($where)->field('g.goodsTips,s.shopName,s.shopId,goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum,isNew')
                   ->order($order[$type])->limit($num)->select();
        }   
        $ids = [];
        foreach($goods as $key =>$v){
        	if($v['isSpec']==1)$ids[] = $v['goodsId'];
        }
        if(!empty($ids)){
        	$specs = [];
        	$rs = Db::name('goods_specs gs ')->where([['goodsId','in',$ids],['dataFlag','=',1]])->order('id asc')->select();
        	foreach ($rs as $key => $v){
        		$specs[$v['goodsId']] = $v;
        	}
        	foreach($goods as $key =>$v){
        		if(isset($specs[$v['goodsId']]))
        		$goods[$key]['specs'] = $specs[$v['goodsId']];
        	}
        }
        cache('TAG_GOODS_'.$type."_".$catId."_".$num."_".$cache,$goods,$cache);
        return $goods;
	}
	
	/**
	 * 获取广告位置
	 */
	public function listAds($positionCode,$num,$cache = 0){
		$cacheData = cache('TAG_ADS'.$positionCode."_".$cache);
		if($cacheData)return $cacheData;
		$today = date('Y-m-d');
		$rs = Db::name("ads")->alias('a')->join('__AD_POSITIONS__ ap','a.adPositionId= ap.positionId and ap.dataFlag=1','left')
		          ->where("a.dataFlag=1 and ap.positionCode='".$positionCode."' and adStartDate<= '$today' and adEndDate>='$today'")
		          ->field('adId,adName,adURL,subTitle,adFile,positionWidth,positionHeight')
		          ->order('adSort')->limit($num)->select();
		if(count($rs)>0){
			foreach ($rs as $key => $v) {
				 $rs[$key]['isOpen'] = false;
				if(stripos($v['adURL'],'http:')!== false || stripos($v['adURL'],'https:')!== false){
                     $rs[$key]['isOpen'] = true;
				}
			}
		}
		cache('TAG_ADS'.$positionCode."_".$cache,$rs,$cache);
		return $rs;
	}
	
	/**
	 * 获取友情链接
	 */
	public function listFriendlink($num,$cache = 0){
		$cacheData = cache('TAG_FRIENDLINK_'.$cache);
		if($cacheData)return $cacheData;
		$rs = Db::name("friendlinks")->where(["dataFlag"=>1])->order("friendlinkSort asc")->select();
		cache('TAG_FRIENDLINK_'.$cache,$rs,$cache);
	    return $rs;
	}
	
    /**
	 * 获取文章列表
	 */
	public function listArticle($catId,$num,$cache = 0){
		$cacheData = cache('TAG_ARTICLES_'.$catId."_".$num."_".$cache);
		if($cacheData)return $cacheData;
		$rs = [];
		if($catId=='new'){
			$rs = $this->listByNewArticle($num,$cache);
		}else{
			$rs = $this->listByArticle($catId,$num,$cache);
		}
		cache('TAG_ARTICLES_'.$catId."_".$num."_".$cache,$rs,$cache);
		return $rs;
	}
    /**
	 * 获取最新文章
	 */
	public function listByNewArticle($num,$cache){
		$cacheData = cache('TAG_NEW_ARTICLES_'.$cache);
		if($cacheData)return $cacheData;
		$rs = Db::name('articles')->alias('a')->field('a.articleId,a.articleTitle,a.coverImg,a.createTime')->join('article_cats ac','a.catId=ac.catId','inner')
		            ->where('ac.catType=0 and ac.parentId<>7 and a.dataFlag=1 and ac.isShow=1 and a.isShow=1 and ac.dataFlag=1')->order('a.catSort asc,a.createTime desc')->limit($num)->select();
		cache('TAG_NEW_ARTICLES_'.$cache,$rs,$cache);
	    return $rs;
	}
	/**
	 * 获取指定分类的文章
	 */
	public function listByArticle($catId,$num,$cache){
		$where = [];
		$where[] = ['dataFlag','=',1];
		$where[] = ['isShow','=',1];
		if(is_array($catId)){
		    $where[] = ['catId','in',$catId];
		}else{
			$where[] = ['catId','=',$catId];
		}
		return Db::name('articles')->where($where)
		         ->field("articleId, catId, articleTitle,articleContent,coverImg,createTime")->order('catSort asc,createTime desc')->limit($num)->select(); 
	}

	/**
	* 获取分类下的品牌
	*/
	public function listBrand($catId,$num,$cache = 0){
		$cacheData = cache('TAG_BRANDS_'.$catId."_".$cache);
		if($cacheData)return $cacheData;
        $where = [];
        $where[] = ['r.dataSrc','=',2];
        $where[] = ['b.dataFlag','=',1]; 
        $where[] = ['r.dataType','=',0];
	    $where[] = ['r.goodsCatId','=',$catId];
	    $brands = Db::name('brands')->alias('b')->join('__RECOMMENDS__ r','b.brandId=r.dataId')
	                   ->where($where)->field('b.brandId,b.brandImg,b.brandName,r.goodsCatId catId')
	                   ->order('r.dataSort')->limit($num)->select();
        //为空的话就取分类关联的
        if(empty($brands)){
        	$where = [['b.dataFlag','=',1]];
        	if($catId>0){
        	 	$where[] = ['gc.catId','=',$catId];
		        $brands = Db::name('goods_cats')->alias('gc')
						   ->join('__CAT_BRANDS__ gcb','gc.catId=gcb.catId','inner')
						   ->join('__BRANDS__ b','gcb.brandId=b.brandId and b.dataFlag=1','inner')
						   ->field('b.brandId,b.brandImg,b.brandName,gcb.catId')
						   ->group('b.brandId,gcb.catId')
						   ->where('gc.dataFlag=1 and gc.isShow=1')
						   ->where($where)
						   ->limit($num)
						   ->select();
			}else{
                $brands = Db::name('brands')->field('brandId,brandImg,brandName,0 catId')->where(['dataFlag'=>1])->order('sortNo asc')->limit($num)->select();
			}
		}
		$brands = $this->unique_multidim_array($brands,'brandId');
        cache('TAG_BRANDS_'.$catId."_".$cache,$brands,$cache);
        return $brands;
	}
	// 二位数组去重
	protected function unique_multidim_array($array, $key) { 
	    $temp_array = array(); 
	    $i = 0; 
	    $key_array = array(); 
	    foreach($array as $val) { 
	        if (!in_array($val[$key], $key_array)) { 
	            $key_array[$i] = $val[$key]; 
	            $temp_array[$i] = $val; 
	        } 
	        $i++; 
	    } 
	    return $temp_array; 
	} 

	/**
	* 获取分类下的店铺
	*/
	public function listShop($catId,$num,$cache = 0){
		$cacheData = cache('TAG_SHOPS_'.$catId."_".$cache);
		if($cacheData)return $cacheData;
        $where = [];
        $where[] = ['r.dataSrc','=',1];
        $where[] = ['b.dataFlag','=',1];
        $where[] = ['b.applyStatus','=',2];
        $where[] = ['r.dataType','=',0];
	    $where[] = ['r.goodsCatId','=',$catId];
	    $shops = Db::name('shops')->alias('b')->join('__RECOMMENDS__ r','b.shopId=r.dataId')
	                   ->join('__SHOP_CONFIGS__ sc','b.shopId=sc.shopId','inner')
	                   ->where($where)->field('b.shopId,b.shopImg,b.shopName,r.goodsCatId catId,sc.shopStreetImg')
	                   ->order('r.dataSort','asc')->limit($num)->select();
        //为空的话就取分类关联的
        if(empty($shops) && $catId>0){
	         $shops = Db::name('goods_cats')->alias('gc')
					   ->join('__CAT_SHOPS__ gcb','gc.catId=gcb.catId','inner')
					   ->join('__SHOPS__ b','gcb.shopId=b.shopId and b.shopStatus=1 and b.dataFlag=1','inner')
					   ->join('__SHOP_CONFIGS__ sc','b.shopId=sc.shopId','inner')
					   ->field('b.shopId,b.shopImg,b.shopName,gcb.catId,sc.shopStreetImg')
					   ->where('gc.dataFlag=1 and gc.isShow=1 and gc.catId='.$catId)
					   ->limit($num)
					   ->select();
		}
        cache('TAG_SHOPS_'.$catId."_".$cache,$shops,$cache);
        return $shops;
	}

	/**
	 * 获取订单列表
	 */
	public function listOrder($type,$num,$cache,$fields = ''){
		if(!in_array($type,['user','shop']))return [];
		$ownId = (int)($type=='user')?session('WST_USER.userId'):session('WST_USER.shopId');
		if($ownId==0)return [];
		if($fields=='')$fields = 'orderId,orderNo,createTime,orderStatus,payType,deliverType,userName,realTotalMoney';
        $data = cache('TAG_ORDER_'.$type."_".$ownId."_".$cache);
        if(!$data){
        	$where = '';
        	if($type=='user')$where = 'userId='.$ownId;
        	if($type=='shop')$where = 'shopId='.$ownId;
            $db = Db::name('orders')->where($where)->limit($num)->order('createTime','desc');
            if($fields!='')$db->field($fields);
            $data =$db->select();
            if(!empty($data)){
            	$ids = [];
            	foreach ($data as $key => $v) {
            		$ids[] = $v['orderId'];
            	}
            	$goods = Db::name('order_goods')->where('orderId in ('.implode(',',$ids).')')->order('id','asc')->select();
                $goodsMap = [];
                foreach($goods as $g){
                    $goodsMap[$g['orderId']][] = $g;
                }
                foreach ($data as $key => $v) {
            		$data[$key]['goods'] = $goodsMap[$v['orderId']];
            	}
            }
            cache('TAG_ORDER_'.$type."_".$ownId."_".$cache,$data,$cache);
        }
        return $data;
	}

	/**
	 * 获取收藏商品/商家列表
	 */
	public function listFavorite($type,$num,$fields = ''){
		if(!in_array($type,['goods','shop']))return [];
		$userId = (int)session('WST_USER.userId');
		if($userId==0)return [];
    	$where = 'userId='.$userId;
    	$db = Db::name('favorites')->alias('f');
        if($type=='goods'){
           $db->join('__GOODS__ g','f.favoriteType=0 and f.targetId=g.goodsId and g.dataFlag=1 ');
           if($fields=='')$fields = 'g.goodsId,g.goodsName,g.goodsImg,g.isSale,g.shopPrice';
           $db->field($fields);
        }else{
           $db->join('__SHOPS__ s','f.favoriteType=1 and f.targetId=s.shopId and s.dataFlag=1 and s.shopStatus=1');
           if($fields=='')$fields = 's.shopName,s.shopId,s.shopImg';
           $db->field($fields);
        }
        $db->limit($num)->where($where);
        $db->order('favoriteId desc');
        $data = $db->select();
        return $data;
	}
	
	/**
	 * 获取搜索关键词
	 */
	public function listSearchkey($type,$cache = 0){
		$cacheData = cache('TAG_SEARCHKEY_'.$type."_".$cache);
		if($cacheData)return $cacheData;
		$keys = WSTConf("CONF.hotWordsSearch");
		if($keys!=''){
			$keys = explode(',',$keys);
			if($type==1){
				foreach ($keys as $key => $v){
					$keys[$key] = [];
					$keys[$key]['name'] = $v;
					$where = [];
					$where[] = ['dataFlag','=',1];
					$where[] = ['isSale','=',1];
					$where[] = ['goodsName','like','%'.$v.'%'];
					$keys[$key]['count'] = Db::name('goods')->where($where)->count();
				}
			}
		}
		cache('TAG_SEARCHKEY_'.$type."_".$cache,$keys,$cache);
		return $keys;
	}
	
	/**
	 * 获取高评分商品
	 */
	public function listScore($catId,$num,$cache = 0){
		$cacheData = cache('TAG_SCORE_'.$catId."_".$cache);
		if($cacheData)return $cacheData;
		$scores = WSTConf("CONF.hotWordsSearch");
		if($scores!=''){
			$where = [];
			$where[] = ['serviceScore','>=',4];
			$where[] = ['g.dataFlag','=',1];
			$where[] = ['ga.dataFlag','=',1];
			$where[] = ['goodsScore','>=',4];
			$where[] = ['timeScore','>=',4];
			if($catId>0)$where[] = ['g.goodsCatIdPath','like',$catId."_%"];
			$scores = Db::name('goods')->alias('g')
			->field('g.goodsId,g.goodsImg,g.goodsName,g.shopPrice,ga.content,u.loginName,u.userName')
			->join('__GOODS_APPRAISES__ ga','g.goodsId=ga.goodsId','inner')
			->join('__USERS__ u','u.userId=ga.userId','inner')
			->where($where)
			->order('ga.createTime desc')
			->limit($num)
			->select();
		}
		cache('TAG_SCORE_'.$catId."_".$cache,$scores,$cache);
		return $scores;
	}
	
	/**
	 * 获取店铺分类列表
	 */
	public function listShopCats($parentId=0,$num,$shopId = 0,$cache = 0){
		if($shopId==0)return [];
		$cacheData = cache('TAG_SHOP_CATS_'.$shopId."_".$parentId."_".$num."_".$cache);
		if($cacheData)return $cacheData;
		$where = [];
		$where[] = ['isShow','=',1];
		$where[] = ['dataFlag','=',1];
		$where[] = ['shopId','=',$shopId];
		$where[] = ['parentId','=',$parentId];
		$data = Db::name('shop_cats')
		->field('catId,shopId,catName')
		->limit($num)->where($where)
		->order('catSort asc')->select();
		cache('TAG_SHOP_CATS_'.$shopId."_".$parentId."_".$num."_".$cache,$data,$cache,'CACHETAG_'.$shopId);
		return $data;
	}

	/**
	 * 获取指定店铺商品
	 */
	public function listShopGoods($type,$shopId,$cat = 0,$num = 0,$cache = 0){
		$cacheData = cache('TAG_SHOP_GOODS_'.$type."_".$shopId."_".$cat."_".$num."_".$cache);
		if($cacheData)return $cacheData;
	    $types = ['recom'=>'isRecom','new'=>'isNew','hot'=>'isHot','best'=>'isBest'];
	    $order = ['recom'=>'saleNum desc,goodsId asc','new'=>'saleTime desc,goodsId asc','hot'=>'saleNum desc,goodsId asc','best'=>'saleNum desc,goodsId asc'];
	    if(!isset($types[$type]))return [];
	    $where = [];
	    if($cat>0){
		    $parentId = Db::name('shop_cats')->where(['catId'=>$cat,'shopId'=>$shopId,'dataFlag'=>1,'isShow'=>1])->value('parentId');
	        if($parentId>0){
                 $where[] = ['shopCatId2','=',$cat];
	        }else{
                 $where[] = ['shopCatId1','=',$cat];
	        }
	    }
	    $where[] = ['shopId','=',$shopId];
	    $where[] = ['isSale','=',1];
	    $where[] = ['goodsStatus','=',1]; 
	    $where[] = ['dataFlag','=',1]; 
	    $where[] = [$types[$type],'=',1];
        $goods = Db::name('goods')
                   ->where($where)->field('goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,appraiseNum,visitNum')
                   ->order($order[$type])->limit($num)->select();       
        $ids = [];
        foreach($goods as $key =>$v){
        	if($v['isSpec']==1)$ids[] = $v['goodsId'];
        }
        if(!empty($ids)){
        	$specs = [];
        	$rs = Db::name('goods_specs gs ')->where([['goodsId','in',$ids],['dataFlag','=',1]])->order('id')->select();
        	foreach ($rs as $key => $v){
        		$specs[$v['goodsId']] = $v;
        	}
        	foreach($goods as $key =>$v){
        		if(isset($specs[$v['goodsId']]))
        		$goods[$key]['specs'] = $specs[$v['goodsId']];
        	}
        }
        cache('TAG_SHOP_GOODS_'.$type."_".$shopId."_".$cat."_".$num."_".$cache,$goods,$cache,'CACHETAG_'.$shopId);
        return $goods;
	}
}
