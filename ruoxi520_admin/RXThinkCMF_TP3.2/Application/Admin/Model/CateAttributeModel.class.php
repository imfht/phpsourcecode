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
 * 分类属性-模型
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class CateAttributeModel extends CBaseModel {
    function __construct() {
        parent::__construct('category_attribute');
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

            //属性类型
            $info['type_name'] = C("CATEGORY_ATTRIBUTE")[$info['type']];
            
            //所属分类
            if($info['category_id']) {
                $cateMod = new CateModel();
                $cateInfo = $cateMod->getInfo($info['category_id']);
                $info['category_name'] = $cateInfo['name'];
            }
            
        }
        return $info;
    }
    
}