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
 * 登录日志-模型
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class AdminLogModel extends CBaseModel {
    function __construct() {
        parent::__construct('admin_log');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-16
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            //TODO...
        }
        return $info;
    }
    
}