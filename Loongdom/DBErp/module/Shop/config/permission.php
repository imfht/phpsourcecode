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

namespace Shop;

use Shop\Controller\IndexController;
use Shop\Controller\OrderGoodsController;

return [
    'Shop' => [
        'name' => '商城',
        'controllers' => [
            IndexController::class => [
                'name' => '商城订单',
                'action' => ['index', 'view', 'delete'],
                'actionNames' => [
                    'index'     => '订单列表',
                    'view'      => '查看详情',
                    'delete'    => '删除订单'
                ]
            ],
            OrderGoodsController::class => [
                'name' => '订单商品',
                'action' => ['index', 'distributionGoods', 'finishDistribution'],
                'actionNames' => [
                    'index'             => '订单商品列表',
                    'distributionGoods' => '匹配商品',
                    'finishDistribution'=> '商品补货'
                ]
            ]
        ]
    ]
];