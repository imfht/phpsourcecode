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
 * 组织机构-模型
 * 
 * @author 牧羊人
 * @date 2018-12-11
 */
namespace app\admin\model;
use app\common\model\BaseModel;
class AdminOrgModel extends BaseModel
{
    // 设置数据表
    protected $name = 'admin_org';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-11
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
            // 组织机构LOGO
            if($info['logo']) {
                $info['logo_url'] = IMG_URL . $info['logo'];
            }
            
            // 获取所属城市
            if($info['district_id']) {
                $cityMod = new CityModel();
                $cityName = $cityMod->getCityName($info['district_id'],">>",true);
                $info['city_name'] = $cityName;
            }
            
        }
        return $info;
    }
    
}