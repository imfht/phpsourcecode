<?php
/**
 * 发送闹钟提醒计划任务
 */

use application\core\utils\StringUtil;
use application\modules\message\model\Notify;
use application\modules\message\model\NotifyAlarm;
use application\modules\message\model\NotifyMessage;
use application\modules\message\utils\AlarmUtil;

$list = NotifyAlarm::model()->fetchAllCron();

// 初始化
$insertTableName = NotifyMessage::model()->tableName();
$now = TIMESTAMP;
$insertSql = '';
$updateA = array();
$uidStr = '';


foreach ($list as $item) {
    if($item['alarmtype'] == 0) {
        $sendTime = $item['stime'];
        if($now > $sendTime) {
            // 这里可能数据量比较大判断时间后再获得
            $uidStr = implode(',', StringUtil::getUidAByUDPX($item['receiveuids'], true));
            if(empty($uidStr)) {
                $updateA[] = $item['id']; // 没有发送人当做已发送
                continue;
            }
            setAndSendNotify($uidStr, $item['node'], $item['title'], $item['body'], $item['url'], $item['eventid'], $item['uid']);
            $updateA[] = $item['id'];
        } else {
            continue; // 发送时间未到
        }
    } else if($item['alarmtype'] == 1) {
        $sendTime = AlarmUtil::getEventTime($item['tablename'], $item['fieldname'], $item['idname'], $item['eventid'], $item['diffetime']);
        if($sendTime === false){
            $updateA[] = $item['id']; // 可能被删除的数据
            continue;
        }
        if($now > $sendTime) {
            // 这里可能数据量比较大判断时间后再获得
            $uidStr = implode(',', StringUtil::getUidAByUDPX($item['receiveuids'], true));
            if(empty($uidStr)) {
                $updateA[] = $item['id']; // 没有发送人当做已发送
                continue;
            }
            setAndSendNotify($uidStr, $item['node'], $item['title'], $item['body'], $item['url'], $item['eventid'], $item['uid']);
            $updateA[] = $item['id'];
        } else {
            continue;
        }
    } else {
        $updateA[] = $item['id']; // 奇怪的数据
    }
}

/**
 * 设置通用提醒参数并发送提醒 todo 这里没管发送失败
 * @param $uids
 * @param $node
 * @param $body
 * @param $url
 * @param $eventid 事件id
 * @param $senduser 发送人
 */
function setAndSendNotify($uids, $node, $title, $body, $url ,$eventId, $senduid)
{
    return Notify::model()->sendNotify($uids, $node, array(
        '{body}' => $body,
        '{orgContent}' => $body,
        '{title}' => $title,
        '{url}' => $url,
        '{id}' => $eventId,
        'isalarm' => 1,
        'senduid' => intval($senduid),
    ));
}

// 将已发送设置为已发送
if(!empty($updateA)) {
    NotifyAlarm::model()->updateAll(array('issend' => 1), 'FIND_IN_SET(id, :ids)', array(
            ':ids' => implode(',', $updateA)
        )
    );
}


