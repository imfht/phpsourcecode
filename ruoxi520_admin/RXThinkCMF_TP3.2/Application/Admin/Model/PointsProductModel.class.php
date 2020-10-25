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
 * 积分商城-模型
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class PointsProductModel extends CBaseModel {
    function __construct() {
        parent::__construct('points_product');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-16
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //商品封面
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }
            
            //品牌
            if($info['brand_id']) {
                $brandMod = new BrandModel();
                $brandInfo = $brandMod->getInfo($info['brand_id']);
                $info['brand_name'] = $brandInfo['name'];
            }
            
            //分类
            if($info['cate_id']) {
                $cateMod = new CateModel();
                $cateInfo = $cateMod->getInfo($info['cate_id']);
                $info['cate_name'] = $cateInfo['name'];
            }
            
            //商品内容
            if($info['intro']) {
                while(strstr($info['intro'],"[IMG_URL]")){
                    $info['intro'] = str_replace("[IMG_URL]", IMG_URL, $info['intro']);
                }
            }
            
            // 商品详情页
            $info['detail_url'] = "http://www.baidu.com";

        }
        return $info;
    }
    
}