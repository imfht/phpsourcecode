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

namespace Store;

use Store\Controller\BrandController;
use Store\Controller\GoodsCategoryController;
use Store\Controller\GoodsController;
use Store\Controller\PositionController;
use Store\Controller\UnitController;
use Store\Controller\WarehouseController;

return [
    'Store' => [
        'name' => '商品&仓库',
        'controllers' => [
            GoodsCategoryController::class => [
                'name' => '商品分类',
                'action' => ['index', 'add', 'edit', 'delete', 'updateAll'],
                'actionNames' => [
                    'index'     => '分类列表',
                    'add'       => '添加分类',
                    'edit'      => '编辑分类',
                    'delete'    => '删除分类',
                    'updateAll' => '批量操作'
                ]
            ],
            GoodsController::class => [
                'name' => '商品',
                'action' =>['index', 'add', 'edit', 'delete', 'priceTrend', 'goodsWarehouse'],
                'actionNames' => [
                    'index'         => '商品列表',
                    'add'           => '添加商品',
                    'edit'          => '编辑商品',
                    'delete'        => '删除商品',
                    'priceTrend'    => '价格趋势',
                    'goodsWarehouse'=> '入库分布'
                ]
            ],
            BrandController::class => [
                'name' => '商品品牌',
                'action' =>['index', 'add', 'edit', 'delete', 'updateAll'],
                'actionNames' => [
                    'index'     => '品牌列表',
                    'add'       => '添加品牌',
                    'edit'      => '编辑品牌',
                    'delete'    => '删除品牌',
                    'updateAll' => '批量操作'
                ]
            ],
            UnitController::class => [
                'name' => '计量单位',
                'action' =>['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index'     => '单位列表',
                    'add'       => '添加单位',
                    'edit'      => '编辑单位',
                    'delete'    => '删除单位'
                ]
            ],
            WarehouseController::class => [
                'name' => '仓库',
                'action' =>['index', 'add', 'edit', 'delete', 'updateAll'],
                'actionNames' => [
                    'index'     => '仓库列表',
                    'add'       => '添加仓库',
                    'edit'      => '编辑仓库',
                    'delete'    => '删除仓库',
                    'updateAll' => '批量操作'
                ]
            ],
            PositionController::class => [
                'name' => '仓位',
                'action' =>['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index'     => '仓位列表',
                    'add'       => '添加仓位',
                    'edit'      => '编辑仓位',
                    'delete'    => '删除仓位'
                ]
            ]
        ]
    ]
];