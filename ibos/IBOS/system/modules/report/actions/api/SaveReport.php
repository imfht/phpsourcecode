<?php
/**
 * 汇报数据保存接口，编辑提交和添加提交
 */

namespace application\modules\report\actions\api;

use application\core\utils\Attach;
use application\core\utils\Ibos;
use application\modules\department\model\Department;
use application\modules\message\model\Notify;
use application\modules\report\core\ReportField;
use application\modules\report\model\Report;
use application\modules\report\core\ReportField as ReportFieldUtil;
use application\modules\report\model\ReportRecord;
use application\modules\report\model\Template;
use application\modules\user\model\User;
use application\modules\user\utils\User as UserUtil;
use application\modules\weibo\utils\Common as WbCommonUtil;
use application\modules\weibo\utils\Feed as WbfeedUtil;
use application\core\model\Log;
use application\core\utils\StringUtil;

class SaveReport extends Base
{

    public function run()
    {
        $data = $this->data;
        $uid = Ibos::app()->user->uid;
        $fields = $data['fields'];
        $template = Template::model()->fetchByPk($data['tid']);
        $error = ReportFieldUtil::filterField($fields);
        if (empty($error)){
            //过滤自己发汇报给自己
            $pusishScope = explode(',', $data['toid']);
            $keys = array_keys($pusishScope, $uid);
            if (!empty($keys)){
                foreach ($keys as $key){
                    unset($pusishScope[$key]);
                }
            }
            //过滤好要发送汇报的用户，其中包括了模板的设置的默认人员
            $pusishScope = implode(',', $pusishScope);
            //更新附件
            if (!empty($data['attachmentid'])){
                Attach::updateAttach($data['attachmentid']);
            }
            //如果设置模板没有选择主管和默认发给谁，同时也没有选择发给谁，用户只能看到自己发送的汇报
            $report = array(
                'uid' => $uid,
                'subject' => $this->replaceAutoNumber($template['autonumber'], $template['tname'], $uid, $fields),
                'addtime' => TIMESTAMP,
                'tid' => $data['tid'],
                'remark' => str_replace("\n", "<br >", \CHtml::encode($data['remark'])),
                'toid' => empty($pusishScope) && empty($template['upuid']) ? '' : $pusishScope,
                'attachmentid' => isset($data['attachmentid']) ? $data['attachmentid'] : '',
                'status' => $data['status'],
            );
            //h5端有位置定位信息
            if (isset($data['place'])){
                $report['place'] = StringUtil::filterStr($data['place']);
            }
            if (isset($data['repid']) && !empty($data['repid'])){//编辑汇报提交
                Report::model()->updateReport($data['repid'], $report);
                $fields = ReportField::handleField($fields, $data['repid']);
                ReportRecord::model()->updateRecord($fields);
                if ($report['status'] == 1){
                    $this->sendUser($uid, $pusishScope, $data['repid'], $template['tname'], $fields[0]['content']);
                    Ibos::app()->controller->ajaxReturn(array(
                        'isSuccess' => true,
                        'msg' => Ibos::lang('Send report success'),
                        'data' => '',
                    ));
                }else{
                    Ibos::app()->controller->ajaxReturn(array(
                        'isSuccess' => true,
                        'msg' => Ibos::lang('Save report success'),
                        'data' => '',
                    ));
                }
            }else{//添加汇报
                $repid = Report::model()->addReport($report);
                $fields = ReportField::handleField($fields, $repid);
                $offectRow = ReportRecord::model()->addRecord($fields);
                if ($report['status'] == 1){
                    $this->sendUser($uid, $pusishScope, $repid, $template['tname'], $fields[0]['content']);
                    if ($offectRow){
                        Ibos::app()->controller->ajaxReturn(array(
                            'isSuccess' => true,
                            'msg' => Ibos::lang('Send report success'),
                            'data' => '',
                        ));
                    }else{
                        Ibos::app()->controller->ajaxReturn(array(
                            'isSuccess' => true,
                            'msg' => Ibos::lang('Send report fail'),
                            'data' => '',
                        ));
                    }
                }else{
                    Ibos::app()->controller->ajaxReturn(array(
                        'isSuccess' => true,
                        'msg' => Ibos::lang('Save report success'),
                        'data' => '',
                    ));
                }
            }
        }else{
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => false,
                'msg' => $error,
                'data' => array(),
            ));
        }
    }

    /**
     * 推送消息
     * @param integer $uid 当前用户
     * @param string $toid 接收者uid
     * @param integer $repid 汇报id
     * @param string $subject 模板标题
     * @param string $content 发送内容
     */
    private function sendUser($uid, $toid, $repid, $subject, $content)
    {
        $wbconf = WbCommonUtil::getSetting(true);
        $toid = trim($toid, ',');
        $urlRoot = Ibos::app()->urlManager->createUrl('report/default/index');
        $url = $urlRoot . "#receive/detail/{$repid}";
        if (isset($wbconf['wbmovement']['report']) && $wbconf['wbmovement']['report']
            == 1
        ) {
            $data = array(
                'title' => Ibos::lang('Feed title', '',
                    array(
                        '{subject}' => html_entity_decode($subject),
                        '{url}' => $url
                    )),
                'body' => StringUtil::cutStr($content, 140),
                'actdesc' => Ibos::lang('Post report'),
                'userid' => $toid,
                'deptid' => '',
                'positionid' => '',
            );
            WbfeedUtil::pushFeed($uid, 'report', 'report', $repid, $data);
        }
        // 更新积分
        UserUtil::updateCreditByAction('addreport', $uid);
        // 给汇报对象发提醒
        $toidArr = array_filter(array_unique(explode(',', $toid)));
        if (!empty($toidArr)) {
            $config = array(
                '{sender}' => User::model()->fetchRealnameByUid($uid),
                '{subject}' => html_entity_decode($subject),
                '{url}' => $url
            );
            Notify::model()->sendNotify($toidArr, 'report_message',
                $config, $uid);
        }
        /**
         * 日志记录
         */
        $log = array(
            'user' => Ibos::app()->user->username,
            'ip' => Ibos::app()->setting->get('clientip'),
            'isSuccess' => 1
        );
        Log::write($log, 'action', 'module.report.default.save');
    }

    /**
     * 获得当前用户对应各个主管的uid
     * @param integer $uid 当前用户uid
     * @return array
     */
    private function getcharge($uid)
    {
        static $uids = array();
        static  $i = 0;
        $user = User::model()->fetchByPk($uid);
        if (!isset($user['upuid']) || empty($user['upuid'])){
            return $uids;
        }else{
            $i = $i + 1;
            $uids[$i] = $user['upuid'];
            $this->getcharge($user['upuid']);
        }
        return $uids;
    }

    /**
     * 格式化自动文号
     * @param integer $tid 模板id
     * @param integer $uid 用户id
     * @param array $fields 字段内容
     */
    private function replaceAutoNumber($autoNumber, $templateName, $uid, $fields = array())
    {
        if (empty($autoNumber)){
            return $templateName;
        }else{
            $user = User::model()->fetchByUid($uid);
            $deparment = Department::model()->fetchDeptNameByUid($uid);
            $autoNumber = str_replace('{Y}', date('Y', time()), $autoNumber);
            $autoNumber = str_replace('{M}', date('n', time()), $autoNumber);
            $autoNumber = str_replace('{D}', date('j', time()), $autoNumber);
            $autoNumber = str_replace('{H}', $deparment, $autoNumber);
            $autoNumber = str_replace('{U}', $user['realname'], $autoNumber);
            $autoNumber = str_replace('{T}', $templateName, $autoNumber);
            //过滤提取字段名
            preg_match_all('/\{(.+?)\}/', $autoNumber, $matchs);
            if (!empty($matchs)){
                $changeFields = array();
                foreach ($fields as $field){
                    if ($field['fieldtype'] == 2){
                        $changeFields[$field['fieldname']] = $field['content'];
                    }
                }
                $filterMatchs = array();
                $other = $matchs[1];
                foreach ($other as $match){
                    if ($match == 'Y' || $match == 'M' || $match == 'D' || $match == 'U' || $match == 'D' || $match == 'T'){
                        unset($match);
                    }else{
                        $filterMatchs[] = $match;
                    }
                }
                foreach ($filterMatchs as $filterMatch){
                    if (isset($changeFields[$filterMatch])){
                        $autoNumber = str_replace('{'.$filterMatch.'}', $changeFields[$filterMatch], $autoNumber);
                    }
                }
            }
            return $autoNumber;
        }
    }
}