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
 * 商家-服务
 * 
 * @author 牧羊人
 * @date 2018-10-19
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\BusinessModel;
use Admin\Model\AdminModel;
class BusinessService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new BusinessModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-19
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //审核状态
        $check_status = (int)$param['check_status'];
        if($check_status) {
            $map['check_status'] = $check_status;
        }
        
        //查询条件
        $keywords = trim($param['keywords']);
        if($keywords) {
            $map['name'] = array('like',"%{$keywords}%");
        }
        
        return parent::getList($map);
    }
    
    /**
     * 商家升级审核
     * 
     * @author 牧羊人
     * @date 2019-01-04
     */
    function checkStatus() {
        $data = I('post.', '', 'trim');
        if(!$data['id']) {
            return message("申请资料ID不能为空", false);
        }
        $data['check_status'] = (int)$data['check_status'];
        
        //开启事务
        $this->mod->startTrans();
        
        $error = '';
        $result = $this->mod->edit($data, $error);
        if(!$result) {
            //事务回滚
            $this->mod->rollback();
            return message($error, false);
        }

        //审核通过创建供应商后台登录账号
        if($data['check_status']==2) {
            $info = $this->mod->getInfo($data['id']);
            if(!$info) {
                //事务回滚
                $this->mod->rollback();
                return message("供应商信息不能为空", false);
            }
            $item = [
                'realname'=>$info['realname'],
                'avatar'=>$info['logo'],
                'mobile'=>$info['mobile'],
                'identity'=>$info['identity_num'],
                'gender'=>$info['gender'],
                'province_id'=>$info['province_id'],
                'city_id'=>$info['city_id'],
                'district_id'=>$info['district_id'],
                'user_type'=>2,
                'user_id'=>$info['user_id'],
                'username'=>$info['mobile'],
            ];
            
            //判断账号是否已经存在
            $adminMod = new AdminModel();
            $adminInfo = $adminMod->getRowByAttr([
                'user_type'=>2,
                'user_id'=>$info['user_id'],
            ]);
            $item['id'] = (int)$adminInfo['id'];
            $rowId = $adminMod->edit($item, $error);
            if(!$rowId) {
                //事务回滚
                $this->mod->rollback();
                return message($error, false);
            }
        }
        
        //提交事务
        $this->mod->commit();
        
        return message();
        
    }
    
}