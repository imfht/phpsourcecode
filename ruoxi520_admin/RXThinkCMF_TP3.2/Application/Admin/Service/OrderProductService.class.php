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
 * 订单产品-服务类
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\OrderProductModel;
class OrderProductService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new OrderProductModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-22
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        //订单ID
        $orderId = (int)$param['order_id'];
        
        $map = [
            'order_id'=>$orderId,
        ];
        
        //商品名称
        $name = trim($param['name']);
        if($name) {
            $map['product_name'] = array('like',"%{$name}%");
        }
        
        return parent::getList($map);
    }
    
}