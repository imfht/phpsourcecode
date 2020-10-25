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
 * 布局-服务类
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\LayoutModel;
class LayoutService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new LayoutModel();
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
        
        //TODO...
        
        return parent::getList();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-20
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $image = trim($data['image']);
        if(!$data['id'] && !$image) {
            return message('请上传封面',false);
        }
        
        if(strpos($image, "temp")) {
            $data['image'] = \Zeus::saveImage($image, 'layout');
        }
        
        return parent::edit($data);
    }
    
}