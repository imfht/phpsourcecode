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
 * 广告描述-模型
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class AdSortModel extends CBaseModel {
    function __construct() {
        parent::__construct('ad_sort');
    }
    
    //自动验证
    protected $_validate = array(
        array('name', 'require', '广告位名称不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('name', '1,50', '广告位名称长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('platform', 'require', '请选择使用平台！', self::EXISTS_VALIDATE, '', 3),
        array('loc_id', 'require', '广告位置不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('item_id', 'require', '请选择站点！', self::EXISTS_VALIDATE, '', 3),
        array('cate_id', 'require', '请选择站点栏目！', self::EXISTS_VALIDATE, '', 3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //获取站点信息
            if($info['item_id']) {
                $itemMod = new ItemModel();
                $itemInfo = $itemMod->getInfo($info['item_id']);
                $info['item_name'] = $itemInfo['name'];
            }
            
            //获取栏目
            if($info['cate_id']) {
                $cateMod = new ItemCateModel();
                $cateName = $cateMod->getCateName($info['cate_id'],">>");
                $info['cate_name'] = $cateName;
            }
            
            //使用平台
            if($info['platform']) {
                $info['platform_name'] = C("PLATFORM_TYPE")[$info['platform']];
            }
            
        }
        return $info;
    }
    
}