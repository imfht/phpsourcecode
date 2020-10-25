<?php
/**
 * 消息提醒控制器 删除闹钟提醒接口 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Env;
use application\core\utils\Ibos;
use application\modules\message\model\NotifyAlarm;

class AlarmDel extends Base
{
    /**
     * 删除闹钟提醒接口，直接删除不设置回收站 TODO 之后设置为只能POST
     */
    public function run() {
        $ids = Env::getRequest('ids');
        if(!empty($ids)) {
            // 只要是自己的就能删除
            $res = NotifyAlarm::model()->deleteAllNotifyAlarm($ids);
            return Ibos::app()->controller->ajaxBaseReturn((bool)$res, array());
        } else{
            return Ibos::app()->controller->ajaxBaseReturn(false, array());
        }
    }
}