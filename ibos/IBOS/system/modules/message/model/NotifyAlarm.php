<?php

namespace application\modules\message\model;

use application\core\model\Model;
use application\core\utils\Ibos;

class NotifyAlarm extends Model
{
    const TYPE_UNSENT = 0; // 未发送
    const TYPE_HAS_BEEN_SENT = 1; // 已发送

    /**
     * @param string $className
     * @return Notify
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{notify_alarm}}';
    }

    /**
     * 用户添加通用闹钟提醒
     * @param $data
     */
    public function addNotifyAlarm($data)
    {
        $data['uid'] = Ibos::app()->user->uid; // 用户id
        $data['ctime'] = $data['uptime'] = TIMESTAMP; // 创建、更新时间
        $newid = $this->add($data, true);
        return $newid;
    }

    /**
     * 用户根据id获得一条闹钟提醒
     * @param $id
     */
    public function getNotifyAlarm($id , $select = '*')
    {
        $uid = Ibos::app()->user->uid;
        return Ibos::app()->db->createCommand()
            ->select($select)
            ->from($this->tableName())
            ->where("`id` = :id AND `uid` = :uid", array(':id' => $id, ':uid' => $uid))
            ->queryRow();
    }

    /**
     * 根据多个id字符串删除属于自己的闹钟提醒
     * @param $ids
     * @return int
     */
    public function deleteAllNotifyAlarm($ids)
    {
        $uid = Ibos::app()->user->uid;
        return $this->deleteAll('uid = :uid AND FIND_IN_SET(id,:id)', array(':uid' => $uid, ':id' => $ids));
    }

    /**
     * 根据uid查找有多少个模块有消息，用于分页
     * @param integer $uid 用户uid
     * @return integer 符合条件的条数，注：是根据模块分组
     */
    public function fetchPageCount($uid, $search, $eventId, $module)
    {
        $condition = '`uid` = :uid AND issend = :unsent';
        $params[':uid'] = $uid;
        $params[':unsent'] = self::TYPE_UNSENT;

        // 拼接条件
        if(!empty($search)) {
            /**
             * todo LIKE 语句不能使用占位符？
             */
            $condition .= ' AND `body` LIKE \'%' .$search. '%\'';
//            $params[':search'] = $search;
        }

        if(!empty($eventId)) {
            $condition .= " AND `eventid` = :eventid";
            $params[':eventid'] = $eventId;
        }

        if(!empty($module)) {
            $condition .= " AND `module` = :module";
            $params[':module'] = $module;
        }

        $pageCount = $this->count($condition, $params);
        return $pageCount;
    }

    /**
     * 根据前端传来的条件查询闹钟提醒列表
     * @param $uid
     * @param $search
     * @param $eventId
     * @param $module
     * @param $pageSize
     * @param $offset
     * @param $select 要查询的字段
     */
    public function fetchAllByModuleOrSearchOrEventId($uid, $search, $eventId, $module, $node, $pageSize, $offset, $select='*')
    {
        $condition = self::getListCondition($uid, $search, $eventId, $module, $node);

        return Ibos::app()->db->createCommand()
            ->select($select)
            ->from($this->tableName())
            ->where($condition['condition'], $condition['params'])
            ->limit($pageSize)
            ->offset($offset)
            ->order('ctime DESC')
            ->queryAll();
    }

    /**
     * 计划任务查找全部
     */
    public function fetchAllCron()
    {
        return Ibos::app()->db->createCommand()
            ->select('id, node, uid, module, title, body, url, receiveuids, stime, alarmtype, diffetime, eventid, tablename, fieldname, idname')
            ->from($this->tableName())
            ->where("`issend` = 0")
            ->queryAll();
    }

    /**
     * 组成列表查询条件
     * @param $uid
     * @param $search
     * @param $eventId
     * @param $module
     * @param $node
     * @return array
     */
    private function getListCondition($uid, $search, $eventId, $module, $node) {
        $condition = '`uid` = :uid AND issend = :unsent';
        $params[':uid'] = $uid;
        $params[':unsent'] = self::TYPE_UNSENT;

        // 拼接条件
        if(!empty($search)) {
            $condition .= ' AND `body` LIKE \'%' .$search. '%\'';
//            $params[':search'] = $search;
        }

        if(!empty($eventId)) {
            $condition .= " AND `eventid` = :eventid";
            $params[':eventid'] = $eventId;
        }

        if(!empty($module)) {
            $condition .= " AND `module` = :module";
            $params[':module'] = $module;
        }

        if(!empty($node)) {
            $condition .= " AND `node` = :node";
            $params[':node'] = $node;
        }

        return array('condition' => $condition, 'params' => $params);
    }

    /**
     * 返回符合条件的列表总数
     * @param $uid
     * @param $search
     * @param $eventId
     * @param $module
     * @param $node
     * @return bool|\CDbDataReader|mixed|string
     */
    public function getListCount($uid, $search, $eventId, $module, $node) {

        $condition = self::getListCondition($uid, $search, $eventId, $module, $node);
        return Ibos::app()->db->createCommand()
            ->select('count(*)')
            ->from($this->tableName())
            ->where($condition['condition'], $condition['params'])
            ->queryScalar();
    }
}
