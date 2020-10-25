<?php
namespace wstmart\home\controller;
use think\Db;
use wstmart\home\model\Goods as M;
use wstmart\common\model\Goods as CM;
use wstmart\common\model\Attributes as AT;
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
 * 商品控制器
 */
class Goods extends Base{
    /**
     * 获取商品规格属性
     */
    public function getSpecAttrs(){
    	$m = new M();
    	return $m->getSpecAttrs();
    }
    /**
     * 进行商品搜索
     */
    public function search(){
    	//获取商品记录
    	$m = new M();
    	$data = [];
    	$data['isStock'] = Input('isStock/d');
    	$data['isNew'] = Input('isNew/d');
        $data['isFreeShipping'] = input('isFreeShipping/d');
    	$data['orderBy'] = Input('orderBy/d');
    	$data['order'] = Input('order/d',1);
    	$data['keyword'] = WSTReplaceFilterWords(input('keyword'),WSTConf("CONF.limitWords"));
    	$data['minPrice'] = Input('minPrice/d');
    	$data['maxPrice'] = Input('maxPrice/d');
        $data['areaId'] = (int)Input('areaId');
        $aModel = model('home/areas');

        hook('afterUserSearchWords',['keyword'=>input('keyword')]);
        // 获取地区
        $data['area1'] = $data['area2'] = $data['area3'] = $aModel->listQuery(); // 省级

        // 如果有筛选地区 获取上级地区信息
        
        if($data['areaId']!==0){
            $areaIds = $aModel->getParentIs($data['areaId']);
            /*
              2 => int 440000
              1 => int 440100
              0 => int 440106
            */
            $selectArea = [];
            $areaName = '';
            foreach($areaIds as $k=>$v){
                $a = $aModel->getById($v);
                $areaName .=$a['areaName'];
                $selectArea[] = $a;
            }
            // 地区完整名称
            $selectArea['areaName'] = $areaName;
            // 当前选择的地区
            $data['areaInfo'] = $selectArea;
            $data['area2'] = $aModel->listQuery($areaIds[2]); // 广东的下级
 
            $data['area3'] = $aModel->listQuery($areaIds[1]); // 广州的下级
        }
        

    	$data['goodsPage'] = $m->pageQuery();
    	return $this->fetch("goods_search",$data);
    }
    
    /**
     * 获取商品列表
     */
    public function lists(){
    	$catId = (int)Input('cat/d');
        $goodsCatIds = [];
        if($catId>0){
            $goodsCatIds = model('GoodsCats')->getParentIs($catId);
        }
    	reset($goodsCatIds);
    	//填充参数
    	$data = [];
    	$data['catId'] = $catId;
    	$data['isStock'] = Input('isStock/d');
    	$data['isNew'] = Input('isNew/d');
        $data['isFreeShipping'] = input('isFreeShipping/d');
    	$data['orderBy'] = Input('orderBy/d');
    	$data['order'] = Input('order/d',1);
    	$data['minPrice'] = Input('minPrice');
    	$data['maxPrice'] = Input('maxPrice');
    	$data['attrs'] = [];

        $data['areaId'] = (int)Input('areaId');
        $aModel = model('home/areas');

        // 分类信息
        $catInfo = Db::name("goods_cats")->field("seoTitle,seoKeywords,seoDes,catListTheme")->where(['catId'=>$catId,'dataFlag'=>1])->find();
        $this->assign("catInfo",$catInfo);

        // 获取地区
        $data['area1'] = $data['area2'] = $data['area3'] = $aModel->listQuery(); // 省级

        // 如果有筛选地区 获取上级地区信息
        if($data['areaId']!==0){
            $areaIds = $aModel->getParentIs($data['areaId']);
            /*
              2 => int 440000
              1 => int 440100
              0 => int 440106
            */
            $selectArea = [];
            $areaName = '';
            foreach($areaIds as $k=>$v){
                $a = $aModel->getById($v);
                $areaName .=$a['areaName'];
                $selectArea[] = $a;
            }
            // 地区完整名称
            $selectArea['areaName'] = $areaName;
            // 当前选择的地区
            $data['areaInfo'] = $selectArea;

            $data['area2'] = $aModel->listQuery($areaIds[2]); // 广东的下级
 
            $data['area3'] = $aModel->listQuery($areaIds[1]); // 广州的下级
        }
        
    	$vs = input('vs');
    	$vs = ($vs!='')?explode(',',$vs):[];
    	foreach ($vs as $key => $v){
    		if($v=='' || $v==0)continue;
    		$v = (int)$v;
    		$data['attrs']['v_'.$v] = input('v_'.$v);
    	}
    	$data['vs'] = $vs;

    	$brandIds = Input('brand');

		
        $bgIds = [];// 品牌下的商品Id
        if(!empty($vs)){
            // 存在筛选条件,取出符合该条件的商品id,根据商品id获取可选品牌
            $goodsId = model('goods')->filterByAttributes();
            $data['brandFilter'] = model('Brands')->canChoseBrands($goodsId);
        }else{
           // 取出分类下包含商品的品牌
           $data['brandFilter'] = model('Brands')->goodsListQuery((int)current($goodsCatIds));
        }
        if(!empty($brandIds))$bgIds = model('Brands')->getGoodsIds($brandIds);


    	$data['price'] = Input('price');
    	//封装当前选中的值
    	$selector = [];
    	//处理品牌
        $brandIds = explode(',',$brandIds);
        $bIds = $brandNames = [];
        foreach($brandIds as $bId){
        	if($bId>0){
        		foreach ($data['brandFilter'] as $key =>$v){
        			if($v['brandId']==$bId){
                        array_push($bIds, $v['brandId']);
                        array_push($brandNames, $v['brandName']);
                    }
        		}
                $selector[] = ['id'=>join(',',$bIds),'type'=>'brand','label'=>"品牌","val"=>join('、',$brandNames)];
            }
        }
        // 当前是否有品牌筛选
        if(!empty($selector)){
            $_s[] = $selector[count($selector)-1];
            $selector = $_s;
            unset($data['brandFilter']);
        }
        $data['brandId'] = Input('brand');

    	//处理价格
    	if($data['minPrice']!='' && $data['maxPrice']!=''){
    		$selector[] = ['id'=>0,'type'=>'price','label'=>"价格","val"=>$data['minPrice']."-".$data['maxPrice']];
    	}
        if($data['minPrice']!='' && $data['maxPrice']==''){
        	$selector[] = ['id'=>0,'type'=>'price','label'=>"价格","val"=>$data['maxPrice']."以上"];
    	}
        if($data['minPrice']=='' && $data['maxPrice']!=''){
        	$selector[] = ['id'=>0,'type'=>'price','label'=>"价格","val"=>"0-".$data['maxPrice']];
    	}
    	//处理已选属性
        $at = new AT();
    	$goodsFilter = $at->listQueryByFilter($catId);
		$ngoodsFilter = [];
		// 完整的属性
		$fullAttrs = [];
        if(!empty($vs)){
            // 存在筛选条件,取出符合该条件的商品id,根据商品id获取可选属性进行拼凑
            $goodsId = model('goods')->filterByAttributes();
                // 如果同时有筛选品牌,则与品牌下的商品Id取交集
            if(!empty($bgIds))$goodsId = array_intersect($bgIds,$goodsId);


            $fullAttrs = $attrs = model('Attributes')->getAttribute($goodsId);
            // 去除已选择属性
            foreach ($attrs as $key =>$v){
                if(!in_array($v['attrId'],$vs))$ngoodsFilter[] = $v;
            }
        }else{
			if(!empty($bgIds))$goodsFilter = model('Attributes')->getAttribute($bgIds);// 存在品牌筛选
			$fullAttrs = $goodsFilter;
            // 当前无筛选条件,取出分类下所有属性
        	foreach ($goodsFilter as $key =>$v){
        		if(!in_array($v['attrId'],$vs))$ngoodsFilter[] = $v;
            }
        }
        if(count($vs)>0){
            $_vv = [];
			$_attrArr = [];
			$_arr = array_merge($goodsFilter, $fullAttrs);
            foreach ($_arr as $key =>$v){
                if(in_array($v['attrId'],$vs)){
                    foreach ($v['attrVal'] as $key2 =>$vv){
                        if(strstr(input('v_'.$v['attrId']),'、')!==false){
                            $attrvs = explode('、',input('v_'.$v['attrId']));
                            foreach($attrvs as $av){
                               if($av==$vv){
                                  array_push($_vv, $vv);
                                  $_attrArr[$v['attrId']]['attrName'] = $v['attrName'];
                                  $_attrArr[$v['attrId']]['val'] = $_vv;
                               }
                            }
                        }else{
                            if(input('v_'.$v['attrId'])==$vv){
                                $_attrArr[$v['attrId']]['attrName'] = $v['attrName'];
								$_attrArr[$v['attrId']]['val'][] = $vv;
								$_attrArr[$v['attrId']]['val'] = array_unique($_attrArr[$v['attrId']]['val']);
                            }
                        }
                    }
                    $_vv = [];
                }
            }
            foreach($_attrArr as $k1=>$v1){
                $selector[] = ['id'=>$k1,'type'=>'v_'.$k1,'label'=>$v1['attrName'],"val"=>implode('、',$v1['val'])];
            }
        }
    	$data['selector'] = $selector;
        $attrs = [];
        foreach ($ngoodsFilter as $k => $val) {
           $result = array_unique($ngoodsFilter[$k]['attrVal']);
           $ngoodsFilter[$k]['attrVal'] = $result;
        }
    	$data['goodsFilter'] = $ngoodsFilter;
    	//获取商品记录
    	$m = new M();
    	$data['priceGrade'] = $m->getPriceGrade($goodsCatIds);
    	$data['goodsPage'] = $m->pageQuery($goodsCatIds);
        $catPaths = model('goodsCats')->getParentNames($catId);

        $data['catNamePath'] = '全部商品分类';
        if(!empty($catPaths))$data['catNamePath'] = implode(' - ',$catPaths);
    	return $this->fetch($catInfo['catListTheme']?$catInfo['catListTheme']:'goods_list',$data);
    }
    
    /**
     * 查看商品详情
     */
    public function detail(){
    	$m = new M();
    	$goods = $m->getBySale(input('goodsId/d',0));
    	if(!empty($goods)){
    	    $history = cookie("history_goods");
    	    $history = is_array($history)?$history:[];
            array_unshift($history, (string)$goods['goodsId']);
            $history = array_values(array_unique($history));
            
			if(!empty($history)){
				cookie("history_goods",$history,25920000);
			}
            // 分类信息
            $catInfo = Db::name("goods_cats")->field("detailTheme")->where(['catId'=>$goods['goodsCatId'],'dataFlag'=>1])->find();

            // 商品详情延迟加载
            $rule = '/<img src="\/(upload.*?)"/';
            preg_match_all($rule, $goods['goodsDesc'], $images);
            foreach($images[0] as $k=>$v){
                $goods['goodsDesc'] = str_replace($v, "<img class='goodsImg' data-original=\"".str_replace('/index.php','',request()->root())."/".WSTImg($images[1][$k],3)."\"", $goods['goodsDesc']);
            }
	    	$this->assign('goods',$goods);
            $this->assign('shop',$goods['shop']);
	    	return $this->fetch($catInfo['detailTheme']);
    	}else{
    		return $this->fetch("error_lost");
    	}
    }

    
	/**
	 * 获取商品浏览记录
	 */
	public function historyByGoods(){
		$rs = model('Tags')->historyByGoods(8);
		return WSTReturn('',1,$rs);
	}
	/**
	 *  记录对比商品
	 */
	public function contrastGoods(){
		$id = (int)input('post.id');
		$contras = cookie("contras_goods");
		if($id>0){
			$m = new M();
			$goods = $m->getBySale($id);
			$catId = explode('_',$goods['goodsCatIdPath']);
			$catId = $catId[0];
			if(isset($contras['catId']) && $catId!=$contras['catId'])return WSTReturn('请选择同分类商品进行对比',-1);
			if(isset($contras['list']) && count($contras['list'])>3)return WSTReturn('商品对比栏已满',-1);
			if(!isset($contras['catId']))$contras['catId'] = $catId;
			$contras['list'][$id] = $id;
			cookie("contras_goods",$contras,25920000);
		}
		if(isset($contras['list'])){
			$m = new M();
			$list = [];
			foreach($contras['list'] as $k=>$v){
				$list[] = $m->getBySale($v);
			}
			return WSTReturn('',1,$list);
		}else{
			return WSTReturn('',1);
		}
	}
	/**
	 *  删除对比商品
	 */
	public function contrastDel(){
		$id = (int)input('post.id');
		$contras = cookie("contras_goods");
		if($id>0 && isset($contras['list'])){
			unset($contras['list'][$id]);
			cookie("contras_goods",$contras,25920000);
		}else{
			cookie("contras_goods", null);
		}
		return WSTReturn('删除成功',1);
	}
	/**
	 *  商品对比
	 */
	public function contrast(){
		$contras = cookie("contras_goods");
		$list = [];
		$list = $lists= $saleSpec = $shop = $score = $brand = $spec = [];
		if(isset($contras['list'])){
			$m = new M();
			foreach($contras['list'] as $key=>$value){
				$dara = $m->getBySale($value);
				if(isset($dara['saleSpec'])){
					foreach($dara['saleSpec'] as $ks=>$vs){
						if($vs['isDefault']==1){
							$dara['defaultSpec'] = $vs;
							$dara['defaultSpec']['ids'] = explode(':',$ks);
						}
					}
					$saleSpec[$value] = $dara['saleSpec'];
				}
				$list[] = $dara;
			}
			//第一个商品信息
			$goods = $list[0];
			//对比处理
			$shops['identical'] = $scores['identical'] = $brands['identical'] = 1;
			foreach($list as $k=>$v){
				$shop[$v['goodsId']] = $v['shop']['shopName'];
				if($goods['shop']['shopId']!=$v['shop']['shopId'])$shops['identical'] = 0;
				$score[$v['goodsId']] = $v['scores']['totalScores'];
				if($goods['scores']['totalScores']!=$v['scores']['totalScores'])$scores['identical'] = 0;
				$brand[$v['goodsId']] = $v['brandName'];
				if($goods['brandId']!=$v['brandId'])$brands['identical'] = 0;
				if(isset($v['spec'])){
					foreach($v['spec'] as $k2=>$v2){
						$spec[$k2]['identical'] = 0;
						$spec[$k2]['type'] = 'spec';
						$spec[$k2]['name'] = $v2['name'];
						$spec[$k2]['catId'] = $k2;
						foreach($v2['list'] as $ks22=>$vs22){
							$v['spec'][$k2]['list'][$ks22]['isDefault'] = (in_array($vs22['itemId'],$v['defaultSpec']['ids']))?1:0;
						}
						$spec[$k2]['info'][$v['goodsId']] = $v['spec'][$k2];
					}
				}
			}
			$shops['name'] = '店铺';
			$shops['type'] = 'shop';
			$shops['info'] =  $shop;
			$lists[] = $shops;
			$scores['name'] = '商品评分';
			$scores['type'] = 'score';
			$scores['info'] =  $score;
			$lists[] = $scores;
			$brands['name'] = '品牌';
			$brands['type'] = 'brand';
			$brands['info'] =  $brand;
			$lists[] = $brands;
			foreach($spec as $k3=>$v3){
				$lists[] = $v3;
			}
		}
		$data['list'] = $list;
		$data['lists'] = $lists;
		$data['saleSpec'] = $saleSpec;
		$this->assign('data',$data);
		return $this->fetch("goods_contrast");
	}
}
