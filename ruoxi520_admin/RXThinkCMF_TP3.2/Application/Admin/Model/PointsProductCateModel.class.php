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
 * 积分商品分类-模型
 * 
 * @author 牧羊人
 * @date 2019-01-10
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class PointsProductCateModel extends CBaseModel {
    function __construct() {
        parent::__construct('category');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2019-01-10
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //分类图片
            if($info['icon']) {
                $info['icon_url'] = IMG_URL . $info['icon'];
            }
            
        }
        return $info;
    }
    
}