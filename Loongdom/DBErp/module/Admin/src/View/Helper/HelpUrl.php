<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;

class HelpUrl extends AbstractHelper
{

    const HELP_URL  = '';
    const URL_STATE = true;

    /**
     * 帮助网址
     * @param string $name
     * @return bool|string
     */
    public function __invoke(string $name)
    {
        if(!self::URL_STATE) return false;

        $urlArray = [
            /*=====================系统========================*/
            'adminList' => '',
            'adminAdd'  => '',
            'adminEdit' => '',
            'adminDel'  => '',
            'adminPassword' => '',

            'adminGroupList' => '',
            'adminGroupAdd' => '',
            'adminGroupEdit' => '',

            'regionList'      => '',
            'regionAdd'       => '',
            'regionEdit'      => '',

            'appList'   => '',
            'appAdd'    => '',

            'operLogList' => '',
            /*=====================仓库========================*/
            'goodsList' => '',
            'goodsAdd'  => '',
            'goodsEdit' => '',
            'goodsPriceTrend' => '',
            'goodsWarehouse' => '',

            'goodsCategoryList' => '',
            'goodsCategoryAdd'  => '',
            'goodsCategoryEdit' => '',

            'warehouseList' => '',
            'warehouseAdd'  => '',
            'warehouseEdit' => '',

            'positionList'  => '',
            'positionAdd'   => '',
            'positionEdit'  => '',

            'unitList'      => '',
            'unitAdd'       => '',
            'unitEdit'      => '',

            'brandList'      => '',
            'brandAdd'       => '',
            'brandEdit'      => '',

            /*=====================销售========================*/
            'salesOrderList' => '',
            'salesOrderShow' => '',
            'salesOrderAdd'  => '',
            'salesOrderEdit' => '',
            'salesOrderView' => '',
            'salesSendOrder' => '',

            'salesSendOrderList' => '',

            'salesOrderReturnList' => '',

            /*=====================采购========================*/
            'pOrderList'    => '',
            'pOrderAdd'     => '',
            'pOrderEdit'    => '',
            'pOrderView'    => '',
            'pOrderReturn'  => '',

            'orderReturnList'      => '',
            'orderReturnAdd'       => '',

            'warehouseOrderList'    => '',
            'warehouseOrderAdd'     => '',
            'warehouseOrderEdit'    => '',

            /*=====================库存========================*/
            'otherWarehouseOrderList' => '',
            'otherWarehouseOrderAdd' => '',
            'otherWarehouseOrderEdit' => '',

            'stockCheckList' => '',
            'stockCheckAdd' => '',
            'stockCheckEdit' => '',

            /*=====================客户========================*/
            'customerList'  => '',
            'customerAdd'   => '',
            'customerEdit'  => '',

            'customerCategoryList'  => '',
            'customerCategoryAdd'   => '',
            'customerCategoryEdit'  => '',

            'supplierList'  => '',
            'supplierAdd'   => '',
            'supplierEdit'  => '',

            'supplierCategoryList'  => '',
            'supplierCategoryAdd'   => '',
            'supplierCategoryEdit'  => '',

            /*=====================商城========================*/
            'shopOrderList'  => '',
            'shopOrderGoodsList' => '',

            /*=====================资金========================*/
            'financePayableList' => '',
            'financePayableView' => '',
            'financeAddPayable'  => '',
            'financePayableLog'  => '',

            'receivablesList'       => '',
            'accountsAddReceivable' => '',
            'accountsReceivableLog' => ''
        ];

        if(!isset($urlArray[$name])) return false;
        $url = '<a href="'.self::HELP_URL.$urlArray[$name].'" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i> '.$this->getView()->plugin('translate')('查看帮助').'</a>';

        return $url;
    }
}