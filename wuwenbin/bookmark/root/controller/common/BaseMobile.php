<?php
/**
 * 移动版基类
 */
class ControllerBaseMobile extends ControllerBase
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        defined("SITE_URL") || define("SITE_URL", dirname(Config::get("baseUrl")));
    }
}
