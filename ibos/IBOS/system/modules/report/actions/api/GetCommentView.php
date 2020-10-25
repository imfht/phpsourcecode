<?php
/**
 * 汇报web端获得评论页面
 */

namespace application\modules\report\actions\api;

use application\core\utils\Env;
use application\core\utils\Ibos;
use application\core\utils\StringUtil;
use application\modules\report\model\Report;
use application\modules\role\utils\Role;
use application\modules\user\model\User;
use application\modules\report\utils\Report as ReportUtil;

class GetCommentView extends Base
{

    public function run()
    {
        $repid= Env::getRequest('repid');
        $report = Report::model()->fetchByPk($repid);
        $uid = Ibos::app()->user->uid;
        $reportCreateUser = $report['uid'];
        $createUser = User::model()->fetchByUid($reportCreateUser);
        //是否开启图章
        if ($this->issetStamp() && ($createUser['upuid'] == $uid) && Role::checkRouteAccess('report/api/getstamp')){
            $showStamp = 1;
        }else{
            $showStamp = 0;
        }
        $stamps = ReportUtil::getEnableStamp();
        $stampList = array();
        if (!empty($stamps)){
            foreach ($stamps as  $stamp){
                $stampList[] = array(
                    'path' => $stamp['icon'],
                    'point' => $stamp['score'],
                    'stamp' => $stamp['stamp'],
                    'title' => $stamp['code'],
                    'value' => $stamp['stampid'],
                );
            }
        }
        //汇报使用哈希值进行控制路由
        $urlRoot = $this->controller->createUrl('default/index');
        $url = $urlRoot . "#receive/detail/{$repid}";
        $this->controller->widget('application\modules\report\widgets\ReportComment', array(
            'module' => 'report',
            'table' => 'report',
            'attributes' => array(
                'rowid' => $repid,
                'moduleuid' => $uid,
                'touid' => $report['uid'],
                'module_rowid' => $repid,
                'module_table' => 'report',
                'url' => $url,
                'showStamp' => $showStamp,
                'stamps' => empty($stampList) ? '' : $stampList,
                'detail' => Ibos::lang('Comment my report', '', array(
                    '{url}' => $url,
                    '{title}' => StringUtil::cutStr($report['subject'], 50)
                )),
            )
        ));
    }
}
