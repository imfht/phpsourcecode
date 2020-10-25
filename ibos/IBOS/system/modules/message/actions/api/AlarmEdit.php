<?php

/**
 * 消息提醒控制器 修改闹钟提醒接口 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Ibos;
use application\modules\message\model\NotifyAlarm;
use Exception;
use application\core\utils\Env;

class AlarmEdit extends Base
{

    /**
     * 修改闹钟提醒接口 TODO 之后设置为只能POST
     */
    public function run() {
        $data = $this->getAlarmFormData();
        // 参数检查
        try{
            $NotifyAlarmRow = $this->checkEditRow();
            $this->manageNotifyAlarmData($data); // 和新增一样的检查参数
            $data = array_merge($NotifyAlarmRow, $data);
            $id = $NotifyAlarmRow['id'];
            $res = NotifyAlarm::model()->updateByPk($id, $data); // todo 没有变化也是成功
            return Ibos::app()->controller->ajaxBaseReturn(true, $data);
        } catch(Exception $e) {
            return Ibos::app()->controller->ajaxBaseReturn(false, array(), $e->getMessage());
        }
    }

    /**
     * 判断id参数是否正确，判断是否为本人修改
     */
    private function checkEditRow()
    {
        // 可以修改条件
        // 0.有该条提醒 1.是本人设置的提醒 2.该条提醒未发送
        $id = Env::getRequest('id');
        if(empty($id) || !is_numeric($id)) {
            throw new Exception(Ibos::lang("Params error"));
        }
        $NotifyAlarmRow = NotifyAlarm::model()->getNotifyAlarm($id); // 查出该条的记录
        if(empty($NotifyAlarmRow) || $NotifyAlarmRow['issend'] == NotifyAlarm::TYPE_HAS_BEEN_SENT ) {
            throw new Exception(Ibos::lang("Params error"));
        }
        return $NotifyAlarmRow;
    }
}