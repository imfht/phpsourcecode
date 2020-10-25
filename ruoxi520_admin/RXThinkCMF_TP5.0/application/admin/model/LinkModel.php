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
 * 友情链接-模型
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class LinkModel extends BaseModel
{
    // 设置数据表
    protected $name = 'link';
    
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
            
            // LOGO
            if($info['logo']) {
                $info['logo_url'] = IMG_URL . $info['logo'];
            }
            
            // 使用平台
            if($info['platform']) {
                $info['platform_name'] = Config::get('adminconfig.platform_type')[$info['platform']];
            }
            
            // 友链类型
            if($info['t_type']) {
                $info['t_type_name'] = Config::get('link_type')[$info['t_type']];
            }
            
        }
        return $info;
    }
    
}