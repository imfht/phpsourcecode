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
 * 属性值-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\CateAttributeValueModel;
class CateAttributeValueService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new CateAttributeValueModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-25
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //属性值名称
        $attribute_value = trim($param['attribute_value']);
        if($attribute_value) {
            $map['attribute_value'] = array('like',"%{$attribute_value}%");
        }
        
        //属性ID
        $category_attribute_id = (int)$param['category_attribute_id'];
        if($category_attribute_id) {
            $map['category_attribute_id'] = $category_attribute_id;
        }
        
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-10-16
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        return parent::edit($data);
    }
    
}