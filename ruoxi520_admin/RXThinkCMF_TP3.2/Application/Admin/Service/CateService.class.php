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
 * 分类-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-09
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\CateModel;
use Admin\Model\CateAttributeRelationModel;
class CateService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new CateModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-09
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $list = $this->mod->getChilds(0,true);
        return message("操作成功",true,$list);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-10-09
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        
        // 分类图标默认样式
        $icon = trim($data['icon']);
        if(strpos($icon, "temp")) {
            $data['icon'] = \Zeus::saveImage($icon, 'category');
        }
        
        // 分类图片选中样式
        $icon2 = trim($data['icon2']);
        if(strpos($icon2, "temp")) {
            $data['icon2'] = \Zeus::saveImage($icon2, 'category');
        }
        
        return parent::edit($data);
    }
    
    /**
     * 获取分类选择列表
     * 
     * @author 牧羊人
     * @date 2018-12-19
     */
    function cateSelect() {
        $category_id = I('get.category_id', '');
        $itemArr = [];
        if($category_id) {
            $itemArr = explode(',', $category_id);
        }
        $list = [];
        $result = $this->mod->where(['level'=>2,'mark'=>1])->getField('id', true);
        if($result) {
            foreach ($result as $val) {
                $info = $this->mod->getInfo($val);
                if(!$info) continue;

                // 获取子级
                $item = [];
                $childList = $this->mod->getChilds($val);
                if($childList) {
                    foreach ($childList as $vt) {
                        $item[] = [
                            'id'=>$vt['id'],
                            'name'=>$vt['name'],
                            'selected'=>in_array($vt['id'], $itemArr),
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
    
    /**
     * 分类属性
     * 
     * @author 牧羊人
     * @date 2018-12-24
     */
    function cateAttr() {
        $data = I('post.', '', 'trim');
        $categoryId = (int)$data['category_id'];
        $cate_attr_id = $data['cate_attr_id'];
        if(!$categoryId) {
            return message('分类ID不能为空', false);
        }
        if(!$cate_attr_id) {
            return message('请选择分类属性', false);
        }
        $cateAttrRelationMod = new CateAttributeRelationModel();
        
        // 删除现有数据
        $result = $cateAttrRelationMod->where([
            'category_id'=>$categoryId,
        ])->select();
        if($result) {
            foreach ($result as $val) {
                $cateAttrRelationMod->drop($val['id']);
            }
        }
        // 创建数据
        foreach ($cate_attr_id as $key=>$val) {
            $item = [
                'category_id'=>$categoryId,
                'category_attribute_id'=>$key,
            ];
            $info = $cateAttrRelationMod->where($item)->find();
            if($info) {
                // 启用之前的记录
                $cateAttrRelationMod->where(['id'=>$info['id']])->setField('mark',1);
            }else{
                // 创建新的记录
                $cateAttrRelationMod->edit($item);
            }
        }
        return message();
        
    }
    
}