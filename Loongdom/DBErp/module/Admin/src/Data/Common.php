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

namespace Admin\Data;

use Zend\Mvc\I18n\Translator;

class Common
{
    /**
     * 状态(通用)
     * @param Translator $translator
     * @return array
     */
    public static function state(Translator $translator)
    {
        return [1 => $translator->translate('启用'), 0 => $translator->translate('禁用')];
    }

    /**
     * 绑定应用的种类
     * @param Translator $translator
     * @return array
     */
    public static function appType(Translator $translator)
    {
        return ['dbshop' => $translator->translate('DBShop商城系统'), 'other' => $translator->translate('其他系统')];
    }

    /**
     * 采购订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function purchaseOrderState(Translator $translator, $style = 1)
    {
        return [
            -5  => $translator->translate('已退货'),
            -1  => $translator->translate('退货'),
            0   => $style == 1 ? $translator->translate('未审核') : '<strong>'.$translator->translate('未审核').'</strong>',
            1   => $style == 1 ? $translator->translate('已审核') : '<strong style="color: green;">'.$translator->translate('已审核').'</strong>',
            2   => $style == 1 ? $translator->translate('待入库') : '<strong>'.$translator->translate('待入库').'</strong>',
            3   => $style == 1 ? $translator->translate('已入库') : '<strong style="color: green;">'.$translator->translate('已入库').'</strong>'
        ];
    }

    /**
     * 采购退货单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function purchaseOrderReturnState(Translator $translator, $style = 1)
    {
        return [
            -1  => $translator->translate('退货中'),
            -5  => $style == 1 ? $translator->translate('已退货') : '<strong class="text-green">'.$translator->translate('已退货').'</strong>'
        ];
    }

    /**
     * 付款方式
     * @param Translator $translator
     * @param string $topName
     * @return array
     */
    public static function payment(Translator $translator, $topName = '')
    {
        return [
            ''          => empty($topName) ? $translator->translate('付款方式') : $topName,
            'payable'   => $translator->translate('应付账款'),
            'cashPay'   => $translator->translate('现金付款'),
            'advancePay'=> $translator->translate('预付款')
        ];
    }

    /**
     * 销售订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function salesOrderState(Translator $translator, $style = 1)
    {
        return [
            -5  => $translator->translate('已退货'),
            -1  => $translator->translate('退货'),
            0   => $style == 1 ? $translator->translate('待确认') : '<strong>'.$translator->translate('待确认').'</strong>',
            1   => $style == 1 ? $translator->translate('已确认') : '<strong class="text-blue">'.$translator->translate('已确认').'</strong>',
            6   => $style == 1 ? $translator->translate('发货出库') : '<strong>'.$translator->translate('发货出库').'</strong>',
            12  => $style == 1 ? $translator->translate('确认收货') : '<strong class="text-green">'.$translator->translate('确认收货').'</strong>',
        ];
    }

    /**
     * 退货订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function salesOrderReturnState(Translator $translator, $style = 1)
    {
        return [
            -1  => $translator->translate('退货中'),
            -5  => $style == 1 ? $translator->translate('已退货') : '<strong class="text-green">'.$translator->translate('已退货').'</strong>'
        ];
    }

    /**
     * 收款方式
     * @param Translator $translator
     * @param string $topName
     * @return array
     */
    public static function receivable(Translator $translator, $topName = '')
    {
        return [
            '' => (empty($topName) ? $translator->translate('收款方式') : $topName),
            'receivable'=> $translator->translate('应收账款'),
            'cashPay'   => $translator->translate('现金收款'),
            'prePay'    => $translator->translate('预存款')
        ];
    }

    /**
     * 入库状态
     * @param Translator $translator
     * @return array
     */
    public static function warehouseOrderState(Translator $translator)
    {
        return [
            2 => $translator->translate('验货完成等待入库'),
            3 => $translator->translate('验货完成直接入库')
        ];
    }

    /**
     * 是否有退货
     * @param Translator $translator
     * @return array
     */
    public static function existReturn(Translator $translator)
    {
        return [
            0 => $translator->translate('无'),
            1 => $translator->translate('有')
        ];
    }

    /**
     * 第三方商城订单状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function shopOrderState(Translator $translator, $style = 1)
    {
        return [
            0 => $translator->translate('已取消'),
            10 => $translator->translate('待付款'),
            15 => $translator->translate('付款中'),
            20 => $translator->translate('已付款'),
            30 => $translator->translate('待发货'),
            40 => $translator->translate('已发货'),
            60 => $translator->translate('订单完成')
        ];
    }

    /**
     * 库存盘点状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function StockCheckState(Translator $translator, $style = 1)
    {
        return [
            1  => $translator->translate('已盘点'),
            2  => $style == 1 ? $translator->translate('未盘点') : '<strong class="text-danger">'.$translator->translate('未盘点').'</strong>'
        ];
    }

    /**
     * 商城订单配货状态
     * @param Translator $translator
     * @param int $style
     * @return array
     */
    public static function distributionState(Translator $translator, $style = 1)
    {
        return [
            -1  => $translator->translate('缺货'),
            3   => $style == 1 ? $translator->translate('未匹配') : '<strong class="text-danger">'.$translator->translate('未匹配').'</strong>',
            4   => $translator->translate('已匹配'),
            6   => $translator->translate('已发货'),
            12  => $translator->translate('已确认收货')
        ];
    }
}