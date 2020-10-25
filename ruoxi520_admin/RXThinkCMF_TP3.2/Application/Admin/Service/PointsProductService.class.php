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
 * 积分商城-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\PointsProductModel;
class PointsProductService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new PointsProductModel();
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
        
        //商品名称
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
     * @date 2018-10-19
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');

        //商品封面
        $cover = trim($data['cover']);
        if(strpos($cover, "temp")) {
            $data['cover'] = \Zeus::saveImage($cover, 'pointsProduct');
        }
        
        //商品详情
        \Zeus::saveImageByContent($data['intro'],$data['name'],"pointsProduct");
        
        return parent::edit($data);
    }
    
}