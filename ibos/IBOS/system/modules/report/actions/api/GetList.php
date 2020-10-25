<?php
/**
 * 我收到和我发出的列表接口,type值为receive（接收）send（发送）unread（未读）
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\core\utils\StringUtil;
use application\modules\main\model\Setting;
use application\modules\report\model\Report;
use application\modules\report\model\ReportRecord;
use application\modules\report\utils\Report as ReportUtil;
use application\modules\report\core\Report as ReportCore;

class GetList extends Base
{

    public function run()
    {
        $data = $this->data;
        $limit = empty($data['limit']) ? 10 : $data['limit'];
        $offset = $data['offset'];
        $type = empty($data['type']) ? 'receive' : $data['type'];
        // $type 参数只支持：receive（接收）send（发送）
        if (!in_array($type, array(self::RECEIVE, self::SEND, self::UNREAD))) {
            $msg = Ibos::lang("Error param") . "请检查 type 参数";
            return $this->getController()->ajaxReturn(array("isSuccess" => false, "msg" => $msg, 'data' => ''));
        }
        $keyword = empty($data['keyword']) ? array() : $data['keyword'];
        $uid = Ibos::app()->user->uid;
        $condition = ReportUtil::getListCondition($type, $uid, $keyword);
        $list = Report::model()->getReportByCondition($condition, $limit, $offset);
        $allcount = $list['count'];
        $lists = ReportCore::handleData($list['list'], $type);
        $count = count($lists);
        for ($i = 0; $i < $count; $i++) {
            $records = ReportRecord::model()->fetchAll('repid = :repid', array(':repid' => $lists[$i]['repid']));
            for ($j = 0; $j < count($records); $j++){
                $records[$j]['content'] = \CHtml::decode(\CHtml::decode($records[$j]['content']));
            }

            $lists[$i]['record'] = $records;
        }

        if ($allcount == 0 || $count + $offset >= $allcount || $allcount <= $limit){
            $hasMore =  false;
        }else{
            $hasMore =  true;
        }
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => array(
                'count' => $allcount,
                'list' => $lists,
                'hasMore' => $hasMore,
            ),
        ));
    }

}