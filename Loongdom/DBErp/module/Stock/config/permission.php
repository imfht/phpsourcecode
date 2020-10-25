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

namespace Stock;

use Stock\Controller\IndexController;
use Stock\Controller\StockCheckController;

return [
    'Stock' => [
        'name' => '库存',
        'controllers' => [
            IndexController::class => [
                'name' => '其他入库',
                'action' => ['index', 'add', 'view'],
                'actionNames' => [
                    'index'         => '入库单列表',
                    'add'           => '添加入库单',
                    'view'          => '查看入库单'
                ]
            ],
            StockCheckController::class => [
                'name' => '库存盘点',
                'action' => ['index', 'add', 'edit', 'delete', 'confirm', 'view', 'delStockCheckGoods'],
                'actionNames' => [
                    'index'     => '盘点列表',
                    'add'       => '添加盘点单',
                    'edit'      => '编辑盘点单',
                    'delete'    => '删除盘点单',
                    'confirm'   => '确认盘点单',
                    'view'      => '查看盘点单',
                    'delStockCheckGoods' => '删除盘点单内商品'
                ]
            ]
        ]
    ]
];