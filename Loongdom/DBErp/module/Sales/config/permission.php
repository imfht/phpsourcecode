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

namespace Sales;

use Sales\Controller\SalesOrderController;
use Sales\Controller\SalesOrderReturnController;
use Sales\Controller\SalesSendOrderController;

return [
    'Sales' => [
        'name' => '销售',
        'controllers' => [
            SalesOrderController::class => [
                'name' => '销售订单',
                'action' => ['index', 'add', 'edit', 'view', 'delete', 'delSalesOrderGoods', 'confirmSalesOrder', 'cancelSalesOrder', 'sendOrder'],
                'actionNames' => [
                    'index'             => '订单列表',
                    'add'               => '添加订单',
                    'edit'              => '编辑订单',
                    'view'              => '查看订单详情',
                    'delete'            => '删除订单',
                    'delSalesOrderGoods'=> '删除订单内商品',
                    'confirmSalesOrder' => '确认订单',
                    'cancelSalesOrder'  => '取消确认',
                    'sendOrder'         => '订单发货'
                ]
            ],
            SalesSendOrderController::class => [
                'name' => '销售发货',
                'action' => ['index', 'view', 'finishSalesOrder'],
                'actionNames' => [
                    'index'             => '发货列表',
                    'view'              => '查看详情',
                    'finishSalesOrder'  => '确认收货'
                ]
            ],
            SalesOrderReturnController::class => [
                'name' => '销售退货',
                'action' => ['index', 'add', 'view', 'finish', 'cancel'],
                'actionNames' => [
                    'index'     => '退货列表',
                    'add'       => '添加退货',
                    'view'      => '查看详情',
                    'finish'    => '退货完成',
                    'cancel'    => '取消退货'
                ]
            ]
        ]
    ]
];