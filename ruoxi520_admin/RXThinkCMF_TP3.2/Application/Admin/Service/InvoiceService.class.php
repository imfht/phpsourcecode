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
 * 发票-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\InvoiceModel;
use Admin\Model\UserModel;
class InvoiceService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new InvoiceModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-18
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //手机号码
        $mobile = trim($param['mobile']);
        if($mobile) {
            $userMod = new UserModel();
            $userInfo = $userMod->getRowByAttr([
                'mobile'=>$mobile,
            ]);
            $map['user_id'] = $userInfo['id'];
        }
        
        //发票抬头
        $invoice_head = trim($param['invoice_head']);
        if($invoice_head) {
            $map['invoice_head'] = $invoice_head;
        }
        
        //类型
        $type = (int)$param['type'];
        if($type) {
            $map['type'] = $type;
        }
        
        //状态
        $status = (int)$param['status'];
        if($status) {
            $map['status'] = $status;
        }
        
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-10-18
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        $data['receive_province_id'] = (int)$data['province_id'];
        $data['receive_city_id'] = (int)$data['city_id'];
        $data['receive_district_id'] = (int)$data['district_id'];
        return parent::edit($data);
    }
    
    
}