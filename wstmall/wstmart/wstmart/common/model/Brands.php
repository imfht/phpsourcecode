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
 * 品牌业务处理类
 */
class Brands extends Base{
	protected $pk = 'brandId';
	/**
	 * 获取品牌列表
	 */
	public function pageQuery($pagesize , $id = 0){
		$id = ($id>0)?$id:(int)input('id');
		$where = [];
		$where['b.dataFlag']=1;
    	$prefix = config('database.prefix');
		$where['gcb.catId']=$id;

		$rs = $this->alias('b')
					   ->join('__CAT_BRANDS__ gcb','gcb.brandId=b.brandId','left')
					   ->where($where)
					   ->field('b.brandId,brandName,brandImg,gcb.catId')
					   ->group('b.brandId,gcb.catId')
					   ->order('b.sortNo asc')
					   ->paginate($pagesize)->toArray();
		return $rs;
	}
	/**
	 * 获取品牌列表
	 */
	public function listQuery($catId){
		$rs = Db::name('cat_brands')->alias('l')
									->join('__BRANDS__ b','b.brandId=l.brandId and b.dataFlag=1 and l.catId='.$catId)
		          					->field('b.brandId,b.brandName,b.brandImg')
		          					->group('b.brandId')
		          					->order('b.sortNo asc')
		          					->select();
		return $rs;
	}

    /**
     * 获取商家申请的品牌列表
     */
    public function shopBrandListQuery($shopId,$catId){
        $rs = Db::name('cat_brands')->alias('l')
            ->join('__BRANDS__ b','b.brandId=l.brandId and b.dataFlag=1 and l.catId='.$catId)
            ->join('__BRAND_APPLYS__ ba','b.brandId=ba.brandId')
            ->where(['ba.shopId'=>$shopId,'ba.applyStatus'=>1,'ba.dataFlag'=>1])
            ->field('b.brandId,b.brandName,b.brandImg')
            ->group('b.brandId')
            ->order('b.sortNo asc')
            ->select();
        return $rs;
    }

	/**
	* 商品筛选品牌查询
	*/
	public function goodsListQuery($catId){
		$rs = Db::name('cat_brands')->alias('l')
									->join('__BRANDS__ b','b.brandId=l.brandId and b.dataFlag=1 and l.catId='.$catId)
									->join('__GOODS__ g','g.brandId=b.brandId','inner')
		          					->field('b.brandId,b.brandName,b.brandImg')
		          					->group('b.brandId')
		          					->order('b.sortNo asc')
		          					->select();
		return $rs;
	}


	/**
	 * 根据商品id获取可供选择的品牌
	 */
	public function canChoseBrands($goodsId){
		$rs = Db::name('cat_brands')->alias('l')
									->join('__BRANDS__ b','b.brandId=l.brandId and b.dataFlag=1')
									->join('__GOODS__ g','g.brandId=b.brandId','inner')
									->where([['g.goodsId','in',$goodsId]])
		          					->field('b.brandId,b.brandName,b.brandImg')
		          					->group('b.brandId')
		          					->order('b.sortNo asc')
		          					->select();
		return $rs;
	}
	/**
	* 根据品牌id获取商品id
	*/
	public function getGoodsIds($brandIds){
		$rs = Db::name('goods')->field('goodsId')->where(['dataFlag'=>1,'isSale'=>1,'goodsStatus'=>1])->where([['brandId','in',$brandIds]])->select();
		if(!empty($rs)){
			$bIds = [];
			foreach($rs as $k=>$v){
				$bIds[$k] = $v['goodsId'];
			}
			return $bIds;
		}
		return [];
	}
}
