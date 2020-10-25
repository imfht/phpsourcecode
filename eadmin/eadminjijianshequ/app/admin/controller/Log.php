<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\logic\Log as LogicLog;

/**
 * 行为日志控制器
 */
class Log extends AdminBase
{

    // 行为日志逻辑
    private static $logLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$logLogic = get_sington_object('logLogic', LogicLog::class);
    }

    /**
     * 日志列表
     */
    public function logList()
    {

        $this->assign('list', self::$logLogic->getLogList([], true, TIME_CT_NAME . ' desc'));

        return $this->fetch('log_list');
    }

    /**
     * 日志删除
     */
    public function logDel($id = 0)
    {

        $this->jump(self::$logLogic->logDel(['id' => $id]));
    }

    /**
     * 日志清空
     */
    public function logClean()
    {

        $this->jump(self::$logLogic->logDel([DATA_STATUS_NAME => DATA_NORMAL]));
    }
}
