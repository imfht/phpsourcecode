<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\controller\ControllerBase;
use app\api\logic\ApiBase as LogicApiBase;

/**
 * 接口基类控制器
 */
class ApiBase extends ControllerBase
{

    // API基础逻辑
    private static $apiBaseLogic = null;

    /**
     * 基类初始化
     */
    public function __construct()
    {

        parent::__construct();

        self::$apiBaseLogic = get_sington_object('apiBaseLogic', LogicApiBase::class);

        self::$apiBaseLogic->checkParam($this->param);

        debug('api_begin');
    }

    /**
     * API返回数据
     */
    public function apiReturn($code_data = [], $return_data = [], $return_type = 'json')
    {

        $result = self::$apiBaseLogic->apiReturn($code_data, $return_data, $return_type);

        debug('api_end');

        write_exe_log('api_begin', 'api_end', DATA_NORMAL);

        return $result;
    }
}
