<?php
namespace app\sale\service;
/**
 * 商品接口
 */
class MallService extends \app\base\service\BaseService {

    public function adminContentHtml($hasId = 0) {
        return target('sale/SaleContent')->hookHtml($hasId, 'mall');
    }

    public function adminContentSave($hasId = 0) {
        return target('sale/SaleContent')->HookSave($hasId, 'mall');
    }

    public function adminContentDel($hasId = 0) {
        return target('sale/SaleContent')->HookDel($hasId, 'mall');
    }


}

