<?php

/**
 * 任务指派模块------ assignment表的数据层操作文件
 *
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2008-2013 IBOS Inc
 * @author gzhzh <gzhzh@ibos.com.cn>
 */

/**
 * 任务指派模块------  assignment表的数据层操作类，继承ICModel
 * @package application.modules.assignments.model
 * @version $Id: Assignment.php 1371 2014-05-15 09:33:26Z gzhzh $
 * @author gzhzh <gzhzh@ibos.com.cn>
 */

namespace application\modules\assignment\model;

use application\core\model\Model;
use application\core\utils\Ibos;
use CDbCriteria;
use CPagination;

class Assignment extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{assignment}}';
    }

    /**
     * 查找指派人是uid所有未完成任务
     * @param integer $uid
     */
    public function fetchUnfinishedByDesigneeuid($uid)
    {
        $record = $this->fetchAll(array(
            'condition' => sprintf('`designeeuid` = %d AND `status` != 2 AND `status` != 3', $uid),
            'order' => 'addtime DESC',
        ));
        return $record;
    }

    /**
     * 查找负责人是uid所有未完成任务
     * @param integer $uid
     */
    public function fetchUnfinishedByChargeuid($uid)
    {
        $record = $this->fetchAll(array(
            'condition' => sprintf('`chargeuid` = %d AND `status` != 2 AND `status` != 3', $uid),
            'order' => 'addtime DESC',
        ));
        return $record;
    }

    /**
     * 查找参与者是uid所有未完成任务
     * @param integer $uid
     */
    public function fetchUnfinishedByParticipantuid($uid)
    {
        $record = $this->fetchAll(array(
            'condition' => sprintf('FIND_IN_SET(%d, `participantuid`) AND `status` != 2 AND `status` != 3', $uid),
            'order' => 'addtime DESC',
        ));
        return $record;
    }

    /**
     * 获得某个用户未完成的任务数据(分为uid指派的、负责的、参与的和用户数据)
     * @param integer $uid
     * @return array
     */
    public function getUnfinishedByUid($uid)
    {
        $datas = array(
            'designeeData' => $this->fetchUnfinishedByDesigneeuid($uid), // 指派的任务
            'chargeData' => $this->fetchUnfinishedByChargeuid($uid), // 负责的任务
            'participantData' => $this->fetchUnfinishedByParticipantuid($uid) // 参与的任务
        );
        return $datas;
    }

    /**
     * 某个用户根据一个事件查找所有相关的任务
     * @param $uid
     * @param $associatedmodule 关联模块
     * @param $associatednode 关联节点
     * @param $associatedid 事件的唯一id
     */
    public function fetchAllAssignmentListByEvent($uid, $associatedmodule, $associatednode, $associatedid)
    {
        $where = "`associatedmodule` = :associatedmodule AND `associatednode` = :associatednode AND `associatedid` = :associatedid";
        $where .= " AND (`designeeuid` = :uid OR `chargeuid` = :uid OR FIND_IN_SET(:uid, `participantuid`))";
        $param = array(
            ':uid' => $uid,
            ':associatedmodule' => $associatedmodule,
            ':associatednode' => $associatednode,
            ':associatedid' => $associatedid,
        );
        return Ibos::app()->db->createCommand()
            ->from($this->tableName())
            ->where($where, $param)
            ->order("status ASC,addtime DESC")
            ->queryAll();
    }

    /**
     * 分页查找数据
     * @param string $conditions 条件
     * @param integer $pageSize 每页多少条数据
     * @return array
     */
    public function fetchAllAndPage($conditions = '', $pageSize = null)
    {
        $conditionArray = array('condition' => $conditions, 'order' => 'finishtime DESC');
        $criteria = new CDbCriteria();
        foreach ($conditionArray as $key => $value) {
            $criteria->$key = $value;
        }
        $count = $this->count($criteria);
        $pages = new CPagination($count);
        $everyPage = is_null($pageSize) ? Ibos::app()->params['basePerPage'] : $pageSize;
        $pages->setPageSize(intval($everyPage));
        $pages->applyLimit($criteria);
        $datas = $this->fetchAll($criteria);
        return array('pages' => $pages, 'datas' => $datas, 'count' => $count);
    }

    /**
     * 获得某个uid未完成任务数（包括指派的任务、负责的任务、参与的任务）
     * @param integer $uid
     * @return type integer
     */
    public function getUnfinishCountByUid($uid)
    {
        $count = $this->count("`status` != 2 AND `status` != 3 AND (`designeeuid` = {$uid} OR `chargeuid` = {$uid} OR FIND_IN_SET({$uid}, `participantuid`) )");
        return intval($count);
    }

    /**
     * 根据任务ID查找任务完成状态
     * @param integer $assignmentid 任务ID
     * @return boolean
     */
    public function getStatusByAssignmentid($assignmentid)
    {
        $record = $this->fetch(array(
            'condition' => sprintf('`assignmentid` = %d AND `status` = 2 OR `status` = 3', $assignmentid)
        ));
        if (!empty($record)) {
            $bool = true;
        } else {
            $bool = false;
        }
        return $bool;
    }

    /**
     * 获取首页显示今天到期的任务
     */
    public function fetchTodayAssignmentData()
    {
        $todayTime = strtotime(date('Y-m-d'));
        $tomorrowTime = strtotime(date('Y-m-d',strtotime('+1 day')));
        $uid = Ibos::app()->user->uid;
        return $this->getQueryByEndtime($uid, $todayTime, $tomorrowTime)
            ->offset(0)
            ->limit(4)
            ->queryAll();
    }

    /**
     * 获取首页显示明天到期的任务
     */
    public function fetchTomorrowAssignmentData()
    {
        $todayTime = strtotime(date('Y-m-d',strtotime('+1 day')));
        $tomorrowTime = strtotime(date('Y-m-d',strtotime('+2 day')));
        $uid = Ibos::app()->user->uid;
        return $this->getQueryByEndtime($uid, $todayTime, $tomorrowTime)
            ->offset(0)
            ->limit(4)
            ->queryAll();
    }

    /**
     *
     * 根据开始-结束时间返回一个时间区间内需要完成的任务查询对象
     * @param $uid
     * @param $start 开始时间
     * @param $end 结束时间
     */
    private function getQueryByEndtime($uid, $start, $end)
    {
        return Ibos::app()->db->createCommand()
            ->from($this->tableName())
            ->where(' `endtime` >= :start AND `endtime` <= :end',array(':start' => $start,':end' => $end))
            ->andWhere('(`designeeuid` = :uid OR `chargeuid` = :uid OR FIND_IN_SET(:uid, `participantuid`))', array(':uid' => $uid))
            ->andWhere(' `status` <2 ')
            ->order('endtime ASC');
    }
}
