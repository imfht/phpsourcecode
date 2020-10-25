<?php
/**
 * 添加模板显示页和编辑模板显示页
 */

namespace application\modules\report\actions\api;

use application\core\utils\Env;
use application\core\utils\Ibos;
use application\modules\report\model\Template;
use application\modules\report\model\TemplateField;
use application\modules\report\utils\Template as TemplateUtil;

class FormTemplate extends Base
{

    const LONGTEXT = "长文本类型";
    const SHORTTEXT = "短文本类型";
    const NUMBER = "数字类型";
    const DATETIME = "日期与时间类型";
    const TIME = "时间类型";
    const DATE = "日期类型";
    const SELECT = "下拉类型";
    public function run()
    {
        $tid = Env::getRequest('tid');
        if (empty($tid)){//添加模板
            //字段类型
            $fieldtype = array(
                array('fieldtype' => 1, 'typename' => self::LONGTEXT),
                array('fieldtype' => 2, 'typename' => self::SHORTTEXT),
                array('fieldtype' => 3, 'typename' => self::NUMBER),
                array('fieldtype' => 4, 'typename' => self::DATETIME),
                array('fieldtype' => 5, 'typename' => self::TIME),
                array('fieldtype' => 6, 'typename' => self::DATE),
                array('fieldtype' => 7, 'typename' => self::SELECT),
            );
            $filenameList = TemplateUtil::getPictureName();
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => '',
                'data' => array(
                    'fieldtype' => $fieldtype,
                    'filenames' => array_keys($filenameList),
                )
            ));
        }else{//编辑模板
            $template = Template::model()->fetchByPk($tid);
            $fields = TemplateField::model()->getFieldByTid($tid);
            $return = array(
                'template' => $template,
                'fields' => $fields,
            );
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => '',
                'data' => $return,
            ));
        }
    }
}