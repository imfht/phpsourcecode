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
 * 广告位-模型
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class AdSortModel extends BaseModel
{
    // 设置数据表
    protected $name = 'ad_sort';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
            // 获取站点
            if($info['item_id']) {
                $itemMod = new ItemModel();
                $itemInfo = $itemMod->getInfo($info['item_id']);
                $info['item_name'] = $itemInfo['name'];
            }
            
            // 获取栏目
            if($info['cate_id']) {
                $cateMod = new ItemCateModel();
                $cateName = $cateMod->getCateName($info['cate_id'],">>");
                $info['cate_name'] = $cateName;
            }
            
            // 使用平台
            if($info['platform']) {
                $info['platform_name'] = Config::get('adminconfig.platform_type')[$info['platform']];
            }
            
        }
        return $info;
    }
    
}