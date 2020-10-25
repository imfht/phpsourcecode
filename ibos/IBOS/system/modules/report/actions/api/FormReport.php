<?php
/**
 * 发送汇报页面和编辑汇报页面接口，得到模板数据或者对应汇报的详细信息
 */

namespace application\modules\report\actions\api;

use application\core\utils\Attach;
use application\core\utils\Env;
use application\core\utils\Ibos;
use application\modules\report\model\Report;
use application\modules\report\model\ReportRecord;
use application\modules\report\model\Template;
use application\modules\report\model\TemplateField;
use application\modules\user\model\User;

class FormReport extends Base
{

    public function run()
    {
        $repid = Env::getRequest('repid');
        if (isset($repid) && !empty($repid)){//编辑页面
            $report = Report::model()->fetchByPk($repid);
            $template = Template::model()->fetchByPk($report['tid']);
            $report['tname'] = $template['tname'];
            $report['attach'] = array();
            //取附件
            if (isset($report['attachmentid']) && !empty($report['attachmentid'])){
                $attach = Attach::getAttach($report['attachmentid']);
                foreach ($attach as $value){
                    $value['name'] = $value['filename'];
                    $value['size'] = $value['filesize'];
                    $value['type'] = $value['filetype'];
                    $value['icon'] = $value['iconbig'];
                    array_push($report['attach'], $value);
                }
            }
            $record = ReportRecord::model()->getRecord($repid, true);
            $reports = array();
            $reports['template'] = $report;
            $reports['templateField'] = $record;
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => '',
                'data' => $reports,
            ));
        }else{//添加页面
            $tid = Env::getRequest('tid');
            $uid = Ibos::app()->user->uid;
            $template = Template::model()->getTemplate($tid);
            $chargeUids = $this->getcharge($uid);
            $uptypeArrays = explode(',', $template['uptype']);
            $uptypeUid = array();
            foreach ($uptypeArrays as $uptypeArray){
                if (isset($chargeUids[$uptypeArray])){
                    $uptypeUid[] = $chargeUids[$uptypeArray];
                }
            }
            $defaultuid = implode(',', $uptypeUid). ',' .$template['upuid'];
            $defaultuidArray = array_filter(array_unique(explode(',', trim($defaultuid, ','))));
            $template['defaultuid'] = implode(',', $defaultuidArray);
            for ($i = 0; $i < count($template); $i++){
                $template['subject'] = $template['tname'];
            }
            $templateField = TemplateField::model()->getFieldByTid($tid);
            $templates['template'] = $template;
            $templates['templateField'] = $templateField;
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => '',
                'data' => $templates,
            ));
        }
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
        $user = User::model()->fetchByUid($uid);
        if (!isset($user['upuid']) || empty($user['upuid']) || $user['uid'] == $user['upuid']){
            return $uids;
        }else{
            $i = $i + 1;
            $uids[$i] = $user['upuid'];
            $this->getcharge($user['upuid']);
        }
        return $uids;
    }
}