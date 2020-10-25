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
 * 订单产品-模型
 * 
 * @author 牧羊人
 * @date 2018-10-08
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class OrderItemModel extends CBaseModel {
    function __construct() {
        parent::__construct('order_item');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-08
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $this->_cacheDelete($id);
        $info = parent::getInfo($id);
        if($info) {
            
            //单价
            if($info['price']) {
                $info['format_price'] = \Zeus::formatToYuan($info['price']);
            }
            
            //总价
            if($info['total_price']) {
                $info['format_total_price'] = \Zeus::formatToYuan($info['total_price']);
            }
            
        }
        return $info;
    }
    
}