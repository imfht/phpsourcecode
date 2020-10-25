<?php
namespace wstmart\home\model;
use wstmart\common\model\Goods as CGoods;
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
 * 商品类
 */
class Goods extends CGoods{
	/**
	 * 获取商品资料在前台展示
	 */
     public function getBySale($goodsId){
     	$key = input('key');
     	// 浏览量
     	$this->where('goodsId',$goodsId)->setInc('visitNum',1);
		$rs = Db::name('goods')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->find();
		if(!empty($rs)){
			$rs['read'] = false;
			$rs['goodsDesc'] = htmlspecialchars_decode($rs['goodsDesc']);
			$rs['goodsDesc'] = str_replace('${DOMAIN}',WSTConf('CONF.resourcePath'),$rs['goodsDesc']);
			//判断是否可以公开查看
			$viKey = WSTShopEncrypt($rs['shopId']);
			if(($rs['isSale']==0 || $rs['goodsStatus']==0) && $viKey != $key)return [];
			if($key!='')$rs['read'] = true;
			//获取店铺信息
			$rs['shop'] = model('shops')->getShopInfo((int)$rs['shopId']);
			if(empty($rs['shop']))return [];
			$gallery = [];
			$gallery[] = $rs['goodsImg'];
			if($rs['gallery']!=''){
				$tmp = explode(',',$rs['gallery']);
				$gallery = array_merge($gallery,$tmp);
			}
			$rs['gallery'] = $gallery;
			if($rs['isSpec']==1){
				//获取规格值
				$specs = Db::name('spec_cats')->alias('gc')->join('__SPEC_ITEMS__ sit','gc.catId=sit.catId','inner')
				                      ->where(['sit.goodsId'=>$goodsId,'gc.isShow'=>1,'sit.dataFlag'=>1])
				                      ->field('gc.isAllowImg,gc.catName,sit.catId,sit.itemId,sit.itemName,sit.itemImg')
				                      ->order('gc.isAllowImg desc,gc.catSort asc,gc.catId asc')->select();                     
				foreach ($specs as $key =>$v){
					$rs['spec'][$v['catId']]['name'] = $v['catName'];
					$rs['spec'][$v['catId']]['list'][] = $v;
				}

				//获取销售规格
				$sales = Db::name('goods_specs')->where('goodsId',$goodsId)->field('id,isDefault,productNo,specIds,marketPrice,specPrice,specStock')->select();
				if(!empty($sales)){
					foreach ($sales as $key =>$v){
						$str = explode(':',$v['specIds']);
						sort($str);
						unset($v['specIds']);
						$rs['saleSpec'][implode(':',$str)] = $v;
						if($v['isDefault']==1)$rs['defaultSpecs'] = $v;
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
			//品牌名称
			$rs['brandName'] = Db::name('brands')->where(['brandId'=>$rs['brandId']])->value('brandName');
			//关注
			$f = model('Favorites');
			$rs['favShop'] = $rs['shop']['isfollow'];
			$rs['favGood'] = $f->checkFavorite($goodsId,0);
		}
		return $rs;
	}
	
	
    /**
     * 获取符合筛选条件的商品ID
     */
    public function filterByAttributes(){
    	$vs = input('vs');
    	if($vs=='')return [];
    	$vs = explode(',',$vs);
    	$goodsIds = [];
    	$prefix = config('database.prefix');
		//循环遍历每个属性相关的商品ID
	    foreach ($vs as $v){
	    	$goodsIds2 = [];
	    	$attrVal = input('v_'.(int)$v);
	    	if($attrVal=='')continue;
	    	if(stristr($attrVal,'、')!==false){
	    		// 同属性多选
	    		$attrArr = explode('、',$attrVal);
	    		foreach($attrArr as $v1){
	    			$sql = "select goodsId from ".$prefix."goods_attributes where attrId=".(int)$v." and find_in_set('".$v1."',attrVal) ";
					$rs = Db::query($sql);
					if(!empty($rs)){
						foreach ($rs as $vg){
							$goodsIds2[] = $vg['goodsId'];
						}
					}
	    		}
	    	}else{
		    	$sql = "select goodsId from ".$prefix."goods_attributes 
		    	where attrId=".(int)$v." and find_in_set('".$attrVal."',attrVal) ";
				$rs = Db::query($sql);
				if(!empty($rs)){
					foreach ($rs as $vg){
						$goodsIds2[] = $vg['goodsId'];
					}
				}
	    	}
			//如果有一个属性是没有商品的话就不需要查了
			if(empty($goodsIds2))return [-1];
			//第一次比较就先过滤，第二次以后的就找集合
			$goodsIds2[] = -1;
			if(empty($goodsIds)){
				$goodsIds = $goodsIds2;
			}else{
				$goodsIds = array_intersect($goodsIds,$goodsIds2);
			}
		}
		return $goodsIds;
    }
	
	/**
	 * 获取分页商品记录
	 */
	public function pageQuery($goodsCatIds = []){
		//查询条件
		$isStock = input('isStock/d');
		$isNew = input('isNew/d');
		$isFreeShipping = input('isFreeShipping/d');
		$keyword = input('keyword');
		$where = $where2 = [];
		$where[] = ['goodsStatus','=',1];
		$where[] = ['g.dataFlag','=',1];
		$where[] = ['isSale','=',1];
        $recommendWhere = [];
        $recommendOrder = [];
        if($keyword!=''){
            $where2 = $this->getKeyWords($keyword);
            $recommendGoodsId = [];
            hook('userSearchWordsCondition',['recommendGoodsId'=>&$recommendGoodsId,'keyword'=>$keyword]);
            if($recommendGoodsId!=''){
                foreach($recommendGoodsId as $v){
                    $recommendWhere[] = "find_in_set(g.goodsId,'".$v."')";
                    $recommendOrder[] = "find_in_set(g.goodsId,'".$v."') desc";
                }
            }
        }
		//属性筛选
		$goodsIds = $this->filterByAttributes();
		if(!empty($goodsIds))$where[] = ['goodsId','in',$goodsIds];
		// 品牌筛选
		$brandIds = input('param.brand');
		if(!empty($brandIds)){
			$brandIds = explode(',',$brandIds);
			$where[] = ['brandId','in',$brandIds];
		}

		// 发货地
		$areaId = (int)input('areaId');
		if($areaId>0)$where[] = ['areaId','=',$areaId];
		//排序条件
		$orderBy = input('orderBy/d',0);
		$orderBy = ($orderBy>=0 && $orderBy<=4)?$orderBy:0;
		$order = (input('order/d',1)==1)?1:0;
		$pageBy = ['saleNum','shopPrice','appraiseNum','visitNum','saleTime'];
		$pageOrder = ['asc','desc'];
		if($isStock==1)$where[] = ['goodsStock','>',0];
		if($isNew==1)$where[] = ['isNew','=',1];
		if($isFreeShipping==1)$where[] = ['isFreeShipping','=',1];
		$condition = '';
        if(!empty($goodsCatIds)){
            $str = implode('_',$goodsCatIds).'_%';
            $condition = "goodsCatIdPath like '$str'";
        }
	    $minPrice = input("param.minPrice");//开始价格
	    $maxPrice = input("param.maxPrice");//结束价格
		if($minPrice!='' && $maxPrice!=''){
	    	$where[] = ['g.shopPrice','between',[(int)$minPrice,(int)$maxPrice]];
	    }elseif($minPrice!=''){
	    	$where[] = ['g.shopPrice','>=',(int)$minPrice];
		}elseif($maxPrice!=''){
			$where[] = ['g.shopPrice','<=',(int)$maxPrice];
		}
		$list = Db::name("goods")->alias('g')->join("__SHOPS__ s","g.shopId = s.shopId")
			->where($where)->where($where2)
            ->where($condition);
        foreach($recommendWhere as $v){
            $list->whereOrRaw($v);
        }
        $list = $list->field('goodsId,goodsName,goodsSn,goodsStock,isNew,saleNum,shopPrice,marketPrice,isSpec,goodsImg,appraiseNum,visitNum,s.shopId,shopName,isSelf,isFreeShipping,gallery');
        foreach($recommendOrder as $v){
            $list->orderRaw($v);
        }
        $list = $list->order($pageBy[$orderBy]." ".$pageOrder[$order].",goodsId desc")
			->paginate(input('pagesize/d',16))->toArray();

		//加载标签
		if(!empty($list['data'])){
			foreach ($list['data'] as $key => $v) {
				$list['data'][$key]['tags'] = [];
				if($v['isSelf']==1)$list['data'][$key]['tags'][] = "<span class='tag'>自营</span>";
	      	    if($v['isFreeShipping']==1)$list['data'][$key]['tags'][] = "<span class='tag'>包邮</span>";
			}
		}
		//关注
		if(!empty($list['data'])){
			foreach ($list['data'] as $key =>$v){
				$list['data'][$key]['favGood'] = model('Favorites')->checkFavorite($v['goodsId'],0);
			}
		}
		hook('afterQueryGoods',['page'=>&$list]);
		return $list;
	}
	
	/**
	 * 关键字
	 */
	public function getKeyWords($name){
		$words = WSTAnalysis($name);
		if(!empty($words)){
			$str = [];
			if(count($words)==1){
				$str[] = ['g.goodsSerachKeywords','LIKE','%'.$words[0].'%'];
			}else{
				foreach ($words as $v){
					$str[] = ['g.goodsSerachKeywords','LIKE','%'.$v.'%'];
				}
			}
			return $str;
		}
		return "";
	}
	/**
	 * 获取价格范围
	 */
	public function getPriceGrade($goodsCatIds = []){
		$isStock = input('isStock/d');
		$isNew = input('isNew/d');
		$keyword = input('keyword');
		$isFreeShipping = input('isFreeShipping/d');
		$where = [];
		$where[] = ['goodsStatus','=',1];
		$where[] = ['g.dataFlag','=',1];
		$where[] = ['isSale','=',1];
		if($keyword!='')$where[] = ['goodsName','like','%'.$keyword.'%'];
		$areaId = (int)input('areaId');
		if($areaId>0)$where[] = ['areaId','=',$areaId];
        //属性筛选
		$goodsIds = $this->filterByAttributes();
		if(!empty($goodsIds))$where[] = ['goodsId','in',$goodsIds];
		//排序条件
		$orderBy = input('orderBy/d',0);
		$orderBy = ($orderBy>=0 && $orderBy<=4)?$orderBy:0;
		$order = (input('order/d',0)==1)?1:0;
		$pageBy = ['saleNum','shopPrice','appraiseNum','visitNum','saleTime'];
		$pageOrder = ['asc','desc'];
		if($isStock==1)$where[] = ['goodsStock','>',0];
		if($isNew==1)$where[] = ['isNew','=',1];
		if($isFreeShipping==1)$where[] = ['isFreeShipping','=',1];
		$condition = '';
		if(!empty($goodsCatIds)){
            $str = implode('_',$goodsCatIds).'_%';
            $condition = "goodsCatIdPath like '$str'";
        }
		$minPrice = input("param.minPrice");//开始价格
	    $maxPrice = input("param.maxPrice");//结束价格
	    if($minPrice!='' && $maxPrice!=''){
	    	$where[] = ['g.shopPrice','between',[(int)$minPrice,(int)$maxPrice]];
	    }elseif($minPrice!=''){
	    	$where[] = ['g.shopPrice','>=',(int)$minPrice];
		}elseif($maxPrice!=''){
			$where[] = ['g.shopPrice','<=',(int)$maxPrice];
		}

        $rs = Db::name("goods")->alias('g')->join("__SHOPS__ s","g.shopId = s.shopId",'inner')
			->where($where)
            ->where($condition)
			->field('min(shopPrice) minPrice,max(shopPrice) maxPrice')->select();
		if(isset($rs[0])){
			$rs = $rs[0];
		}else{
			return;
		}
		if($rs['maxPrice']=='')return;
		$minPrice = 0;
		$maxPrice = $rs['maxPrice'];
		$pavg5 = ($maxPrice/5);
		$prices = array();
    	$price_grade = 0.0001;
        for($i=-2; $i<= log10($maxPrice); $i++){
            $price_grade *= 10;
        }
    	//区间跨度
        $span = ceil(($maxPrice - $minPrice) / 8 / $price_grade) * $price_grade;
        if($span == 0){
            $span = $price_grade;
        }
		for($i=1;$i<=8;$i++){
			$prices[($i-1)*$span."_".($span * $i)] = ($i-1)*$span."-".($span * $i);
			if(($span * $i)>$maxPrice) break;
		}
		return $prices;
	}

}
