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
 * 
 * @author 牧羊人
 * @date 2018-10-08
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\OrderItemModel;
class OrderItemService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new OrderItemModel();
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
        $param = I("request.");
        
        $map = [];
        //查询条件
        $keywords = trim($param['keywords']);
        if($keywords) {
            $map['name'] = array('like',"%{$keywords}%");
        }
        
        return parent::getList($map);
    }
    
}