<?php

/**
 * 消息提醒控制器 新增闹钟提醒接口 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Ibos;
use application\modules\message\model\NotifyAlarm;
use application\modules\message\utils\AlarmUtil;
use Exception;

class AlarmAdd extends Base
{

    /**
     * 新增闹钟提醒接口 TODO 之后设置为只能POST
     */
    public function run() {
        $data = $this->getAlarmFormData();
        // 参数检查
        try{
            $this->manageNotifyAlarmData($data);
            $newid = NotifyAlarm::model()->addNotifyAlarm($data);
            $data['id'] = $newid;
            return Ibos::app()->controller->ajaxBaseReturn(true, $data);
        } catch(Exception $e) {
            return Ibos::app()->controller->ajaxBaseReturn(false, array(), $e->getMessage());
        }
    }
}