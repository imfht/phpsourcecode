<?php
/**
 * 统计接口
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\department\model\Department;
use application\modules\report\model\Report;
use application\modules\report\model\ReportStats;
use application\modules\report\utils\Report as ReportUtil;
use application\modules\user\model\User;

class GetCount extends Base
{

    public function run()
    {
        $uid = Ibos::app()->user->uid;
        $user = User::model()->fetchByUid($uid);
        $userData['deptname'] = Department::model()->fetchDeptNameByDeptId($user['deptid']);
        $userData['avatar_middle'] = $user['avatar_middle'];
        $userData['realname'] = $user['realname'];
        $receiveCondition = ReportUtil::getListCondition(self::RECEIVE, $uid);
        $unreadCondition = ReportUtil::getListCondition(self::UNREAD, $uid);
        $sendCondition = ReportUtil::getListCondition(self::SEND, $uid);
        $receive = Report::model()->count($receiveCondition);
        $unread = Report::model()->count($unreadCondition);
        $send = Report::model()->count($sendCondition);
        $sendList = Ibos::app()->db->createCommand()
            ->from('{{report}}')
            ->select('repid')
            ->where($sendCondition)
            ->queryColumn();
        $sendRepids = "'".implode("','", $sendList)."'";
        $reviewCount = ReportStats::model()->count("repid IN ($sendRepids)");
        $params = array(
            'user' => $userData,
            'receive' => $receive,
            'unread' => $unread,
            'send' => $send,
            'review' => $reviewCount,
        );
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => $params,
        ));
    }

}