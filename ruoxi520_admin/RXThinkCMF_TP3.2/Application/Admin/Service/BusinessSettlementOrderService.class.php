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
 * 商家结算申请单-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-24
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\BusinessSettlementOrderModel;
class BusinessSettlementOrderService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new BusinessSettlementOrderModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-01-07
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //订单状态
        $status = (int)$param['status'];
        if($status) {
            $map['status'] = $status;
        }
        
        //手机号码
        $mobile = trim($param['mobile']);
        if($mobile) {
            $map['mobile'] = array('like',"%{$mobile}%");
        }
        
        return parent::getList($map);
    }
    
    /**
     * 订单确认
     * 
     * @author 牧羊人
     * @date 2018-10-24
     */
    function confirmOrder() {
        $data = I('post.', '', 'trim');
        if(!$data['id']) {
            return message('订单信息不存在',false);
        }
        return parent::edit();
    }
    
}