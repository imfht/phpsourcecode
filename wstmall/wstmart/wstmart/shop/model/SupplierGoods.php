<?php
namespace wstmart\shop\model;
use wstmart\common\validate\Goods as Validate;
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
 * 商品类
 */
class SupplierGoods extends Base{
	protected $pk = 'goodsId';
     /**
      *  上架商品列表
      */
	public function saleByPage(){
		$where[] = ['g.goodsStatus','=',1];
		$where[] = ['g.dataFlag','=',1];
		$where[] = ['g.isSale','=',1];
		$areaIdPath = input('areaIdPath');
		$goodsCatIdPath = input('goodsCatIdPath');
		$goodsName = input('goodsName');
		$supplierName = input('supplierName');
		if($areaIdPath !='')$where[] = ['areaIdPath','like',$areaIdPath."%"];
		if($goodsCatIdPath !='')$where[] = ['goodsCatIdPath','like',$goodsCatIdPath."%"];
		if($goodsName != '')$where[] = ['goodsName|goodsSn','like',"%$goodsName%"];
		if($supplierName != '')$where[] = ['supplierName|supplierSn','like',"%$supplierName%"];
		// 排序
		$sort = input('sort');
		$order = 'saleTime desc';
		if($sort!=''){
			$sortArr = explode('.',$sort);
			$order = $sortArr[0].' '.$sortArr[1];
		}
		$keyCats = model('GoodsCats')->listKeyAll();
		$rs = $this->alias('g')->join('__SUPPLIERS__ s','g.supplierId=s.supplierId','left')
		    ->where($where)
			->field('goodsId,goodsName,goodsSn,saleNum,supplierPrice,g.supplierId,goodsImg,s.supplierName,goodsCatIdPath,goodsStock')
			->order($order)
			->paginate(input('limit/d'))->toArray();
		foreach ($rs['data'] as $key => $v){
			$rs['data'][$key]['verfiycode'] = $this->supplierEncrypt($v['supplierId']);
			$rs['data'][$key]['goodsCatName'] = self::getGoodsCatNames($v['goodsCatIdPath'],$keyCats);
		}
		return $rs;
	}


	/**
	 * 获取商品资料在前台展示
	 */
     public function getBySale($goodsId){
     	$key = input('key');
     	// 浏览量
     	$this->where('goodsId',$goodsId)->setInc('visitNum',1);
		$rs = Db::name('supplier_goods')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->find();
		if(!empty($rs)){
			$rs['read'] = false;
			$rs['goodsDesc'] = htmlspecialchars_decode($rs['goodsDesc']);
			$rs['goodsDesc'] = str_replace('${DOMAIN}',WSTConf('CONF.resourcePath'),$rs['goodsDesc']);
			if(($rs['isSale']==0 || $rs['goodsStatus']==0))return [];
			//获取店铺信息
			$rs['supplier'] = model('shop/suppliers')->getSupplierInfo((int)$rs['supplierId']);
			if(empty($rs['supplier']))return [];
			$gallery = [];
			$gallery[] = $rs['goodsImg'];
			if($rs['gallery']!=''){
				$tmp = explode(',',$rs['gallery']);
				$gallery = array_merge($gallery,$tmp);
			}
			$rs['gallery'] = $gallery;
			if($rs['isSpec']==1){
				//获取规格值
				$specs = Db::name('spec_cats')->alias('gc')->join('supplier_spec_items sit','gc.catId=sit.catId','inner')
				                      ->where(['sit.goodsId'=>$goodsId,'gc.isShow'=>1,'sit.dataFlag'=>1])
				                      ->field('gc.isAllowImg,gc.catName,sit.catId,sit.itemId,sit.itemName,sit.itemImg')
				                      ->order('gc.isAllowImg desc,gc.catSort asc,gc.catId asc')->select();                     
				foreach ($specs as $key =>$v){
					$rs['spec'][$v['catId']]['name'] = $v['catName'];
					$rs['spec'][$v['catId']]['list'][] = $v;
				}

				//获取销售规格
				$sales = Db::name('supplier_goods_specs')->where('goodsId',$goodsId)->field('id,isDefault,productNo,specIds,marketPrice,specPrice,specStock')->select();
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
			$rs['attrs'] = Db::name('attributes')->alias('a')->join('supplier_goods_attributes ga','a.attrId=ga.attrId','inner')
			                   ->where(['a.isShow'=>1,'dataFlag'=>1,'goodsId'=>$goodsId])->field('a.attrName,ga.attrVal')
			                   ->order('attrSort asc')->select();
			//获取商品评分
			$rs['scores'] = Db::name('supplier_goods_scores')->where('goodsId',$goodsId)->field('totalScore,totalUsers')->find();
			$rs['scores']['totalScores'] = ($rs['scores']['totalScore']==0)?5:WSTScore($rs['scores']['totalScore'],$rs['scores']['totalUsers'],5,0,3);
			WSTUnset($rs, 'totalUsers');
			//品牌名称
			$rs['brandName'] = Db::name('brands')->where(['brandId'=>$rs['brandId']])->value('brandName');
			if($rs['isWholesale']==1){
		        $rs['wholesale']  = Db::name('supplier_wholesale_goods')->where('goodsId',$goodsId)->order('buyNum asc')->select();
		        $wholesale = [];
		        foreach ($rs['wholesale'] as $key => $v) {
		        	$v['goodsPrice'] = $rs['supplierPrice'] - $v['rebate'];
		        	$wholesale[] = $v;
		        }
		        $rs['wholesale'] = $wholesale;
		    }else{
		    	$rs['wholesale'] = [];
		    }
		}
		return $rs;
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
		if($keyword!='')$where2 = $this->getKeyWords($keyword);
		//属性筛选
		$goodsIds = $this->filterByAttributes();
		if(!empty($goodsIds))$where[] = ['goodsId','in',$goodsIds];
		// 品牌筛选
		$brandIds = input('param.brand');
		if(!empty($brandIds)){
			$brandIds = explode(',',$brandIds);
			$where[] = ['brandId','in',$brandIds];
		}
		$catId = (int)input('catId');
		// 发货地
		$areaId = (int)input('areaId');
		if($areaId>0)$where[] = ['areaId','=',$areaId];
		//排序条件
		$orderBy = input('orderBy/d',0);
		$orderBy = ($orderBy>=0 && $orderBy<=4)?$orderBy:0;
		$order = (input('order/d',1)==1)?1:0;
		$pageBy = ['saleNum','supplierPrice','appraiseNum','visitNum','saleTime'];
		$pageOrder = ['asc','desc'];
		if($isStock==1)$where[] = ['goodsStock','>',0];
		if($isNew==1)$where[] = ['isNew','=',1];
		if($isFreeShipping==1)$where[] = ['isFreeShipping','=',1];
		$condition = '';
        if(!empty($goodsCatIds)){
            $str = implode('_',$goodsCatIds).'_%';
            $condition = "goodsCatIdPath like '$str'";
        }
	    $minPrice = (int)input("param.minPrice");//开始价格
	    $maxPrice = (int)input("param.maxPrice");//结束价格
		if($minPrice>0 && $maxPrice>0){
	    	$where[] = ['g.supplierPrice','between',[(int)$minPrice,(int)$maxPrice]];
	    }elseif($minPrice>0){
	    	$where[] = ['g.supplierPrice','>=',(int)$minPrice];
		}elseif($maxPrice>0){
			$where[] = ['g.supplierPrice','<=',(int)$maxPrice];
		}
		$list = Db::name("supplier_goods")->alias('g')->join("suppliers s","g.supplierId = s.supplierId")
			->where($where)->where($where2)
            ->where($condition)
			->field('goodsId,goodsName,goodsSn,goodsStock,isNew,saleNum,supplierPrice,marketPrice,isSpec,goodsImg,appraiseNum,visitNum,s.supplierId,supplierName,isFreeShipping,gallery')
			->order($pageBy[$orderBy]." ".$pageOrder[$order].",goodsId desc")
			->paginate(input('pagesize/d',20))->toArray();
		//加载标签
		if(!empty($list['data'])){
			foreach ($list['data'] as $key => $v) {
				$list['data'][$key]['tags'] = [];
	      	    if($v['isFreeShipping']==1)$list['data'][$key]['tags'][] = "<span class='tag'>包邮</span>";
			}
		}
	
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
		$isStock = (int)input('isStock/d');
		$isNew = (int)input('isNew/d');
		$keyword = input('keyword');
		$isFreeShipping = (int)input('isFreeShipping/d');
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
		$pageBy = ['saleNum','supplierPrice','appraiseNum','visitNum','saleTime'];
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
	    	$where[] = ['g.supplierPrice','between',[(int)$minPrice,(int)$maxPrice]];
	    }elseif($minPrice!=''){
	    	$where[] = ['g.supplierPrice','>=',(int)$minPrice];
		}elseif($maxPrice!=''){
			$where[] = ['g.supplierPrice','<=',(int)$maxPrice];
		}

        $rs = Db::name("supplier_goods")->alias('g')->join("suppliers s","g.supplierId = s.supplierId",'inner')
			->where($where)
            ->where($condition)
			->field('min(supplierPrice) minPrice,max(supplierPrice) maxPrice')->select();
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
	    			$sql = "select goodsId from ".$prefix."supplier_goods_attributes where attrId=".(int)$v." and find_in_set('".$v1."',attrVal) ";
					$rs = Db::query($sql);
					if(!empty($rs)){
						foreach ($rs as $vg){
							$goodsIds2[] = $vg['goodsId'];
						}
					}
	    		}
	    	}else{
		    	$sql = "select goodsId from ".$prefix."supplier_goods_attributes 
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
	 * 获取商品资料方便编辑
	 */
	public function getById($goodsId){
		$rs = $this->where(['goodsId'=>$goodsId,'dataFlag'=>1,'isSale'=>1])->find();
		if(!empty($rs)){
			if($rs['gallery']!='')$rs['gallery'] = explode(',',$rs['gallery']);
			$rs['goodsDesc'] = htmlspecialchars_decode($rs['goodsDesc']);
            $resourceDomain = WSTConf('CONF.resourcePath');
            $rs['goodsDesc'] = str_replace('${DOMAIN}',$resourceDomain,$rs['goodsDesc']);
			//获取规格值
			$specs = Db::name('spec_cats')->alias('gc')->join('supplier_spec_items sit','gc.catId=sit.catId','inner')
			                      ->where(['sit.goodsId'=>$goodsId,'gc.isShow'=>1,'sit.dataFlag'=>1])
			                      ->field('gc.isAllowImg,sit.catId,sit.itemId,sit.itemName,sit.itemImg')
			                      ->order('gc.isAllowImg desc,gc.catSort asc,gc.catId asc')->select();
			$spec0 = [];
			$spec1 = [];                    
			foreach ($specs as $key =>$v){
				if($v['isAllowImg']==1){
					$spec0[] = $v;
				}else{
					$spec1[] = $v;
				}
			}
			$rs['spec0'] = $spec0;
			$rs['spec1'] = $spec1;
			//获取销售规格
			$rs['saleSpec'] = Db::name('supplier_goods_specs')->where('goodsId',$goodsId)->field('id,isDefault,productNo,specIds,marketPrice,specPrice,specStock,warnStock,saleNum,specWeight,specVolume')->select();
			//获取属性值
			$rs['attrs'] = Db::name('supplier_goods_attributes')->alias('ga')->join('attributes a','ga.attrId=a.attrId','inner')
			                 ->where('goodsId',$goodsId)->field('ga.attrId,a.attrType,ga.attrVal')->select();
			if($rs['isWholesale']==1){
		        $rs['wholesale']  = Db::name('supplier_wholesale_goods')->where('goodsId',$goodsId)->order('buyNum asc')->select();
		        $wholesale = [];
		        foreach ($rs['wholesale'] as $key => $v) {
		        	$v['goodsPrice'] = $rs['supplierPrice'] - $v['rebate'];
		        	$wholesale[] = $v;
		        }
		        $rs['wholesale'] = $wholesale;
		    }else{
		    	$rs['wholesale'] = [];
		    }
		}
		return $rs;
	}


	/**
	 * 新增商品
	 */
	public function add(){
		$shopId = (int)session('WST_USER.shopId');
		$supplierGoodsId = (int)input("supplierGoodsId");
		
		$hasCopy = $this->checkHasCopy($shopId,$supplierGoodsId);
		if($hasCopy)return WSTReturn("您已铺货该商品，不能重复铺货");	
		$supplierGoods = Db::name("supplier_goods")->where(['dataFlag'=>1,'goodsId'=>$supplierGoodsId])->find();
		if(empty($supplierGoods) || ($supplierGoods['isSale']==0 || $supplierGoods['goodsStatus']==0))return WSTReturn("无效的商品信息");
		$goodmodel = model('GoodsCats');
		$goodsCats = $goodmodel->getParentIs($supplierGoods['goodsCatId']);
		$cartId = $goodsCats[count($goodsCats)-1];
		$gcat = Db::name("goods_cats")->where(["catId"=>$cartId])->field("catId,catName")->find();
		//校验商品分类有效性
		$applyCatIds = $goodmodel->getShopApplyGoodsCats($shopId);
		$isApplyCatIds = array_intersect($applyCatIds,$goodsCats);
		if(empty($isApplyCatIds))return WSTReturn("您的店铺未开通“".$gcat['catName']."”商城分类，不能复制该商品信息");		
		

		Db::startTrans();
        try{
        	$supplierId = $supplierGoods['supplierId'];
        	//对图片域名进行处理
			$resourceDomain = WSTConf('CONF.resourcePath');
        	$shop = model('shops')->get(['shopId'=>$shopId]);
        	$data = $supplierGoods;
        	if(WSTConf("CONF.isGoodsVerify")==1){
	            $data['goodsStatus'] = 0;
	        }else{
	            $data['goodsStatus'] = 1;
	        }
    		//复制商品主图
    		$timg = explode(".",$supplierGoods['goodsImg']);
    		$ext = $timg[count($timg)-1];
    		$gpath = 'upload/goods/'.date('Y-m');
    		$filePath = Env::get('root_path').$gpath;
    		$filename = strtolower(WSTGuid()).".".$ext;
    		$url = WSTConf('CONF.resourceDomain')."/".$supplierGoods['goodsImg'];
    		$this->downCopyFile($url, $filePath, $filename);
    		$data['goodsImg'] = $gpath.'/'.$filename;
        	
        	if($supplierGoods['goodsVideo']!=''){
        		//复制商品视频
	    		$timg = explode(".",$supplierGoods['goodsVideo']);
	    		$ext = $timg[count($timg)-1];
	    		$gpath = 'upload/goods/'.date('Y-m');
	    		$filePath = Env::get('root_path').$gpath;
	    		$filename = strtolower(WSTGuid()).".".$ext;
	    		$url = WSTConf('CONF.resourceDomain')."/".$supplierGoods['goodsVideo'];
	    		$this->downCopyFile($url, $filePath, $filename,1);
	    		$data['goodsVideo'] = $gpath.'/'.$filename;
        	}
        	
        	
        	//复制商品相册
        	$supplierGallerys = [];
        	if($supplierGoods['gallery']!=""){
        		$supplierGallerys = explode(",",$supplierGoods['gallery']);
        		foreach ($supplierGallerys as $key => $gimg) {
    				$timg = explode(".",$gimg);
	        		$ext = $timg[count($timg)-1];
	        		$gpath = 'upload/goods/'.date('Y-m');
	        		$filePath = Env::get('root_path').$gpath;
	        		$filename = strtolower(WSTGuid()).".".$ext;
	        		$url = WSTConf('CONF.resourceDomain')."/".$gimg;
	        		$this->downCopyFile($url, $filePath, $filename);
	        		$gallerys[$key] = $gpath.'/'.$filename;
        		}
        		$data['gallery'] = implode(",",$gallerys);
        	}
        	$supplierGoods['goodsDesc'] = htmlspecialchars_decode($supplierGoods['goodsDesc']);
			$goodsDesc = str_replace('${DOMAIN}',WSTConf('CONF.resourcePath'),$supplierGoods['goodsDesc']);
			//编辑器里的图片
			$rule = '/src="\/.*?(upload.*?)"/';
		    // 获取旧的src数组
		    preg_match_all($rule,$goodsDesc,$pathImgs);
		    //print_r($pathImgs);exit();
		    $spathImgs = $pathImgs[1];
		    if(!empty($spathImgs)){
        		foreach ($spathImgs as $key => $gimg) {
    				$timg = explode(".",$gimg);
	        		$ext = $timg[count($timg)-1];
	        		$gpath = 'upload/goods/'.date('Y-m');
	        		$filePath = Env::get('root_path').$gpath;
	        		$filename = strtolower(WSTGuid()).".".$ext;
	        		$url = WSTConf('CONF.resourceDomain')."/".$gimg;
	        		$this->downCopyFile($url, $filePath, $filename,2);
	        		//$spathImgs[$key] = $gpath.'/'.$filename;
	        		$data['goodsDesc'] = str_replace($gimg, ($gpath.'/'.$filename), $data['goodsDesc']);
        		}
        	}
        	WSTUnset($data,"goodsId,supplierGoodsId,supplierId,supplierPrice,supplierCatId1,supplierCatId2,isWholesale,supplierExpressId");
        	$data["shopId"] = $shopId;
        	$data["shopPrice"] = $supplierGoods['supplierPrice'];
        	$data["createTime"] = date("Y-m-d H:i:s");
        	$data["isSale"] = 0;
        	$data['saleNum'] = 0;
        	$data['shopCatId1'] = 0;
        	$data['shopCatId2'] = 0;
        	$data['goodsSn'] = WSTGoodsNo();
        	$productNo = WSTGoodsNo();
        	$data['productNo'] = $productNo;
    		$result = model("goods")->allowField(true)->save($data);
        	
			if(false !== $result){
				$goodsId = model("goods")->goodsId;
				//商品图片
				WSTUseResource(0, $goodsId, $data['goodsImg']);
				//商品相册
				WSTUseResource(0, $goodsId, $data['gallery']);
				//商品描述图片
				WSTEditorImageRocord(0, $goodsId, '',$data['goodsDesc']);
				// 视频
				if(isset($data['goodsVideo']) && $data['goodsVideo']!=''){
					WSTUseResource(0, $goodsId, $data['goodsVideo']);
				}
				//复制商品关联记录
				$copyrelation = [];
				$copyrelation['goodsId'] = $goodsId;
				$copyrelation['shopId'] = $shopId;
				$copyrelation['supplierGoodsId'] = $supplierGoodsId;
				$copyrelation['supplierId'] = $supplierId;
				$copyrelation['dataFlag'] = 1;
				$copyrelation['createTime'] = date('Y-m-d H:i:s');
				Db::name('supplier_goods_copyrelates')->insert($copyrelation);
				//建立商品评分记录
				$gs = [];
				$gs['goodsId'] = $goodsId;
				$gs['shopId'] = $shopId;
				Db::name('goods_scores')->insert($gs);
				//如果是实物商品并且有销售规格则保存销售和规格值
    	        if($data['goodsType']==0 && $data['isSpec']==1){
	    	        $specItems = Db::name('supplier_spec_items')->where(['goodsId'=>$supplierGoodsId])->select();
	    	       	$itemIdMaps = [];
		    		foreach ($specItems as $v){
		    			$itemImg = $v["itemImg"];
		    			if($itemImg!=""){
		    				$timg = explode(".",$itemImg);
			        		$ext = $timg[count($timg)-1];
			        		$gpath = 'upload/goods/'.date('Y-m');
			        		$filePath = Env::get('root_path').$gpath;
			        		$filename = strtolower(WSTGuid()).".".$ext;
			        		$url = WSTConf('CONF.resourceDomain')."/".$itemImg;
			        		$this->downCopyFile($url, $filePath, $filename);
			        		$itemImg = $gpath.'/'.$filename;
		    			}
		    			$sitem['shopId'] = $shopId;
		    			$sitem['catId'] = $v['catId'];
		    			$sitem['goodsId'] = $goodsId;
		    			$sitem['itemName'] = $v['itemName'];
		    			$sitem['itemImg'] = $itemImg;
		    			$sitem['dataFlag'] = 1;
		    			$sitem['createTime'] = date('Y-m-d H:i:s');
		    			$itemId = Db::name('spec_items')->insertGetId($sitem);
		    			$itemIdMaps[$v['itemId']] = $itemId;
		    			if($sitem['itemImg']!='')WSTUseResource(0, $itemId, $sitem['itemImg']);
		    			
		    		}
		    		$gspecArray = [];
		    		$goodsSpecs = Db::name('supplier_goods_specs')->where(['goodsId'=>$supplierGoodsId,'dataFlag'=>1])->select();
		    		$pno = 0;
		    		foreach ($goodsSpecs as $v){
		    			$pno++;
		    			$gspec = [];
		    			$tmpspecIds = explode(":",$v['specIds']);
		    			$specIds = [];
		    			for ($i=0; $i < count($tmpspecIds); $i++) { 
		    				$specIds[] = $itemIdMaps[$tmpspecIds[$i]];
		    			}
		    			$gspec['specIds'] = implode(":",$specIds);
		    			$gspec['shopId'] = $shopId;
		    			$gspec['goodsId'] = $goodsId;
		    			$gspec['productNo'] = $productNo."-".$pno;
		    			$gspec['marketPrice'] = $v['marketPrice'];
		    			$gspec['specPrice'] = $v['specPrice'];
		    			$gspec['specStock'] = $v['specStock'];
		    			$gspec['warnStock'] = $v['warnStock'];
		    			$gspec['isDefault'] = $v['isDefault'];
		    			$gspec['dataFlag'] = 1;
		    			$gspec['saleNum'] = 0;
		    			$gspec['specWeight'] = $v['specWeight'];
		    			$gspec['specVolume'] = $v['specVolume'];
		    			$gspecArray[] = $gspec;
		    		}
		    		if(count($gspecArray)>0){
		    		    Db::name('goods_specs')->insertAll($gspecArray);
		    		    //更新默认价格和总库存
    	                $this->where('goodsId',$goodsId)->update(['isSpec'=>1]);
		    		}
    	        }

    	        //保存商品属性
		    	$attrsArray = [];
		    	$goodsAttributes = Db::name('supplier_goods_attributes')->where(['goodsId'=>$supplierGoodsId])->select();

		    	foreach ($goodsAttributes as $key =>$v){
		    		$attrs = [];
		    		$attrs['attrVal'] = $v['attrVal'];
		    		$attrs['shopId'] = $shopId;
		    		$attrs['goodsId'] = $goodsId;
		    		$attrs['attrId'] = $v['attrId'];
		    		$attrs['createTime'] = date('Y-m-d H:i:s');
		    		$attrsArray[] = $attrs;
		    	}
		    	if(count($attrsArray)>0)Db::name('goods_attributes')->insertAll($attrsArray);
		    	//保存关键字
        	    $searchKeys = WSTGroupGoodsSearchKey($goodsId);
        	    Db::name("goods")->where('goodsId',$goodsId)->update(['goodsSerachKeywords'=>implode(',',$searchKeys)]);
    	        Db::commit();
				return WSTReturn("复制成功", 1,['id'=>$goodsId]);
			}else{
				return WSTReturn($this->getError(),-1);
			}
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('复制失败',-1);
        }
	}
    

    /**
	 * 获取店铺商品列表
	 */
	public function supplierGoods($supplierId){
		$msort = (int)input("param.msort");
		$mdesc = (int)input("param.mdesc");
		$order = array('g.saleTime'=>'desc');
		$orderFile = array('0'=>'(gs.totalScore/gs.totalUsers),g.saleNum','1'=>'g.isHot','2'=>'g.saleNum','3'=>'g.supplierPrice','4'=>'g.supplierPrice','5'=>'(gs.totalScore/gs.totalUsers)','6'=>'g.saleTime');
		$orderSort = array('0'=>'asc','1'=>'desc');
		$order = $orderFile[$msort]." ".$orderSort[$mdesc];
		$goodsName = input("param.goodsName");//搜索店鋪名
		$words = $where = $where2 = $where3 = $where4 = [];
		if($goodsName!=""){
			$words = explode(" ",$goodsName);
		}
		if(!empty($words)){
			$sarr = array();
			foreach ($words as $key => $word) {
				if($word!=""){
					$sarr[] = "g.goodsName like '%$word%'";
				}
			}
			$where4 = implode(" or ", $sarr);
		}

		$sprice = input("param.sprice");//开始价格
		$eprice = input("param.eprice");//结束价格
		if($sprice!="")$where2 = "g.supplierPrice >= ".(float)$sprice;
		if($eprice!="")$where3 = "g.supplierPrice <= ".(float)$eprice;
		$ct1 = input("param.ct1/d");
		$ct2 = input("param.ct2/d");
		if($ct1>0)$where['supplierCatId1'] = $ct1;
		if($ct2>0)$where['supplierCatId2'] = $ct2;
		$goods = Db::name('supplier_goods')->alias('g')
		->join('supplier_goods_scores gs','gs.goodsId = g.goodsId','left')
		->where(['g.supplierId'=>$supplierId,'g.isSale'=>1,'g.goodsStatus'=>1,'g.dataFlag'=>1])
		->where($where)->where($where2)->where($where3)->where($where4)
		->field('g.goodsId,g.goodsName,g.goodsImg,g.supplierPrice,g.marketPrice,g.saleNum,g.appraiseNum,g.goodsStock,g.isFreeShipping,gallery')
		->orderRaw($order)
		->paginate((input('pagesize/d')>0)?input('pagesize/d'):16)->toArray();
		return  $goods;
	}

	/**
	 * 获取店铺分类列表
	 */
	public function listSupplierCats($parentId=0,$num,$supplierId = 0,$cache = 0){
		if($supplierId==0)return [];
		$cacheData = cache('TAG_SUPPLIER_CATS_'.$supplierId."_".$parentId."_".$num."_".$cache);
		if($cacheData)return $cacheData;
		$where = [];
		$where[] = ['isShow','=',1];
		$where[] = ['dataFlag','=',1];
		$where[] = ['supplierId','=',$supplierId];
		$where[] = ['parentId','=',$parentId];
		$data = Db::name('supplier_cats')
		->field('catId,supplierId,catName')
		->limit($num)->where($where)
		->order('catSort asc')->select();
		cache('TAG_SUPPLIER_CATS_'.$supplierId."_".$parentId."_".$num."_".$cache,$data,$cache,'CACHETAG_SUPPLIER_'.$supplierId);
		return $data;
	}

	/**
	 * 获取指定店铺商品
	 */
	public function listSupplierGoods($type,$supplierId,$cat = 0,$num = 0,$cache = 0){
	
	    $types = ['recom'=>'isRecom','new'=>'isNew','hot'=>'isHot','best'=>'isBest'];
	    $order = ['recom'=>'saleNum desc,goodsId asc','new'=>'saleTime desc,goodsId asc','hot'=>'saleNum desc,goodsId asc','best'=>'saleNum desc,goodsId asc'];
	    if(!isset($types[$type]))return [];
	    $where = [];
	    if($cat>0){
		    $parentId = Db::name('supplier_cats')->where(['catId'=>$cat,'supplierId'=>$supplierId,'dataFlag'=>1,'isShow'=>1])->value('parentId');
	        if($parentId>0){
                 $where[] = ['supplierCatId2','=',$cat];
	        }else{
                 $where[] = ['supplierCatId1','=',$cat];
	        }
	    }
	    $where[] = ['supplierId','=',$supplierId];
	    $where[] = ['isSale','=',1];
	    $where[] = ['goodsStatus','=',1]; 
	    $where[] = ['dataFlag','=',1]; 
	    $where[] = [$types[$type],'=',1];
        $goods = Db::name('supplier_goods')
                   ->where($where)->field('goodsId,goodsName,goodsImg,goodsSn,goodsStock,saleNum,supplierPrice,marketPrice,isSpec,appraiseNum,visitNum')
                   ->order($order[$type])->limit($num)->select();       
        $ids = [];
        foreach($goods as $key =>$v){
        	if($v['isSpec']==1)$ids[] = $v['goodsId'];
        }
        if(!empty($ids)){
        	$specs = [];
        	$rs = Db::name('supplier_goods_specs gs ')->where([['goodsId','in',$ids],['dataFlag','=',1]])->order('id')->select();
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



	/*
	*下载并复制图片
	$type 1:视频 0：图片
	*/
	
	public function downCopyFile($url, $filePath = '', $filename = '', $type = 0, $isThumb = 1) {
	    if (trim($url) == '') {
	        return false;
	    }
	    if (trim($filePath) == '') {
	        $filePath = './';
	    }
	    if (0 !== strrpos($filePath, '/')) {
	        $filePath.= '/';
	    }
	    //创建保存目录
	    if (!file_exists($filePath) && !mkdir($filePath, 0777, true)) {
	        return false;
	    }
	    
        ob_start();
        readfile($url);
        $content = ob_get_contents();
        ob_end_clean();
	    
	    $size = strlen($content);
	    //文件大小
	    $fp2 = @fopen($filePath . $filename, 'a');
	    fwrite($fp2, $content);
	    fclose($fp2);
	    unset($content, $url);
	    if($type==1){
	    	$this->svideoUpload($filePath,$filename);
	    }else if($type==2){
	    	$this->seditUpload($filePath,$filename,$isThumb);
	    }else{
	    	$this->sfileUpload($filePath,$filename,$isThumb);
	    }
	    
	    return array(
	        'file_name' => $filename,
	        'save_path' => $filePath . $filename
	    );
	}


	public function sfileUpload($filePath,$fname,$isThumb=1,$isWatermark=1){
		$filePath = str_replace(Env::get('root_path'),'',$filePath);
		$filePath = str_replace('\\','/',$filePath);
		$filePath = str_replace($fname,'',$filePath);
		//原图路径
		$imageSrc = trim($filePath.$fname,'/');
		//图片记录
		WSTRecordResources($imageSrc, 0);
		//打开原图
		$image = \image\Image::open($imageSrc);
		//缩略图路径 手机版原图路径 手机版缩略图路径
		$thumbSrc = $mSrc = $mThumb = null;
		//手机版原图宽高
		$mWidth = min($image->width(),(int)input('mWidth',700));
		$mHeight = min($image->height(),(int)input('mHeight',700));
		//手机版缩略图宽高
		$mTWidth = min($image->width(),(int)input('mTWidth',250));
		$mTHeight = min($image->height(),(int)input('mTHeight',250));

		/****************************** 生成缩略图 *********************************/
		//$isThumb = (int)input('isThumb');
		$isThumb =1;
		if($isThumb==1){
			// 检测是否需要翻转图片
			$image = checkImageOrientation($image, $imageSrc);

			//缩略图路径
			$thumbSrc = str_replace('.', '_thumb.', $imageSrc);
			$image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
			//是否需要生成移动版的缩略图
			$suffix = WSTConf("CONF.wstMobileImgSuffix");
			if(!empty($suffix)){
				$image = \image\Image::open($imageSrc);
				$mSrc = str_replace('.',"$suffix.",$imageSrc);
				$mThumb = str_replace('.', '_thumb.',$mSrc);
				$image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
				$image->thumb($mTWidth, $mTHeight, 2)->save($mThumb,$image->type(),90);
			}
		}
		/***************************** 添加水印 ***********************************/
		if($isWatermark==1 && (int)WSTConf('CONF.watermarkPosition')!==0){
	    	//取出水印配置
	    	$wmWord = WSTConf('CONF.watermarkWord');//文字
	    	$wmFile = trim(WSTConf('CONF.watermarkFile'),'/');//水印文件
	    	//判断水印文件是否存在
	    	if(!file_exists(WSTRootPath()."/".$wmFile))$wmFile = '';
	    	$wmPosition = (int)WSTConf('CONF.watermarkPosition');//水印位置
	    	$wmSize = ((int)WSTConf('CONF.watermarkSize')!=0)?WSTConf('CONF.watermarkSize'):'20';//大小
	    	$wmColor = (WSTConf('CONF.watermarkColor')!='')?WSTConf('CONF.watermarkColor'):'#000000';//颜色必须是16进制的
	    	$wmOpacity = ((int)WSTConf('CONF.watermarkOpacity')!=0)?WSTConf('CONF.watermarkOpacity'):'100';//水印透明度
	    	//是否有自定义字体文件
	    	$customTtf = Env::get('root_path').WSTConf('CONF.watermarkTtf');
	    	$ttf = is_file($customTtf)?$customTtf:Env::get('extend_path').'verify/verify/ttfs/3.ttf';
	        $image = \image\Image::open($imageSrc);
	    	if(!empty($wmWord)){//当设置了文字水印 就一定会执行文字水印,不管是否设置了文件水印
	    		// 文字偏移量
	    		$offset = WSTConf('CONF.watermarkOffset');
	    		if($offset!=''){
	    			$offset = explode(',',str_replace('，', ',',$offset));
	    			$offset = array_slice($offset,0,2);
	    			$offset = array_map(function($val){return (int)$val;},$offset);
	    			if(count($offset)<2)array_push($offset, 0);
	    		}
	    		//执行文字水印
	    		$image->text($wmWord, $ttf, $wmSize, $wmColor, $wmPosition,$offset)->save($imageSrc);
	    		if($thumbSrc!==null){
	    			$image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
	    		}
	    		//如果有生成手机版原图
	    		if(!empty($mSrc)){
	    			$image = \image\Image::open($imageSrc);
	    			$image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
	    			$image->thumb($mTWidth, $mTHeight, 2)->save($mThumb,$image->type(),90);
	    		}
	    	}elseif(!empty($wmFile)){//设置了文件水印,并且没有设置文字水印
	    		//执行图片水印
	    		$image->water($wmFile, $wmPosition, $wmOpacity)->save($imageSrc);
	    		if($thumbSrc!==null){
	    			$image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
	    		}
	    		//如果有生成手机版原图
	    		if($mSrc!==null){
	    			$image = \image\Image::open($imageSrc);
	    			$image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
	    			$image->thumb($mTWidth, $mTHeight,2)->save($mThumb,$image->type(),90);
	    		}
	    	}
		}
		//判断是否有生成缩略图
		$thumbSrc = ($thumbSrc==null)?$fname:str_replace('.','_thumb.', $fname);
		$filePath = ltrim($filePath,'/');
		// 用户头像上传宽高限制
		$isCut = (int)input('isCut');
		if($isCut){
			$imgSrc = $filePath.$fname;
			$image = \image\Image::open($imgSrc);
			$size = $image->size();//原图宽高
			$w = $size[0];
			$h = $size[1];
			$rate = $w/$h;
			if($w>$h && $w>500){
				$newH = 500/$rate;
				$image->thumb(500, $newH)->save($imgSrc,$image->type(),90);
			}elseif($h>$w && $h>500){
				$newW = 500*$rate;
				$image->thumb($newW, 500)->save($imgSrc,$image->type(),90);
			}
		}
		$info=null;
		$rdata = ['status'=>1,'savePath'=>$filePath,'name'=>$fname,'thumb'=>$thumbSrc];
		hook('afterUploadPic',['data'=>&$rdata,'isLocation'=>(int)input('isLocation')]);
	}


	public function svideoUpload($filePath,$fname){
		$filePath = str_replace(Env::get('root_path'),'',$filePath);
		$filePath = str_replace('\\','/',$filePath);
		$filePath = str_replace($fname,'',$filePath);

		$rdata = ['status'=>1,'name'=>$fname,'savePath'=>$filePath];
		// 视频记录
		$videoSrc = trim($filePath.$fname,'/');
		// 只有商家才能上传视频
    	WSTRecordResources($videoSrc, 0, 1);
		hook('afterUploadPic',['data'=>&$rdata,'isVideo'=>true]);
	}


	public function seditUpload($filePath,$fname,$fromType=1,$mediaType=0){
		
		$filePath = str_replace(Env::get('root_path'),'',$filePath);
		$filePath = str_replace('\\','/',$filePath);
		$filePath = str_replace($fname,'',$filePath);

		$timg = explode(".",$fname);
		$extension = $timg[count($timg)-1];
        if(in_array($extension,['jpg','jpeg','gif','png','bmp'])){
            //原图路径
            $imageSrc = trim($filePath.$fname,'/');
            //打开原图
            $image = \image\Image::open($imageSrc);

            //手机版原图宽高
            $mWidth = min($image->width(),(int)input('mWidth',700));
            $mHeight = min($image->height(),((int)input('mHeight',700)>$image->height())?$image->height():$image->height()/2);
            /****************************** 生成移动大图 *********************************/

            //是否需要生成移动版的大图
            $suffix = WSTConf("CONF.wstMobileImgSuffix");
            if(!empty($suffix)){
                $image = \image\Image::open($imageSrc);
                $mSrc = str_replace('.',"$suffix.",$imageSrc);
                $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
            }

            /***************************** 添加水印 ***********************************/
            if((int)WSTConf('CONF.watermarkPosition')!==0){
                //取出水印配置
                $wmWord = WSTConf('CONF.watermarkWord');//文字
                $wmFile = trim(WSTConf('CONF.watermarkFile'),'/');//水印文件
                //判断水印文件是否存在
                if(!file_exists(WSTRootPath()."/".$wmFile))$wmFile = '';
                $wmPosition = (int)WSTConf('CONF.watermarkPosition');//水印位置
                $wmSize = ((int)WSTConf('CONF.watermarkSize')!=0)?WSTConf('CONF.watermarkSize'):'20';//大小
                $wmColor = (WSTConf('CONF.watermarkColor')!='')?WSTConf('CONF.watermarkColor'):'#000000';//颜色必须是16进制的
                $wmOpacity = ((int)WSTConf('CONF.watermarkOpacity')!=0)?WSTConf('CONF.watermarkOpacity'):'100';//水印透明度
                //是否有自定义字体文件
                $customTtf = Env::get('root_path').WSTConf('CONF.watermarkTtf');
                $ttf = is_file($customTtf)?$customTtf:Env::get('extend_path').'verify/verify/ttfs/3.ttf';
                $image = \image\Image::open($imageSrc);
                if(!empty($wmWord)){//当设置了文字水印 就一定会执行文字水印,不管是否设置了文件水印
                    // 文字偏移量
                    $offset = WSTConf('CONF.watermarkOffset');
                    if($offset!=''){
                        $offset = explode(',',str_replace('，', ',',$offset));
                        $offset = array_slice($offset,0,2);
                        $offset = array_map(function($val){return (int)$val;},$offset);
                        if(count($offset)<2)array_push($offset, 0);
                    }
                    //执行文字水印
                    $image->text($wmWord, $ttf, $wmSize, $wmColor, $wmPosition,$offset)->save($imageSrc);

                    //如果有生成手机版原图
                    if(!empty($mSrc)){
                        $image = \image\Image::open($imageSrc);
                        $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                    }
                }elseif(!empty($wmFile)){//设置了文件水印,并且没有设置文字水印
                    //执行图片水印
                    $image->water($wmFile, $wmPosition, $wmOpacity)->save($imageSrc);
                    //如果有生成手机版原图
                    if($mSrc!==null){
                        $image = \image\Image::open($imageSrc);
                        $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                    }
                }
            }
        }

        $rdata = ['status'=>1,'name'=>$fname,'savePath'=>ltrim($filePath,'/')];
        $info = null;
    	hook('afterUploadPic',['data'=>&$rdata]);
    	//图片记录
    	WSTRecordResources($imageSrc, (int)$fromType, $mediaType);
	    	
	}

	public function checkHasCopy($shopId,$supplierGoodsId){
		$where = [];
		$where[] = ["shopId","=",$shopId];
		$where[] = ["supplierGoodsId","=",$supplierGoodsId];
		$where[] = ["dataFlag","=",1];
		$rs = Db::name("supplier_goods_copyrelates")->where($where)->find();
		return (!empty($rs))?true:false;
	}
}
