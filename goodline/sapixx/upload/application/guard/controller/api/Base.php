<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\allwin\controller\api;
use app\common\controller\Api;
use think\facade\Request;

class Base extends Api{

    protected $city_id;
    protected $header;
    protected $lng;  //经度
    protected $lat;  //纬度
    protected $qqgps;

    /**
     * 初始化API并读取城市或你的经纬度,把QQ经纬度转换成百度经纬度
     * @return void
     */
    public function initialize() {
        parent::initialize();
    
    }
}