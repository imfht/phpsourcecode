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
 * 布局-模型
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class LayoutModel extends BaseModel
{
    // 设置数据表
    protected $name = 'layout';
    
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
            
            // 图片地址
            if($info['image']) {
                $info['image_url'] = IMG_URL . $info['image'];
            }
            
            // 类型名称
            if($info['type']) {
                $info['type_name'] = Config::get('adminconfig.system_recomm_type')[$info['type']];
            }
            
            //获取推荐对象
            if($info['type']==1) {
//                 //CMS文章
//                 $articleMod = new ArticleModel();
//                 $articleInfo = $articleMod->getInfo($info['type_id']);
//                 $info['type_desc'] = $articleInfo["title"];
            }else{
                //TODO...
            }
            
            //页面位置
            if($info['page_id']) {
                $itemInfo = db("item")->find($info['page_id']);
                $info['page_name'] = $itemInfo['name'];
            }
            
            //页面编号
            $locInfo = db("layoutDesc")->where([
                'page_id'   =>$info['page_id'],
                'loc_id'    =>$info['loc_id']
            ])->find();
            if($locInfo) {
                $info['loc_name'] = $locInfo['loc_desc'] . "=>" . $info['loc_id'];
            }
            
        }
        return $info;
    }
    
}