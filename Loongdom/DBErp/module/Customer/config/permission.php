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

namespace Customer;

use Customer\Controller\CustomerCategoryController;
use Customer\Controller\CustomerController;
use Customer\Controller\SupplierCategoryController;
use Customer\Controller\SupplierController;

return [
    'Customer' => [
        'name' => '客户',
        'controllers' => [
            CustomerCategoryController::class => [
                'name' => '客户组',
                'action' => ['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index' => '客户组列表',
                    'add'   => '添加客户组',
                    'edit'  => '编辑客户组',
                    'delete'=> '删除客户组'
                ]
            ],
            CustomerController::class => [
                'name' => '客户',
                'action' => ['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index' => '客户列表',
                    'add'   => '添加客户',
                    'edit'  => '编辑客户',
                    'delete'=> '删除客户'
                ]
            ],
            SupplierCategoryController::class => [
                'name' => '供应商组',
                'action' => ['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index' => '供应商组列表',
                    'add'   => '添加供应商组',
                    'edit'  => '编辑供应商组',
                    'delete'=> '删除供应商组'
                ]
            ],
            SupplierController::class => [
                'name' => '供应商',
                'action' => ['index', 'add', 'edit', 'delete'],
                'actionNames' => [
                    'index' => '供应商列表',
                    'add'   => '添加供应商',
                    'edit'  => '编辑供应商',
                    'delete'=> '删除供应商'
                ]
            ]
        ]
    ]
];