<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;

/**
 * 执行记录控制器
 */
class Exelog extends AdminBase
{

    // 执行记录逻辑
    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'ExeLog');
    }

    /**
     * 全局范围列表
     */
    public function appList()
    {


        $clist = self::$commonLogic->getDataList(['type' => DATA_DISABLE], true, TIME_CT_NAME . ' desc');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('exelogapp_list');
    }

    /**
     * 接口范围列表
     */
    public function apiList()
    {


        $clist = self::$commonLogic->getDataList(['type' => DATA_NORMAL], true, TIME_CT_NAME . ' desc');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('exelogapi_list');
    }

    /**
     * 日志入库
     */
    public function logImport()
    {
        $exe_log_path = "./data/exe_log.php";

        $exe_log_array = require $exe_log_path;


        if (is_array($exe_log_array)) {

            self::$commonLogic->dataAdd($exe_log_array, false) && file_put_contents($exe_log_path, '');
        }
        $this->jump([RESULT_SUCCESS, '日志已入库']);
    }

    /**
     * 日志清空
     */
    public function logClean()
    {

        $this->jump(self::$commonLogic->dataDel([DATA_STATUS_NAME => DATA_NORMAL], '日志已清空', true));
    }
}
