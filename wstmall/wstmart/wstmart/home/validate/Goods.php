<?php
namespace wstmart\home\validate;
use think\Validate;
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
 * 商品验证器
 */
class Goods extends Validate{
	protected $rule = [
		'goodsName' => 'require|max:300',
		'goodsImg' => 'require',
		'goodsType' => 'in:,0,1',
		'goodsSn' => 'checkGoodsSn:1',
		'productNo' => 'checkProductNo:1',
		'marketPrice' => 'checkMarketPrice:1',
		'shopPrice' => 'checkShopPrice:1',
		'goodsUnit' => 'require',
		'isSale' => 'in:,0,1',
		'isRecom' => 'in:,0,1',
		'isBest' => 'in:,0,1',
		'isNew' => 'in:,0,1',
		'isHot' => 'in:,0,1',
		'isFreeShipping' => 'in:,0,1',
		'goodsCatId' => 'require',
		'goodsDesc' => 'require',
		'specsIds' => 'checkSpecsIds:1'
	];
	
	protected $message  =  [
		'goodsName.require' => '请输入商品名称',
		'goodsName.max' => '商品名称不能超过100个字符',
		'goodsImg.require' => '请上传商品图片',
		'goodsType.in' => '无效的商品类型',
		'goodsSn.checkGoodsSn' => '请输入商品编号',
		'productNo.checkProductNo' => '请输入商品货号',
		'marketPrice.checkMarketPrice' => '请输入市场价格',
		'shopPrice.checkShopPrice' => '请输入店铺价格',
		'goodsUnit.require' => '请输入商品单位',
		'isSale.in' => '无效的上架状态',
		'isRecom.in' => '无效的推荐状态',
		'isBest.in' => '无效的精品状态',
		'isNew.in' => '无效的新品状态',
		'isHot.in' => '无效的热销状态',
		'isFreeShipping.in' => '无效的包邮状态',
		'goodsCatId.require' => '请选择完整商品分类',
		'goodsDesc.require' => '请输入商品描述',
		'specsIds.checkSpecsIds' => '请填写完整商品规格信息'
	];
	
	
    /**
     * 检测商品编号
     */
    protected function checkGoodsSn($value){
    	$goodsId = Input('post.goodsId/d',0);
    	$key = Input('post.goodsSn');
    	if($key=='')return '请输入商品编号';
    	$isChk = model('Goods')->checkExistGoodsKey('goodsSn',$key,$goodsId);
    	if($isChk)return '对不起，该商品编号已存在';
    	return true;
    }
    /**
     * 检测商品货号
     */
    protected function checkProductNo($value){
    	$goodsId = Input('post.goodsId/d',0);
    	$key = Input('post.productNo');
    	if($key=='')return '请输入商品货号';
    	$isChk = model('Goods')->checkExistGoodsKey('productNo',$key,$goodsId);
    	if($isChk)return '对不起，该商品货号已存在';
    	return true;
    }
    /**
     * 检测价格
     */
    public function checkMarketPrice(){
        $marketPrice = floatval(input('post.marketPrice'));
        if($marketPrice<0.01)return '市场价格不能小于0.01';
        return true;
    }
    public function checkShopPrice(){
        $shopPrice = floatval(input('post.shopPrice'));
        if($shopPrice<0.01)return '店铺价格不能小于0.01';
        return true;
    }
    /**
     * 检测商品规格是否填写完整
     */
    public function checkSpecsIds(){
    	$specsIds = input('post.specsIds');
    	if($specsIds!=''){
	    	$str = explode(',',$specsIds);
	    	$specsIds = [];
	    	foreach ($str as $v){
	    		$vs = explode('-',$v);
	    		foreach ($vs as $vv){
	    		   if(!in_array($vv,$specsIds))$specsIds[] = $vv;
	    		}
	    	}
    		//检测规格名称是否填写完整
    		foreach ($specsIds as $v){
    			if(input('post.specName_'.$v)=='')return '请填写完整商品规格值';
    		}
    		//检测销售规格是否完整	
    		foreach ($str as $v){
    			if(input('post.productNo_'.$v)=='')return '请填写完整商品销售规格-货号';
                if(input('post.marketPrice_'.$v)=='')return '请填写完整商品销售规格-市场价';
                if(floatval(input('post.marketPrice_'.$v))<0.01)return '商品销售规格-市场价不能小于0.01';
                if(input('post.specPrice_'.$v)=='')return '请填写完整商品销售规格-本店价';
                if(floatval(input('post.specPrice_'.$v))<0.01)return '商品销售规格-本店价不能小于0.01';
                if(input('post.specStock_'.$v)=='')return '请填写完整商品销售规格-库存';
                if(intval(input('post.specStock_'.$v))<0)return '商品销售规格-库存不能小于0';
                if(input('post.warnStock_'.$v)=='')return '请填写完整商品销售规格-预警库存';
                if(intval(input('post.warnStock_'.$v))<0)return '商品销售规格-预警库存不能小于0';
    		}
    		if(input('post.defaultSpec')=='')return '请选择推荐规格';
    	}
    	return true;
    }
}