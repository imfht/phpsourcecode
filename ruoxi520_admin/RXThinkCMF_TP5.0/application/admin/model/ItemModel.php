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
 * 站点-模型
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class ItemModel extends BaseModel
{
    // 设置数据表
    protected $name = 'item';
    
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
                $info['type_name'] = Config::get('adminconfig.item_type')[$info['type']];
            }
            
        }
        return $info;
    }
    
}