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
 * 商家-模型
 * 
 * @author 牧羊人
 * @date 2018-10-19
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class BusinessModel extends CBaseModel {
    function __construct() {
        parent::__construct('business');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-19
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {

            //申请人性别
            $info['gender_name'] = C('GENDER_ARR')[$info['gender']];
            
            //商家LOGO
            if($info['logo']) {
                $info['logo_url'] = IMG_URL . $info['logo'];
            }
            
            //营业执照
            if($info['business_img']) {
                $info['business_img_url'] = IMG_URL . $info['business_img'];
            }
            
            //开户许可证
            if($info['account_img']) {
                $info['account_img_url'] = IMG_URL . $info['account_img'];
            }
            
            //待结算余额
            if($info['balance']) {
                $info['format_balance'] = \Zeus::formatToYuan($info['balance']);
            }
            
            //审核状态
            if($info['check_status']) {
                $info['check_status_name'] = C('CHECK_STATUS_ARR')[$info['check_status']];
            }
            
            //所在城市
            if($info['district_id']) {
                $cityMod = new CityModel();
                $cityName = $cityMod->getCityName($info['district_id'], '>>');
                $info['city_name'] = $cityName;
            }
            
        }
        return $info;
    }
    
}