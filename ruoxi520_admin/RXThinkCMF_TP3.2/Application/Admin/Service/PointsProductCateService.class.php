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
 * 积分商品分类-服务类
 * 
 * @author 牧羊人
 * @date 2019-01-10
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\PointsProductCateModel;
class PointsProductCateService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new PointsProductCateModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-01-10
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [
            'parent_id'=>0,
            'type'=>2,
        ];
        
        //查询条件
        $name = trim($param['name']);
        if($name) {
            $map['name'] = array('like',"%{$name}%");
        }
        
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2019-01-10
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        
        // 分类图片
        $icon = trim($data['icon']);
        if(strpos($icon, "temp")) {
            $data['icon'] = \Zeus::saveImage($icon, 'category');
        }
        
        return parent::edit($data);
    }
    
}