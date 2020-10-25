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
 * 商品附件-服务类
 * 
 * @author 牧羊人
 * @date 2018-12-21
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\ProductFileModel;
class ProductFileService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new ProductFileModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-12-21
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        //商品ID
        $productId = (int)$param['product_id'];
        
        $map = [
            'product_id'=>$productId,
        ];
        
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
     * @date 2018-12-21
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        
        if(!$data['product_id']) {
            return message("商品ID不能为空", false);
        }
        
        // 文件地址
        $file = $data['file'];
        if(!$file) {
            return message('请上传文件', false);
        }
        if(strpos($file, "temp")) {
            $data['file'] = \Zeus::saveImage($file, 'product');
        }
        
        return parent::edit($data);
        
    }
    
}