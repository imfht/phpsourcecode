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
 * 部门-服务类
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\AdminDepModel;
class AdminDepService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new AdminDepModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-07-20
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::getList()
     */
    function getList() {
        $list = $this->mod->getChilds(0,1);
        return message('操作成功',true,$list);
    }
    
}