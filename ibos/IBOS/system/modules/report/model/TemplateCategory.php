<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/30
 * Time: 17:03
 */

namespace application\modules\report\model;


use application\core\model\Model;

class TemplateCategory extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{template_category}}';
    }

    /*
     * 根据模板id得到分类名称
     * @param integer $cateid 分类id
     */
    public function getTemplateCategoryName($cateid)
    {
        $category = $this->find('cateid = :cateid', array(':cateid' => $cateid));
        return $category['categoryname'];
    }
}