<?php
/**
 * 用户添加系统模板,思路是添加系统模板时拷贝一份，数据库生成新的数据
 */

namespace application\modules\report\actions\api;

use application\core\utils\Api;
use application\core\utils\Ibos;
use application\modules\report\model\Template;
use application\modules\report\model\TemplateAdd;
use application\modules\report\model\TemplateField;

class AddTemplate extends Base
{
    const ADDURL = 'http://api.ibos.cn/v3/report/gettemplate';

    public function run()
    {
        $data = $this->data;
        $uid = Ibos::app()->user->uid;
        if (isset($data['tid']) && !empty($data['tid'])){//添加系统模板
            $tid = $data['tid'];
            $result = Api::getInstance()->fetchResult(self::ADDURL, array('tid' => $tid), 'post');
            $resultArray = json_decode($result, true);
            $templateAll = $resultArray['data'];
            $template = $templateAll['template'];
            $fields = $templateAll['fields'];
            $newTemplateId = Template::model()->addTemplateForSystem($template, $uid);
            TemplateField::model()->addShopTemplateField($fields, $newTemplateId);
            TemplateAdd::model()->addTemplateUser($newTemplateId, $tid, $uid);
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => Ibos::lang('Add template success'),
                'data' => ''
            ));
        }
    }

}