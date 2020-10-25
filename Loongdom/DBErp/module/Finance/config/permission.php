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

namespace Finance;

use Finance\Controller\PayableController;
use Finance\Controller\ReceivablesController;

return [
    'Finance'   => [
        'name'  => '资金',
        'controllers' => [
            PayableController::class => [
                'name' => '应付账款',
                'action' => ['index', 'addPayable', 'show', 'payableLog'],
                'actionNames' => [
                    'index'     => '账款列表',
                    'addPayable'=> '添加付款',
                    'show'      => '详情查看',
                    'payableLog'=> '付款记录查看'
                ]
            ],
            ReceivablesController::class => [
                'name' => '应收账款',
                'action' => ['index', 'addReceivable', 'show', 'receivableLog'],
                'actionNames' => [
                    'index'         => '账款列表',
                    'addReceivable' => '添加收款',
                    'show'          => '性情查看',
                    'receivableLog' => '收款记录查看'
                ]
            ]
        ]
    ]
];