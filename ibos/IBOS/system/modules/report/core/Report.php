<?php

/**
 * 工作总结与计划模块------工作总结与计划组件类文件
 *
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2008-2013 IBOS Inc
 * @author gzhzh <gzhzh@ibos.com.cn>
 */
/**
 *  工作总结与计划模块------工作总结与计划组件类
 * @package application.modules.report.core
 * @version $Id: ICReport.php 66 2013-09-13 08:40:50Z gzhzh $
 * @author gzhzh <gzhzh@ibos.com.cn>
 */

namespace application\modules\report\core;

use application\core\model\Module;
use application\core\utils\Convert;
use application\core\utils\Ibos;
use application\core\utils\StringUtil;
use application\modules\dashboard\model\Stamp;
use application\modules\message\model\Comment;
use application\modules\report\model\ModuleReader;
use application\modules\report\model\ReportStats;
use application\modules\report\model\Template;
use application\modules\user\model\User;
use application\modules\user\utils\User as UserUtil;

class Report
{

    /**
     * 处理总结计划列表输出数据
     * @param array $reports 总结计划二维数组
     * @return array 处理过后的总结计划二维数组
     */
    public static function handelListData($reports)
    {
        $return = array();
        foreach ($reports as $report) {
            $report['cutSubject'] = StringUtil::cutStr(strip_tags($report['subject']), 60);
            $report['user'] = User::model()->fetchByUid($report['uid']);
            // 阅读次数
            $readeruid = $report['readeruid'];
            $report['readercount'] = empty($readeruid) ? 0 : count(explode(',', trim($readeruid, ',')));
            $report['content'] = StringUtil::cutStr(strip_tags($report['content']), 255);
            $report['addtime'] = Convert::formatDate($report['addtime'], 'u');
            // 图章
            if ($report['stamp'] != 0) {
                $path = Stamp::model()->fetchIconById($report['stamp']);
                $report['stampPath'] = $path;
            }
            $return[] = $report;
        }
        return $return;
    }

    /**
     * 处理总结汇报的添加数据，目的是为了补充默认值
     * @param array $data 要添加的总结报告数组
     * @return array 返回填充默认值后的总结报告数组
     */
    public static function handleSaveData($data)
    {
        $fieldDefault = array(
            'uid' => 0,
            'begindate' => 0,
            'enddate' => 0,
            'addtime' => TIMESTAMP,
            'typeid' => 0,
            'subject' => '',
            'content' => '',
            'attachmentid' => '',
            'toid' => '',
            'readeruid' => '',
            'status' => 0,
            'remark' => '',
            'stamp' => 0,
            'lastcommenttime' => 0,
            'comment' => '',
            'commentline' => 0,
            'replyer' => 0,
            'reminddate' => 0,
            'commentcount' => 0
        );
        foreach ($data as $field => $val) {
            if (array_key_exists($field, $fieldDefault)) {
                $fieldDefault[$field] = $val;
            }
        }
        return $fieldDefault;
    }

    /**
     * 处理总结或者计划标题
     * @param array $reportType 汇报类型数组
     * @param integer $begin 总结/计划区间开始时间 时间戳
     * @param integer $end 总结/计划区间结束时间 时间戳
     * @param string $connection 显示的文字，0为总结，1为计划， 其他为为自定义标题
     * @return string 返回标题字符串
     */
    public static function handleShowSubject($reportType, $begin, $end, $connection = 0)
    {
        if ($reportType['intervaltype'] == 5) { // 如果是自定义类型
            $connectTitle = $reportType['typename'];
        } else {
            $interval = ReportType::handleShowInterval($reportType['intervaltype']);
            $connectTitle = $connection == 0 ? $interval . '报' : $interval . '计划';
        }
        $subject = date('m.d', $begin) . ' - ' . date('m.d', $end) . ' ' . $connectTitle;
        return $subject;
    }

    /**
     * 判断用户是否有阅读某篇总结的权限
     * @param array $report 要阅读的总结
     * @param integer $uid 阅读人
     * @return boolean 返回是否有权限
     */
    public static function checkPermission($report, $uid)
    {
        // 如果总结所属的uid在他的下属uid里，或者这篇总结的汇报对象是他，有权限
        $toid = explode(',', $report['toid']);
        if ($report['uid'] == $uid || in_array($uid, $toid) || UserUtil::checkIsSub($uid, $report['uid'])) {
            return true;
        } else {
            return false;
        }
    }

    static $week = array(
        '0' => '周日',
        '1' => '周一',
        '2' => '周二',
        '3' => '周三',
        '4' => '周四',
        '5' => '周五',
        '6' => '周六',
    );

    /**
     * 处理汇报列表数据列表
     * @param array $lists 汇报列表数据
     * @return array
     */
    public static function handleData($lists, $type){
        $return = array();
        $uid = Ibos::app()->user->uid;
        $module = Ibos::getCurrentModuleName();
        foreach ($lists as $list){
            $indexWeek = date('w', $list['addtime']);
            $list['week'] = self::$week[$indexWeek];
            $list['createtime'] = Convert::formatDate($list['addtime'], 'u');;
            $reader = ModuleReader::model()->getReader($list['repid'], true);
            $list['readcount'] = $reader['count'];
            $commentCount = Comment::model()->count('`module` = :module AND `table` = :table AND `rowid` = :rowid AND `isdel` = :isdel', array(
                ':module' => 'report',
                ':table' => 'report',
                ':rowid' => $list['repid'],
                ':isdel' => 0,
            ));
            $list['commentcount'] = intval($commentCount);
            $static= ReportStats::model()->find('repid = :repid', array(':repid' => $list['repid']));
            if (empty($static)){
                $list['stamp'] = '';
                $list['bigstamp'] = '';
            }else{
                $stamp = Stamp::model()->fetchByPk($static['stamp']);
                $list['stamp'] = $stamp['icon'];
                $list['bigstamp'] = $stamp['stamp'];
            }
            $reader = ModuleReader::model()->fetchAll("`module` = :module AND `relateid` = :relateid AND `uid` = :uid",
                array(
                    ':module' => $module,
                    ':relateid' => $list['repid'],
                    ':uid' => $uid,
                ));
            $list['isreview'] = empty($reader) ? 0 : 1;
            $icon = Template::model()->getIcon($list['tid']);
            $list['icon'] = $icon;
            $list['subject'] = html_entity_decode($list['subject']);
            $list['remark'] = html_entity_decode($list['remark']);
            if ($type == 'receive'){
                $user = User::model()->fetchByUid($list['uid']);
                $list['user'] = array(
                    'realname' => $user['realname'],
                    'uid' => $user['uid'],
                    'avatar_small' => $user['avatar_small'],
                    'space_url' => $user['space_url'],
                );
            }else{
                $user = User::model()->fetchByUid(Ibos::app()->user->uid);
                $list['user'] = array(
                    'realname' => $user['realname'],
                    'uid' => $user['uid'],
                    'avatar_small' => $user['avatar_small'],
                    'space_url' => $user['space_url'],
                );
            }
            $return[] = $list;
        }
        return $return;
    }

}
