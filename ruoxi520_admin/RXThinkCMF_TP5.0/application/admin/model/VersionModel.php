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
 * 版本管理-模型
 * 
 * @author 牧羊人
 * @date 2018-12-14
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class VersionModel extends BaseModel
{
    // 设置数据表
    protected $name = 'version';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-14
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
            // 版本类型名称
            if($info['version_type']) {
                $info['version_type_name'] = Config::get('adminconfig.version_type')[$info['version_type']];
            }
            
        }
        return $info;
    }
    
}