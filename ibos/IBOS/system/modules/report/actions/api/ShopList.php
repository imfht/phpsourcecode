<?php
/*
*汇报模板商城列表接口
*/

namespace application\modules\report\actions\api;

use application\core\utils\Api;
use application\core\utils\Ibos;
use application\modules\report\model\TemplateAdd;
use application\modules\role\utils\Role;

class ShopList extends Base
{
    const SHOPURL = 'http://api.ibos.cn/v3/report/gettemplatelist';

    public function run()
    {
        $api = new Api();
        $result = $api->fetchResult(self::SHOPURL);
        $resultArray = json_decode($result, true);
        $templateLists = $resultArray['data'];
        $lists = array();
        foreach ($templateLists as $templateList){
            $templateList['isadd'] = TemplateAdd::model()->isAddTemplate($templateList['tid']);
            $lists[$templateList['categoryname']][] = $templateList;
        }
        $shopList = array();
        foreach ($lists as $key => $list){
            $shopList[] = array(
                'catename' => $key,
                'template' => $lists[$key]
            );
        }

        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' =>true,
            'msg' => '',
            'data' => $shopList,
            'isAdd' => Role::checkRouteAccess('report/api/savetemplate'),
        ));
    }

}