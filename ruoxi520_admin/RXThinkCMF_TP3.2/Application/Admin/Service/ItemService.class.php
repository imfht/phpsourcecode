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
 * 站点-服务类
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\ItemModel;
class ItemService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new ItemModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
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
     * @date 2018-067-20
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $image = trim($data['image']);
        $data['is_domain'] = (isset($data['is_domain']) && $data['is_domain']=="on") ? 1 : 2;
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        
        if(!$data['id'] && !$image) {
            return message('请上传站点图片',false);
        }
        //图片处理
        if(strpos($image, "temp")) {
            $data['image'] = \Zeus::saveImage($image,'item');
        }
        
        return parent::edit($data);
    }
    
}