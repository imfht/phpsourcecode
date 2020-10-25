<?php


namespace WxSDK\core\model\card;


use WxSDK\core\model\Model;

class AdvancedInfo extends Model
{
    /**
     * @var UseCondition 使用门槛（条件）字段，若不填写使用条件则在券面拼写 ：无最低消费限制，全场通用，不限品类；并在使用说明显示： 可与其他优惠共享
     */
    public $use_condition;
    /**
     * @var CardAbstract 封面摘要结构体
     */
    public $abstract;
    /**
     * @var array TextImage数组。图文列表，显示在详情内页 ，优惠券券开发者须至少传入 一组图文列表
     */
    public $text_image_list;

    /**
     * @var Array    商家服务类型，可多选：
     * BIZ_SERVICE_DELIVER 外卖服务；
     * BIZ_SERVICE_FREE_PARK 停车位；
     * BIZ_SERVICE_WITH_PET 可带宠物；
     * BIZ_SERVICE_FREE_WIFI 免费wifi
     */
    public $business_service;
    /**
     * @var array TimeLimit数组。使用时段限制，不填默认不显示
     */
    public $time_limit;
}