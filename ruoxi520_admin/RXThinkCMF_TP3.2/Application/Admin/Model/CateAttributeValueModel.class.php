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
 * 属性值-模型
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class CateAttributeValueModel extends CBaseModel {
    function __construct() {
        parent::__construct('category_attribute_value');
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
        $info = parent::getInfo($id,true);
        if($info) {
            
            //分类属性
            if($info['category_attribute_id']) {
                $cateAttrMod = new CateAttributeModel();
                $cateAttrInfo = $cateAttrMod->getInfo($info['category_attribute_id']);
                $info['category_attribute_name'] = $cateAttrInfo['name'];
                $info['category_name'] = $cateAttrInfo['category_name'];
            }
            
        }
        return $info;
    }
    
}