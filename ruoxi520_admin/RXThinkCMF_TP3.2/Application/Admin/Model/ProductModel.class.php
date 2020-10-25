<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 商品-模型
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class ProductModel extends CBaseModel {
    function __construct() {
        parent::__construct('product');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-16
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //商品封面
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }
            
            //商品内容
            if($info['content']) {
                while(strstr($info['content'],"[IMG_URL]")){
                    $info['content'] = str_replace("[IMG_URL]", IMG_URL, $info['content']);
                }
            }
            
            //标签
            if($info['tags_id']) {
                $tagsMod = new TagsModel();
                $tagsInfo = $tagsMod->getInfo($info['tags_id']);
                $info['tags_name'] = $tagsInfo['name'];
            }
            
            //品牌
            if($info['brand_id']) {
                $brandMod = new BrandModel();
                $brandInfo = $brandMod->getInfo($info['brand_id']);
                $info['brand_name'] = $brandInfo['name'];
            }
            
            //商品价格
            if($info['price']) {
                $info['format_price'] = \Zeus::formatToYuan($info['price']);
            }
            
            //商品图集
            if($info['image']) {
                $imgsList =  unserialize($info['image']);
                foreach ($imgsList as &$row) {
                    $row = IMG_URL . $row;
                }
                $info['imageList'] = $imgsList;
            }
            
            //商品上下架
            $info['is_sale_name'] = C('PRODUCT_IS_SALE')[$info['is_sale']];
            
            //商品分类
            if($info['category_id']) {
                $cateMod = new CateModel();
                $cateList = $cateMod->where([
                    'id'=>array('in', $info['category_id']),
                    'mark'=>1,
                ])->select();
                $info['category_list'] = $cateList;
            }
            
            // 阶梯报价
            if($info['ladder_price']) {
                $priceList = unserialize($info['ladder_price']);
                foreach ($priceList as &$val) {
                    $val['price'] = $val['price']/1000000;
                }
                $info['priceList'] = $priceList;
            }
            
            //商品属性
            $productAttributeRelationMod = new ProductAttributeRelationModel();
            $attributeList = $productAttributeRelationMod->where(['product_id'=>$id,'mark'=>1])->select();
            $info['attribute_list'] = $attributeList;
            if($attributeList) {
                $info['attribute_id'] = implode(',', array_key_value($attributeList,'category_attribute_value_id'));
            }
            
            // 商品详情页
            $info['detail_url'] = "http://www.baidu.com";//SITE_URL . "/Product/detail?id={$info['id']}";
            
        }
        return $info;
    }
    
}