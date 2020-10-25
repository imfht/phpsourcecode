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
 * @date 2018-07-17
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class AdModel extends CBaseModel {
    function __construct() {
        parent::__construct('ad');
    }
    
    //自动验证
    protected $_validate = array(
        array('title', 'require', '广告标题不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('title', '1,100', '广告标题长度不合法', self::EXISTS_VALIDATE, 'length',3),
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
            
            //广告封面
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }
            
            //广告类型
            if($info['t_type']) {
                $info['t_type_name'] = C('AD_TYPE')[$info['t_type']];
            }
            
            //类型名称
            if($info['type']) {
                $info['type_name'] = C('SYSTEM_RECOMM_TYPE')[$info['type']];
            }
            
            //页面编号
            if($info['ad_sort_id']) {
                $adSortMod = new AdSortModel();
                $adSortInfo = $adSortMod->getInfo($info['ad_sort_id']);
                $info['ad_sort_name'] = $adSortInfo['name'] . "=>" . $adSortInfo['loc_id'];
            }
            
            //获取推荐对象
            if($info['type']==1) {
                //CMS文章
                $articleMod = new ArticleModel();
                $articleInfo = $articleMod->getInfo($info['type_id']);
                $info['type_desc'] = $articleInfo["title"];
            }else{
                //TODO...
            }
            
        }
        return $info;
    }
    
}