<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 城市-控制器
 * 
 * @author 牧羊人
 * @date 2018-11-22
 */
namespace Script\Controller;
use Script\Model\CityModel;
use Script\Service\CityService;
class CityController extends BaseScriptController {
    function __construct() {
        parent::__construct();
        $this->mod = new CityModel();
        $this->service = new CityService();
    }
    
    /**
     * 城市选择组件数据
     * 备注：将获取的数据转为JSON提供给城市选择组件使用
     * 
     * @author 牧羊人
     * @date 2018-11-22
     */
    function getCityList() {
        $result = $this->service->getCityList();
        print_r(json_encode($result));exit;
    }
    
}