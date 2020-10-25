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
 * 广告-模型
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class AdModel extends BaseModel
{
    // 设置数据表
    protected $name = 'ad';
    
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
            
            // 广告封面
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }
            
            // 广告类型
            if($info['t_type']) {
                $info['t_type_name'] = Config::get('adminconfig.ad_type')[$info['t_type']];
            }
            
            // 类型名称
            if($info['type']) {
                $info['type_name'] = Config::get('adminconfig.system_recomm_type')[$info['type']];
            }
            
            //页面编号
            if($info['ad_sort_id']) {
                $adSortMod = new AdSortModel();
                $adSortInfo = $adSortMod->getInfo($info['ad_sort_id']);
                $info['ad_sort_name'] = $adSortInfo['name'] . "=>" . $adSortInfo['loc_id'];
            }
            
            //推荐对象
            if($info['type']==1) {
//                 //CMS文章
//                 $articleMod = new ArticleModel();
//                 $articleInfo = $articleMod->getInfo($info['type_id']);
//                 $info['type_desc'] = $articleInfo["title"];
            }else{
                //TODO...
            }
            
        }
        return $info;
    }
    
}