<?php
/**
 * 消息提醒控制器 获取关联事件时间接口 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Ibos;
use application\modules\message\utils\AlarmUtil;
use Exception;

class AlarmEventTime extends Base
{
    /**
     * 获取关联事件时间接口 todo 之后改成只限post
     */
    public function run()
    {
        try{
            $data = $this->getNotifyAlarmEventTimeData();
            $sendTime = AlarmUtil::getEventTimeByModuleAndNode($data['module'], $data['node'], $data['eventid'], $data['diffetime'], $data['timenode']);
            if(!$sendTime){
                throw new Exception(Ibos::lang("Notily sendTime error"));
            }
            return Ibos::app()->controller->ajaxBaseReturn(true, array('sendTime' => $sendTime));
        } catch(Exception $e) {
            return Ibos::app()->controller->ajaxBaseReturn(false, array(), $e->getMessage());
        }
    }
}