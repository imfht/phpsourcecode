<?php
/**
 * 显示汇报详细内容接口
 */

namespace application\modules\report\actions\api;

use application\core\utils\ArrayUtil;
use application\core\utils\Attach;
use application\core\utils\Env;
use application\core\utils\Ibos;
use application\modules\dashboard\model\Stamp;
use application\modules\main\model\Setting;
use application\modules\message\model\NotifyMessage;
use application\modules\report\core\ReportField;
use application\modules\report\model\ModuleReader;
use application\modules\report\model\Report;
use application\modules\report\core\Report as ReportCore;
use application\modules\report\model\ReportRecord;
use application\modules\report\model\ReportStats;
use application\modules\report\model\Template;
use application\modules\role\utils\Role;
use application\modules\user\model\User;
use application\modules\user\utils\User as UserUtil;
use application\modules\report\utils\Report as ReportUtil;

class ShowReport extends Base
{

    public function run()
    {
        $repid = Env::getRequest('repid');
        $type = Env::getRequest('type');
        $uid = Ibos::app()->user->uid;
        $report = Report::model()->fetchByPk($repid);
        $toIds = explode(',', $report['toid']);
        $subUid = User::model()->getAllSubByUid($uid);
        $subUidArr = ArrayUtil::getColumn($subUid, 'uid');
        if (!in_array($uid, $toIds) && $uid != $report['uid'] && !in_array($report['uid'], $subUidArr)){
            header("HTTP/1.0 500");
            exit(json_encode(array(
                'isSuccess' => false,
                'msg' => Ibos::lang('Do not have permission to show this report'),
            )));
        }
        $user = User::model()->fetchByUid($report['uid']);
        $report['user'] = array(
            'realname' => $user['realname'],
            'uid' => $user['uid'],
            'avatar_small' => $user['avatar_small'],
            'space_url' => $user['space_url'],
        );
        $template = Template::model()->fetchByPk($report['tid']);
        $report['icon'] = $template['pictureurl'];
        $indexWeek = date('w', $report['addtime']);
        $report['week'] = ReportCore::$week[$indexWeek];
        $report['createtime'] = date('Y-m-d H:i:s', $report['addtime']);
        $report['type'] = $type;
        $static = ReportStats::model()->fetch('repid = :repid', array(':repid' => $repid));
        if (empty($static)){
            $report['stamp'] = '';
        }else{
            $report['stamp'] = Stamp::model()->fetchStampById($static['stamp']);
        }
        $record = ReportRecord::model()->fetchAll('repid = :repid', array(':repid' => $repid));
        if (!empty($record)) {
            $record = ReportField::transferField($record);
        }
        $report['record'] = $record;
        //取附件
        $report['attach'] = array();
        if (isset($report['attachmentid']) && !empty($report['attachmentid'])){
            $attach = Attach::getAttach($report['attachmentid']);
            foreach ($attach as $value){
                array_push($report['attach'], $value);
            }
        }
        //上一篇汇报id和下一篇汇报id
        if (!empty($type)){
            $nextAndPrev = $this->getNextAndPrevByType($repid, $type);
            $report['next'] = $nextAndPrev['next'];
            $report['prev'] = $nextAndPrev['prev'];
        }

        //返回接收者
        if (empty($report['toid'])){
            $report['receiver'] = array();
        }else{
            $receiver = User::model()->findRealnameIndexByUid(explode(',', $report['toid']));
            $report['receiver'] = implode(',', $receiver);
        }

        //返回已读人员uid数组
        $reader = ModuleReader::model()->getReader($repid, true);
        $returnReader = array();
        if (!empty($reader['reader'])){
            foreach ($reader['reader'] as $value){
                $readerUser = User::model()->fetchByUid($value);
                $returnReader[] = array(
                    'realname' => $readerUser['realname'],
                    'uid' => $readerUser['uid'],
                    'avatar_small' => $readerUser['avatar_small'],
                    'space_url' => $readerUser['space_url'],
                );
            }
        }
        $report['reader'] = $returnReader;
        $report['count'] = $reader['count'];
        $report['readercount'] = empty($reader['reader']) ? 0 : count($reader['reader']);

        //是否可以显示盖章
        $reportCreateUser = $report['uid'];
        $createUser = User::model()->fetchByUid($reportCreateUser);
        if ($this->issetStamp() && $createUser['upuid'] == $uid && Role::checkRouteAccess('report/api/getstamp')){
            $report['isstamp'] = true;
        }else{
            $report['isstamp'] = false;
        }

        ModuleReader::model()->addReader($repid, $uid);

        //更新汇报为已读状态
        if ($report['uid'] != $uid){
            Report::model()->modify( $repid, array( 'isreview' => 1) );
        }

        //判断后台是否开启自动评阅，若是，把该总结改成已评阅
        $dashboardConfig = $this->getReportConfig();
        if ($dashboardConfig['stampenable'] && $dashboardConfig['autoreview']) {
            $this->changeIsreview($repid);
        }

        if ($report['uid'] == $uid){
            $report['isdel'] = true;
        }else{
            $report['isdel'] = false;
        }

        $reportShowUrl = $this->controller->createUrl('default/index');
        $reportShowUrl .=  "#receive/detail/{$repid}";
        NotifyMessage::model()->setReadByUrl($uid, $reportShowUrl);
        Ibos::app()->controller->ajaxReturn(array(
           'isSuccess' => true,
            'msg' => '',
            'data' => $report,
        ));
    }

    /**
     * 把某篇总结改成已评阅
     * @param integer $repid 汇报id
     */
    private function changeIsreview($repid)
    {
        $report = Report::model()->fetchByPk($repid);
        //判断是否是直属上司，只给直属上司自动评阅
        if (!empty($report) && UserUtil::checkIsUpUid($report['uid'], Ibos::app()->user->uid)){
            $static = ReportStats::model()->fetch("repid = :repid", array(":repid" => $repid));
            if (empty($static)){
                $stamp = $this->getAutoReviewStamp();
                Report::model()->modify( $repid, array( 'isreview' => 1, 'stamp' => $stamp ) );
                ReportStats::model()->scoreReport( $report['repid'], $report['uid'], $stamp );
            }else{
                Report::model()->modify( $repid, array( 'isreview' => 1 ) );
            }
        }
    }

    /**
     * 得到上一个汇报id或者下一个汇报id
     * @param integer $currentRepid 当前的汇报id
     * @return array
     */
    private function getNextAndPrevByType($currentRepid, $type)
    {
        $uid = Ibos::app()->user->uid;
        $condition = ReportUtil::getListCondition($type, $uid);
        $nextAndPrev = array();
        $repids = Ibos::app()->db->createCommand()
            ->select('repid')
            ->from('{{report}}')
            ->where($condition)
            ->queryColumn();
        if (empty($repids)){
            $nextAndPrev['next'] = '';
            $nextAndPrev['prev'] = '';
            return $nextAndPrev;
        }
        $key = array_search($currentRepid, $repids);
        $nextKey = $key + 1;
        $prevKey = $key - 1;
        $next = isset($repids[$nextKey]) ? $repids[$nextKey] : '';
        $nextAndPrev['next'] = $next;
        $prev = isset($repids[$prevKey]) ? $repids[$prevKey] : '';
        $nextAndPrev['prev'] = $prev;
        return $nextAndPrev;
    }

}