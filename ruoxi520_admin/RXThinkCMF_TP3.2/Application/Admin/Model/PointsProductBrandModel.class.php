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
 * 积分商城品牌-模型
 * 
 * @author 牧羊人
 * @date 2019-01-11
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class PointsProductBrandModel extends CBaseModel {
    function __construct() {
        parent::__construct('brand');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @ate 2019-01-11
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //LOGO
            if($info['logo']) {
                $info['logo_url'] = IMG_URL . $info['logo'];
            }
            
            //品牌类型
            if($info['type']) {
                $info['type_name'] = C("BRAND_TYPE")[$info['type']];
            }
            
        }
        return $info;
    }
    
}