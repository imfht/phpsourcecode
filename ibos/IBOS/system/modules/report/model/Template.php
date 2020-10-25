<?php

namespace application\modules\report\model;

use application\core\model\Model;
use application\core\utils\Ibos;
use application\modules\role\utils\Role;
use application\modules\report\utils\Template as TemplateUtil;

class Template extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{template}}';
    }

    /**
     * 添加系统模板
     * @param integer $tid 模板ID
     * @param integer $uid 用户ID
     * @return integer
     */
    public function addTemplateForSystem($template, $uid)
    {
        $insertValue = array(
            'tname' => $template['tname'],
            'pictureurl' => $template['pictureurl'],
            'cateid' => $template['cateid'],
            'description' => $template['description'],
            'deptid' => 'alldept',
            'addtime' => TIMESTAMP,
            'adduser' => $uid,
            'isnew' => 1
        );
        Ibos::app()->db->createCommand()->insert($this->tableName(), $insertValue);
        $newTemplateId = Ibos::app()->db->getLastInsertID();
        return $newTemplateId;
    }

    /**
     * 用户自己创建模板
     * @param array $template 模板数组
     * @return integer
     */
    public function addTemplateForUser($template)
    {
        Ibos::app()->db->createCommand()->insert($this->tableName(),$template);
        $newTemplateId = Ibos::app()->db->getLastInsertID();
        return $newTemplateId;
    }

    /**
     * 得到可以管理的模板
     * @return array
     */
    public function getTemplateForManager($apiType = '')
    {
        $templates = Ibos::app()->db->createCommand()
            ->select('*')
            ->from($this->tableName())
            ->where('isnew = :isnew', array(
                ':isnew' => 1,
            ))
            ->order('addtime DESC')
            ->queryAll();
        for ($i = 0; $i < count($templates); $i++){
            $templates[$i]['color'] = TemplateUtil::getPictureName($templates[$i]['pictureurl']);
            $templates[$i]['field'] = Ibos::app()->db->createCommand()
                ->select('fieldname,fieldtype')
                ->from('{{template_field}}')
                ->where('tid = :tid', array(
                    ':tid' => $templates[$i]['tid'],
                ))
                ->queryAll();
            $templates[$i]['settemplate'] = Role::checkRouteAccess('report/api/settemplate');
            $templates[$i]['edittemplate'] = Role::checkRouteAccess('report/api/savetemplate');
            $templates[$i]['deltemplate'] = Role::checkRouteAccess('report/api/deltemplte');
        }
        if ($apiType == 'web'){
            $templateLists = array();
            foreach ($templates as $template){
                $categoryName = TemplateCategory::model()->getTemplateCategoryName($template['cateid']);
                $templateLists[$categoryName][] = $template;
            }
            $list = array();
            foreach ($templateLists as $key => $templateList) {
                $list[] = array(
                    'catename' => $key,
                    'template' => $templateLists[$key],
                );
            }
            return $list;
        }else{
            return $templates;
        }
    }

    /**
     * 得到用户可以使用的模板
     * @param string $condition 查询条件
     * @return array
     */
    public function getTemplateForUser($condition = '', $apiType = '')
    {
        $uid = Ibos::app()->user->uid;
        $templates = array();
        $sortTemplate = TemplateSort::model()->getSortTemplateByUid($uid);
        if (empty($sortTemplate)){
            $templates = Ibos::app()->db->createCommand()->from($this->tableName())
                ->select('*')
                ->where($condition)
                ->order('addtime DESC')
                ->queryAll();
        }else{
            foreach ($sortTemplate as $tid) {
                $templates[] = $this->fetchByPk($tid);
            }
        }
        for ($i = 0; $i < count($templates); $i++){
            $templates[$i]['color'] = TemplateUtil::getPictureName($templates[$i]['pictureurl']);
            $templates[$i]['tname'] = html_entity_decode($templates[$i]['tname']);
        }
        if ($apiType == 'web'){
            $templateLists = array();
            foreach ($templates as $template){
                $categoryName = TemplateCategory::model()->getTemplateCategoryName($template['cateid']);
                $templateLists[$categoryName][] = $template;
            }
            $list = array();
            foreach ($templateLists as $key => $templateList) {
                $list[] = array(
                    'catename' => $key,
                    'template' => $templateLists[$key],
                );
            }
            return $list;
        }else{
            return $templates;
        }
    }


    /**
     * 得到模板ID的设置值
     * @param integer $tid 模板ID
     * @return array
     */
    public function getTemplateSet($tid)
    {
        $set = Ibos::app()->db->createCommand()->from($this->tableName())
            ->select('uid,uptype,upuid')
            ->where('tid = :tid', array(':tid' => $tid))
            ->queryAll();
        return $set[0];
    }

    /**
     * @param integer $tid 模板id
     * @param string $uid  可用使用模板uid，如1,2,3
     * @param string $uptype 主管类型，如1,2,3
     * @param string $upuid 默认发送uid 如1,2,3
     * @param string $deptid 如果uid为空，则为alldept
     */
    public function setTemplate($tid, $uid, $uptype, $upuid, $deptid = '')
    {
        Ibos::app()->db->createCommand()->update($this->tableName(), array(
            'deptid' => $deptid,
            'uid' => $uid,
            'uptype' => $uptype,
            'upuid' => $upuid,
        ), "tid = :tid", array(
            ':tid' => $tid,
        ));
    }

    /**
     * 获得模板
     * @param integer $tid 模板ID
     * @return array
     */
    public function getTemplate($tid)
    {
        return $this->fetchByPk($tid);
    }

    /**
     * 模板的默认发送uid
     * @param integer $tid 模板ID
     * @return string
     */
    public function getDefaultUid($tid)
    {
        $uid = Ibos::app()->db->createCommand()->from($this->tableName())
            ->select('upuid')
            ->where('tid = :tid', array(
                ':tid' => $tid,
            ))
            ->queryScalar();
        return $uid;
    }

    /**
     * 删除模板
     * @param integer $tid 模板id
     */
    public function deleteTemplate($tid){
        $row = $this->updateByPk($tid, array('isnew' => 0), 'tid = :tid', array(
            ':tid' => $tid
        ));
        if ($row > 0){
            //删除排序模板中对应的模板id
            TemplateSort::model()->deleteAll('tid = :tid', array(':tid' => $tid));
            return true;
        }
        return false;
    }

    /**
     *根据模板id得到对应的图片名称
     * @param integer $tid 模板id
     * @return string
     */
    public function getIcon($tid)
    {
        $icon = Ibos::app()->db->createCommand()
            ->select('pictureurl')
            ->from($this->tableName())
            ->where('tid = :tid', array(':tid' => $tid))
            ->queryScalar();
        return $icon;
    }

    /**
     * 根据模板id得到对应的自动文号
     */
    public function getAutoNumber($tid)
    {
        $autoNumber = Ibos::app()->db->createCommand()
            ->select('autonumber')
            ->from($this->tableName())
            ->where('tid = :tid', array(':tid' => $tid))
            ->queryScalar();
        return $autoNumber;
    }
}