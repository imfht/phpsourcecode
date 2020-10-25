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
 * 商品-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\ProductModel;
use Admin\Model\ProductAttributeModel;
use Admin\Model\CateAttributeModel;
use Admin\Model\CateAttributeValueModel;
use Admin\Model\ProductSkuModel;
use Admin\Model\ProductImageModel;
use Admin\Model\ProductCateRelationModel;
use Admin\Model\CateModel;
use Admin\Model\ProductAttributeRelationModel;
class ProductService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new ProductModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-19
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //商品名称
        $name = trim($param['name']);
        if($name) {
            $map['name'] = array('like',"%{$name}%");
        }
        
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-10-19
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $data['is_sale'] = (isset($data['is_sale']) && $data['is_sale']=="on") ? 1 : 2;
        
        //商品单价
        $data['price'] = $data['price']*100;
        
        //商品分类
        $cateStr = $data['category_id'];
        if(!$cateStr) {
            return message('请选择商品分类', false);
        }
        
        //属性分类
        $attrStr = $data['attribute_id'];
        if(!$attrStr) {
            return message('请选择商品属性', false);
        }
        
        //商品封面
        $cover = trim($data['cover']);
        if(strpos($cover, "temp")) {
            $data['cover'] = \Zeus::saveImage($cover, 'product');
        }
        
        //图集处理
        $imgsList = trim($data['image']);
        if($imgsList) {
            $imgArr = explode(',', $imgsList);
            foreach ($imgArr as $key => $val) {
                if(strpos($val, "temp")) {
                    //新上传图片
                    $imgStr[] = \Zeus::saveImage($val, 'product');
                }else{
                    //过滤已上传图片
                    $imgStr[] = str_replace(IMG_URL, "", $val);
                }
            }
        }
        $data['image'] = serialize($imgStr);
        
        //商品详情
        \Zeus::saveImageByContent($data['content'],$data['name'],"product");

        //开启事务
        $this->mod->startTrans();

        $error = '';
        $rowId = $this->mod->edit($data, $error);
        if(!$rowId) {
            //事务回滚
            $this->mod->rollback();
            return message($error,false);
        }
        
        //商品分类附表处理
        $productCateRelationMod = new ProductCateRelationModel();
        
        //获取当前商品所有分类
        $cateList = $productCateRelationMod->where(['product_id'=>$rowId])->getField('id', true);
        if($cateList) {
            foreach ($cateList as $val) {
                $productCateRelationMod->drop($val);
            }
        }
        
        //写入新的数据
        $cateArr = explode(',', $cateStr);
        if($cateArr) {
            $cateMod = new CateModel();
            foreach ($cateArr as $vt) {
                $cateInfo = $cateMod->getInfo($vt);
                if(!$cateInfo) continue;
                
                $info = $productCateRelationMod->where([
                    'product_id'=>$rowId,
                    'category_id'=>$vt,
                ])->find();
                if($info) {
                    //恢复被删除的数据
                    $productCateRelationMod->where(['id'=>$info['id']])->setField('mark',1);
                }else{
                    //创建新的数据
                    $item = [
                        'product_id'=>$rowId,
                        'p_category_id'=>(int)$cateInfo['parent_id'],
                        'category_id'=>$vt,
                    ];
                    $productCateRelationMod->edit($item);
                }
            }
        }
        
        //商品属性业务处理
        $productAttrRelationMod = new ProductAttributeRelationModel();
        $attrList = $productAttrRelationMod->where(['product_id'=>$rowId])->getField('id', true);
        if($attrList) {
            foreach ($attrList as $val) {
                $productAttrRelationMod->drop($val);
            }
        }
        //写入新数据
        $attrArr = explode(',', $attrStr);
        if($attrArr) {
            $cateAttrValueMod = new CateAttributeValueModel();
            foreach ($attrArr as $vt) {
                $attrValueInfo = $cateAttrValueMod->getInfo($vt);
                if(!$attrValueInfo) continue;
        
                $info = $productAttrRelationMod->where([
                    'product_id'=>$rowId,
                    'category_attribute_value_id'=>$vt,
                ])->find();
                if($info) {
                    //恢复被删除的数据
                    $productAttrRelationMod->where(['id'=>$info['id']])->setField('mark',1);
                }else{
                    //创建新的数据
                    $item = [
                        'product_id'=>$rowId,
                        'category_attribute_id'=>(int)$attrValueInfo['category_attribute_id'],
                        'attr_name'=>$attrValueInfo['category_attribute_name'],
                        'category_attribute_value_id'=>$vt,
                        'attr_value'=>$attrValueInfo['attribute_value'],
                    ];
                    $productAttrRelationMod->edit($item);
                }
            }
        }

        //提交事务
        $this->mod->commit();
        
        return message();
        
    }
    
    /**
     * 设置产品规格状态
     * 
     * @author 牧羊人
     * @date 2018-10-25
     */
    function setIsSpec() {
        $data = I('post.', '', 'trim');
        $productId = (int)$data['product_id'];
        $is_spec = (int)$data['is_spec'];
        if(!$productId) {
            return message('商品ID不能为空',false);
        }
        if(!$is_spec) {
            return message('规格状态不能为空',false);
        }
        
        $data = [
            'id'=>$productId,
            'is_spec'=>$is_spec,
        ];
        $rowId = $this->mod->edit($data);
        if($rowId) {
            return message();
        }
        return message('规格状态设置失败',false);
        
    }
    
    /**
     * 产品规格管理
     * 
     * @author 牧羊人
     * @date 2018-10-24
     */
    function productModel() {
        $data = I('post.', '', 'trim');
        $productId = (int)$data['product_id'];
        $result = json_decode($data['result'],true);
        
        //商品属性处理
        $cateAttrMod = new CateAttributeModel();
        $cateAttrValueMod = new CateAttributeValueModel();
        $productAttrMod = new ProductAttributeModel();
        $productSkuMod = new ProductSkuModel();
        
        //开启事务
        $this->mod->startTrans();
        
        //删除商品属性
        $attrList = $productAttrMod->where([
            'product_id'=>$productId,
            'mark'=>1,
        ])->select();
        if(is_array($attrList)) {
            foreach ($attrList as $attr) {
                if(!$productAttrMod->drop($attr['id'])) {
                    //事务回滚
                    $this->mod->rollback();
                    return message("商品属性删除失败",false);
                    break;
                }
            }
        }
        
        //删除商品SKU
        $skuList = $productSkuMod->where([
            'product_id'=>$productId,
            'mark'=>1,
        ])->select();
        if(is_array($skuList)) {
            foreach ($skuList as $sku) {
                if(!$productSkuMod->drop($sku['id'])) {
                    //事务回滚
                    $this->mod->rollback();
                    return message("商品SKU删除失败",false);
                    break;
                }
            }
        }
        
        //SKU数据处理
        if(is_array($result)) {
            foreach ($result as $val) {
                $itemArr = [];
                foreach ($val['ids'] as $vt) {
                    foreach ($vt as $k=>$v) {
                        
                        $item = [
                            'product_id'=>$productId,
                            'category_attribute_id'=>$k,
                            'category_attribute_value_id'=>$v,
                        ];
                        $attrInfo = $productAttrMod->where($item)->find();
                        if(!$attrInfo) {
                            $cateAttrInfo = $cateAttrMod->getInfo($k);
                            $cateAttrValueInfo = $cateAttrValueMod->getInfo($v);
                            $item['attr_name'] = $cateAttrInfo['name'];
                            $item['attr_value'] = $cateAttrValueInfo['attribute_value'];
                        }else{
                            $item['id'] = $attrInfo['id'];
                        }
                        $item['mark'] = 1;
                        $attributeId = $productAttrMod->edit($item);
                        if(!$attributeId) {
                            //事务回滚
                            $this->mod->rollback();
                            return message("商品属性添加失败",false);
                            break;
                        }
                        $itemArr[] = $attributeId;
                        
                    }
                }
                
                //更新SKU
                $skuInfo = $productSkuMod->where([
                    'product_id'=>$productId,
                    'product_attr_ids'=>implode('_', $itemArr),
                ])->find();
                $data = [
                    'id'=>$skuInfo['id'],
                    'product_id'=>$productId,
                    'product_attr_ids'=>implode('_', $itemArr),
                    'price'=>$val['price']*100,
                    'stock'=>$val['stock'],
                    'is_default'=>$val['is_default'],
                    'mark'=>1,
                ];
                $res = $productSkuMod->edit($data);
                if(!$res) {
                    //事务回滚
                    $this->mod->rollback();
                    return message("商品SKU添加失败",false);
                }
            }
        }
        
        //提交事务
        $this->mod->commit();
        
        return message("商品规格更新成功",true);
    }
    
    /**
     * 获取产品SKU列表
     * 
     * @author 牧羊人
     * @date 2018-10-25
     */
    function getSkuList($productId) {
        $productAttrMod = new ProductAttributeModel();
        $productSkuMod = new ProductSkuModel();
        $result = $productSkuMod->where([
            'product_id'=>$productId,
            'mark'=>1,
        ])->order("id ASC")->select();
        $list = [];
        if(is_array($result)) {
            $data = [];
            foreach ($result as $val) {
                $cateArr = explode('_', $val['product_attr_ids']);
                $itemArr = [];
                foreach ($cateArr as $vt) {
                    $attrInfo = $productAttrMod->getInfo($vt);
                    $itemArr[] = [
                        $attrInfo['category_attribute_id']=>$attrInfo['category_attribute_value_id'],
                    ];
                }
                
                $data = [
                    'ids'=>$itemArr,
                    'id'=>$val['id'],
                    'price'=>\Zeus::formatToYuan($val['price']),
                    'stock'=>$val['stock'],
                    'sku'=>0,
                ];
                $list[] = $data;
            }
        }
        return $list;
    }
    
    /**
     * SKU图集
     * 
     * @author 牧羊人
     * @date 2018-11-01
     */
    function skuImgs() {
        $data = I('post.', '', 'trim');
        $sku_id = (int)$data['sku_id'];
        
        //获取商品ID
        $skuMod = new ProductSkuModel();
        $skuInfo = $skuMod->getInfo($sku_id);
        if(!$skuInfo) {
            return message('商品SKU信息不存在',false);
        }
        $data['product_id'] = $skuInfo['product_id'];
        
        //SKU图集处理
        $imgsList = trim($data['imgs']);
        if($imgsList) {
            $imgArr = explode(',', $imgsList);
            foreach ($imgArr as $key => $val) {
                if(strpos($val, "temp")) {
                    //新上传图片
                    $imgStr[] = \Zeus::saveImage($val, 'product');
                }else{
                    //过滤已上传图片
                    $imgStr[] = str_replace(IMG_URL, "", $val);
                }
            }
        }
        $data['imgs'] = serialize($imgStr);
        
        $productImageMod = new ProductImageModel();
        $error = '';
        $result = $productImageMod->edit($data,$error);
        if($result) {
            return message();
        }
        return message($error,false);
        
    }
    
    /**
     * 阶梯报价
     * 
     * @author 牧羊人
     * @date 2018-12-24
     */
    function ladderPrice() {
        $data = I('post.', '', 'trim');
        // 商品ID
        $productId = (int)$data['product_id'];
        if(!$productId) {
            return message('商品ID不能为空', false);
        }
        // 最小值数组
        $min_num = $data['min_num'];
        if(!$min_num || !is_array($min_num)) {
            return message('最小值不能为空', false);
        }
        // 最大值数组
        $max_num = $data['max_num'];
        if(!$max_num || !is_array($max_num)) {
            return message('最大值不能为空', false);
        }
        // 阶梯报价价格
        $price = $data['price'];
        if(!$price || !is_array($price)) {
            return message('阶梯报价价格不能为空', false);
        }
        
        $totalNum = count($min_num);
        $list = [];
        
        // 最低单价留存，专为搜索使用
        $minPrice = 0;
        for ($i=0; $i<$totalNum; $i++) {
            // 最小值
            $minValue = (int)$min_num[$i];
            if(!$minValue) {
                continue;
            }
            // 最大值
            $maxValue = (int)$max_num[$i];
            if(!$maxValue && $i<$totalNum-1) {
                continue;
            }
            // 单价
            $amount = strval($price[$i])*1000000;
            if($i==0) {
                $minPrice = $amount;
            }
            $subItem = [
                'min_num'=>$min_num[$i],
                'max_num'=>$max_num[$i],
                'price'=>$amount,
            ];
            if($i==$totalNum-1 && !$maxValue) {
                $subItem['is_ladder'] = 2;
            }else{
                $subItem['is_ladder'] = 1;
            }
            $list[] = $subItem;
        }
        if($totalNum!=count($list)) {
            return message("数据异常", false);
        }
        $item = [
            'id'=>$productId,
            'ladder_price'=>serialize($list),
            'max_ladder_price'=>$minPrice,
        ];
        $error = '';
        $result = $this->mod->edit($item,$error);
        if($result) {
            return message();
        }
        return message($error, false);
        
    }
    
    /**
     * 选择属性
     * 
     * @author 牧羊人
     * @date 2018-12-26
     */
    function attrSelect() {
        $attribute_id = I('get.attribute_id', '');
        $itemArr = [];
        if($attribute_id) {
            $itemArr = explode(',', $attribute_id);
        }
        $list = [];
        $cateAttrMod = new CateAttributeModel();
        $cateAttrValueMod = new CateAttributeValueModel();
        $result = $cateAttrMod->where(['type'=>1,'status'=>1,'mark'=>1])->getField('id', true);
        if($result) {
            foreach ($result as $val) {
                $info = $cateAttrMod->getInfo($val);
                if(!$info) continue;
        
                // 获取子级
                $item = [];
                $childList = $cateAttrValueMod->where([
                    'category_attribute_id'=>$val,
                    'status'=>1,
                    'mark'=>1,
                ])->getField('id',true);
                if($childList) {
                    foreach ($childList as $vt) {
                        $info2 = $cateAttrValueMod->getInfo($vt);
                        if(!$info2) continue;
                        $item[] = [
                            'id'=>$info2['id'],
                            'name'=>$info2['attribute_value'],
                            'selected'=>in_array($info2['id'], $itemArr),
                        ];
                    }
                }
        
                $list[] = [
                    'id'=>$info['id'],
                    'name'=>$info['name'],
                    'list'=>$item,
                ];
        
            }
        }
        return $list;
    }
    
}