<?php
/**
 * 消息提醒控制器 闹钟提醒单条详情接口 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Env;
use application\core\utils\Ibos;
use application\modules\message\model\NotifyAlarm;
use application\modules\message\utils\AlarmUtil;

class AlarmDetail extends Base
{
    /**
     * @return mixed
     */
    public function run()
    {
        $id = Env::getRequest('id');
        $module = Env::getRequest('module');
        $node = Env::getRequest('node');
        $eventId = Env::getRequest('eventId');

        $eventId = empty($eventId) || !is_numeric($eventId) ? null : $eventId;

        if(empty($module) || empty($node)){
            Ibos::app()->controller->ajaxBaseReturn(false, array());
        }

        // 获得配置
        $nodeConfig = AlarmUtil::getAlarmConfigView($module, $node, $eventId);

        if(empty($nodeConfig)) {
            Ibos::app()->controller->ajaxBaseReturn(false, array());
        }

        $NotifyAlarmRow = array();
        if(!empty($id) && is_numeric($id)) {
            $NotifyAlarmRow = NotifyAlarm::model()->getNotifyAlarm($id); // 查出该条的记录
        }

        return Ibos::app()->controller->ajaxBaseReturn(true, array(
            'data' => $NotifyAlarmRow,
            'nodeConfig' => $nodeConfig
            ));
    }
}