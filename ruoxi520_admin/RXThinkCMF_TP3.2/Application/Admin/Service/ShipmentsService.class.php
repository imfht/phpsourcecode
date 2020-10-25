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
 * 货物物流-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-23
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\ShipmentsModel;
class ShipmentsService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new ShipmentsModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-24
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //快递单号
        $express_no = trim($param['express_no']);
        if($express_no) {
            $map['express_no'] = array('like',"%{$express_no}%");
        }
        
        return parent::getList($map);
    }
    
}