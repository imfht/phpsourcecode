<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/30
 * Time: 14:37
 */

namespace application\modules\report\model;


use application\core\model\Model;
use application\core\utils\Ibos;

class TemplateField extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{template_field}}';
    }

    /**
     * 根据模板id得到对应的模板字段
     * @param integer $tid 模板id
     * @param array
     */
    public function getFieldByTid($tid)
    {
        $templateFields = Ibos::app()->db->createCommand()
            ->select('*')
            ->from($this->tableName())
            ->where('tid = :tid', array(
                ':tid' => $tid,
            ))
            ->queryAll();
        $fileds = array();
        foreach ($templateFields as $templateField){
            $templateField['fieldid'] = $templateField['fid'];
            unset($templateField['fid']);
            if ($templateField['fieldtype'] == 7){
                $templateField['fieldvalue'] = explode(',', $templateField['fieldvalue']);
                $fileds[] = $templateField;
            }else{
                $fileds[] = $templateField;
            }
        }
        return $fileds;
    }

    /**
     * 新增模板字段,用户添加系统模板或者自己创建模板
     * @param integer $tid 模板ID，添加系统模板时用到
     * @param integer $newTemplateId 新模板的ID
     * @param array $fields 模板字段
     * @return boolean
     */
    public function addTemplateField($tid = '', $newTemplateId, $fields = array())
    {
        if (empty($fields)){//添加系统模板
            $templateFields = Ibos::app()->db->createCommand()->from($this->tableName())
                ->select('fieldname,iswrite,fieldtype,fieldvalue,fieldsort')
                ->where('tid = :tid', array(':tid' => $tid))
                ->queryAll();
            $count = count($templateFields);
            for ($i = 0; $i < $count; $i++) {
                $templateFields[$i]['tid'] = $newTemplateId;
                $templateFields[$i]['fieldname'] = \CHtml::encode($templateFields[$i]['fieldname']);
            }
            $affectRow = Ibos::app()->db->schema->commandBuilder
                ->createMultipleInsertCommand($this->tableName(), $templateFields)
                ->execute();
        }else {//创建自己模板
            $count = count($fields);
            for ($i =0; $i < $count; $i++){
                $fields[$i]['tid'] = $newTemplateId;
                $fields[$i]['fieldname'] = \CHtml::encode($fields[$i]['fieldname']);
            }
            $affectRow = Ibos::app()->db->schema->commandBuilder
                ->createMultipleInsertCommand($this->tableName(), $fields)
                ->execute();
        }

        if ($affectRow > 0) {
            return true;
        }
        return false;
    }

    /**
     * 添加模板商城模板字段
     * @param array $field 模板字段
     * @param integer $newTemplateId 模板id
     */
    public function addShopTemplateField($fields, $newTemplateId)
    {
        $count = count($fields);
        for ($i =0; $i < $count; $i++){
            $fields[$i]['tid'] = $newTemplateId;
        }
        Ibos::app()->db->schema->commandBuilder
            ->createMultipleInsertCommand($this->tableName(), $fields)
            ->execute();
    }
}