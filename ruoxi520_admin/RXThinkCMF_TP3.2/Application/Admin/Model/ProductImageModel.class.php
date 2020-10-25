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
 * 商品SKU图片
 * 
 * @author 牧羊人
 * @date 2018-11-01
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class ProductImageModel extends CBaseModel {
    function __construct() {
        parent::__construct('product_image');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-11-01
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //商品图集
            if($info['imgs']) {
                $imgsList =  unserialize($info['imgs']);
                foreach ($imgsList as &$row) {
                    $row = IMG_URL . $row;
                }
                $info['imgsList'] = $imgsList;
            }
            
        }
        return $info;
    }
    
}