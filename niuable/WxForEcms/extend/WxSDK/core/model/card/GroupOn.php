<?php


namespace WxSDK\core\model\card;

use WxSDK\core\model\Model;

/**
 * 团购
 * @package WxSDK\core\model\card
 */
class GroupOn extends Model
{
    public $base_info;
    public $advanced_info;
    /**
     * @var string 团购券专用，团购详情
     */
    public $deal_detail;
}