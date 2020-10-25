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
 * 组织机构-服务类
 * 
 * @author 牧羊人
 * @date 2018-07-23
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\AdminOrgModel;
class AdminOrgService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new AdminOrgModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-07-24
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::getList()
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
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-24
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $logo = trim($data['logo']);
        
        //LOGO
        if(strpos($logo, "temp")) {
            $data['logo'] = \Zeus::saveImage($logo, 'adminOrg');
        }
        return parent::edit($data);
    }
    
}