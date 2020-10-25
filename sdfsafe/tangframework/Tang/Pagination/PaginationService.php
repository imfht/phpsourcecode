<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Pagination;
use Tang\Services\I18nService;
use Tang\Services\ServiceProvider;

/**
 * PaginationService
 * Class PaginationService
 * @package Tang\Pagination
 */
class PaginationService extends ServiceProvider
{
    /**
     * @return \Tang\Pagination\IPaginator
     */
    public static function getService()
    {
        return parent::getService();
    }
    protected static function register()
    {
        $instance = static::initObject('pagination','\Tang\Pagination\IPaginator');
        $instance->setl18n(I18nService::getService());
        return $instance;
    }
}