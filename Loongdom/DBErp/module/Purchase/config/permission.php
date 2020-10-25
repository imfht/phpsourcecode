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

namespace Purchase;

use Purchase\Controller\OrderController;
use Purchase\Controller\OrderReturnController;
use Purchase\Controller\WarehouseOrderController;

return [
    'Purchase' => [
        'name' => '采购',
        'controllers' => [
            OrderController::class => [
                'name' => '采购订单',
                'action' => ['index', 'add', 'edit', 'delete', 'view', 'authPassOrder', 'cancelOrder', 'delOrderGoods'],
                'actionNames' => [
                    'index'         => '订单列表',
                    'add'           => '添加订单',
                    'edit'          => '编辑订单',
                    'delete'        => '删除订单',
                    'view'          => '查看订单',
                    'authPassOrder' => '审核订单',
                    'cancelOrder'   => '取消订单',
                    'delOrderGoods' => '删除订单内商品'
                ]
            ],
            WarehouseOrderController::class => [
                'name' => '采购入库',
                'action' => ['index', 'add', 'view', 'delete', 'insertWarehouse'],
                'actionNames' => [
                    'index'         => '入库列表',
                    'add'           => '添加入库',
                    'view'          => '查看入库信息',
                    'delete'        => '删除入库',
                    'insertWarehouse'=> '待入库单入库'
                ]
            ],
            OrderReturnController::class => [
                'name' => '采购退货',
                'action' => ['index', 'view', 'cancel', 'returnFinish', 'returnOrder'],
                'actionNames' => [
                    'index'         => '退货列表',
                    'view'          => '查看详情',
                    'returnFinish'  => '退货完成',
                    'cancel'        => '取消退货',
                    'returnOrder'   => '添加退货'
                ]
            ]
        ]
    ]
];