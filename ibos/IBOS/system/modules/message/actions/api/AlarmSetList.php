<?php
/**
 * 消息提醒控制器 设置提醒列表 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Ibos;
use application\modules\message\model\NotifyAlarm;
use application\modules\message\utils\AlarmUtil;

class AlarmSetList extends Base
{
    // 设置提醒列表
    public function run()
    {
        $data = $this->getNotifyAlarmListSelectData();
        $uid = Ibos::app()->user->uid;
        if(empty($data['node']) || empty($data['eventid']) || empty($data['module'])) {
            return Ibos::app()->controller->ajaxBaseReturn(false, array());
        }
        // 获得配置
        $nodeConfig = AlarmUtil::getAlarmConfigView($data['module'], $data['node'], $data['eventid']);

        if(empty($nodeConfig)) {
            Ibos::app()->controller->ajaxBaseReturn(false, array());
        }

        // 这个列表不需要搜索，事件id和节点是必须的
        $list = NotifyAlarm::model()->fetchAllByModuleOrSearchOrEventId($uid, null, $data['eventid'], $data['module'], $data['node'], $data['pagesize'], $data['offset']);
        if(!empty($list)) {
            $this->afterProcessingList($list);
        }

        $allData = array(
            'list' => $list,
            'nodeConfig' => $nodeConfig,
        );

        return Ibos::app()->controller->ajaxBaseReturn(true, $allData);
    }
}