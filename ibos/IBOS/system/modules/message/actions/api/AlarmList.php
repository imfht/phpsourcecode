<?php
/**
 * 消息提醒控制器 闹钟提醒列表接口 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Ibos;
use application\core\utils\Page;
use application\modules\message\model\NotifyAlarm;

class AlarmList extends Base
{
    /**
     * 新增闹钟提醒接口 TODO 之后设置为只能POST
     */
    public function run()
    {
        $data = $this->getNotifyAlarmListSelectData();
        $uid = Ibos::app()->user->uid;
        $list = array();
        // 该接口不需要node和eventid两个条件
        $count =  NotifyAlarm::model()->getListCount(
            $uid,
            $data['search'],
            null,
            $data['module'],
            null
        );
        if(!empty($count)){
            $list = NotifyAlarm::model()->fetchAllByModuleOrSearchOrEventId(
                $uid,
                $data['search'],
                null,
                $data['module'],
                null,
                $data['pagesize'],
                $data['offset'],
                "id,uid,node,module,title,body,ctime,url,receiveuids,stime,alarmtype,diffetime,eventid,uptime,timenode"
            );
        }
        if(!empty($list)) {
            $this->afterProcessingList($list);
        }
        return Ibos::app()->controller->ajaxBaseReturn(true, array('list'=>$list, 'count'=>$count));
    }
}