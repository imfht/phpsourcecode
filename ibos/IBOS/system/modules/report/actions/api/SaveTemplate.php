<?php
/**
 * 添加模板页面提交，编辑模板页面提交，编辑页面提交分两种情况，一种是用户有设置模板的权限
 * 另外一种是没有设置模板权限，编辑提交的思路是，删除之前的模板，重新添加模板
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\TemplateAdd;
use application\modules\report\model\TemplateSort;
use application\modules\role\utils\Role as RoleUtil;
use application\modules\report\model\Template;
use application\modules\report\model\TemplateField;

class SaveTemplate extends Base
{

    public function run()
    {
        $data = $this->data;
        $uid = Ibos::app()->user->uid;
        $template = $data['template'];
        if (empty($template['tname'])){
            $this->controller->ajaxReturn(array(
                'isSuccess' => false,
                'msg' => Ibos::lang('Not empty template name'),
                'data' => '',
            ));
        }
        $fields = $data['fields'];
        if (empty($fields)){
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => false,
                'msg' => Ibos::lang('Field is no empty'),
                'data' => ''
            ));
        }
        $templateList = array(
            'tname' => $template['tname'],
            'autonumber' => empty($template['autonumber']) ? '' : $template['autonumber'],
            'pictureurl' => empty($template['pictureurl']) ? 'default' : $template['pictureurl'],
            'cateid' => empty($template['catid']) ? 0 : $template['catid'],
            'addtime' => TIMESTAMP,
            'adduser' => $uid,
            'isnew' => 1,
        );
        if (empty($template['tid'])){
            $templateList['deptid'] = empty($template['uid']) ? 'alldept' : '';
            $templateList['uid'] = $template['uid'];
            $templateList['uptype'] = $template['uptype'];
            $templateList['upuid'] =  $template['upuid'];
            $lang = Ibos::lang('Add template success');
        }else{
            $oldTemplate = Template::model()->fetchByPk($template['tid']);
            $templateList['deptid'] = $oldTemplate['deptid'];
            $templateList['uid'] = $oldTemplate['uid'];
            $templateList['uptype'] = $oldTemplate['uptype'];
            $templateList['upuid'] =  $oldTemplate['upuid'];
            Template::model()->deleteTemplate($template['tid']);
            $lang = Ibos::lang('Edit template success');
        }
        $templateId = Template::model()->addTemplateForUser($templateList);
        //编辑的时候修改排序模板表对应的tid为新的模板id
        if (!empty($template['tid'])){
            TemplateAdd::model()->updateAll(array('tid' => $templateId), 'tid = :tid', array(':tid' => $template['tid']));
            TemplateSort::model()->updateTid($template['tid'], $templateId);
        }
        $newTemplateField = TemplateField::model()->addTemplateField('', $templateId, $fields);
        if ($newTemplateField) {
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => $lang,
                'data' => ''
            ));
        }
    }
}