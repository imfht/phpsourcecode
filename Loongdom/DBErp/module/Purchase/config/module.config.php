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

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Purchase\Controller\Factory\OrderControllerFactory;
use Purchase\Controller\Factory\OrderReturnControllerFactory;
use Purchase\Controller\Factory\WarehouseOrderControllerFactory;
use Purchase\Controller\OrderController;
use Purchase\Controller\OrderReturnController;
use Purchase\Controller\WarehouseOrderController;
use Purchase\Plugin\Factory\PurchaseCommonPluginFactory;
use Purchase\Plugin\PurchaseCommonPlugin;
use Purchase\Service\Factory\OrderGoodsManagerFactory;
use Purchase\Service\Factory\OrderGoodsReturnManagerFactory;
use Purchase\Service\Factory\OrderManagerFactory;
use Purchase\Service\Factory\OrderReturnManagerFactory;
use Purchase\Service\Factory\PurchaseGoodsPriceLogManagerFactory;
use Purchase\Service\Factory\PurchaseOperLogManagerFactory;
use Purchase\Service\Factory\WarehouseOrderGoodsManagerFactory;
use Purchase\Service\Factory\WarehouseOrderManagerFactory;
use Purchase\Service\OrderGoodsManager;
use Purchase\Service\OrderGoodsReturnManager;
use Purchase\Service\OrderManager;
use Purchase\Service\OrderReturnManager;
use Purchase\Service\PurchaseGoodsPriceLogManager;
use Purchase\Service\PurchaseOperLogManager;
use Purchase\Service\WarehouseOrderGoodsManager;
use Purchase\Service\WarehouseOrderManager;
use Purchase\View\Helper\Factory\PurchaseHelperFactory;
use Purchase\View\Helper\PurchaseHelper;
use Store\Service\Factory\WarehouseGoodsManagerFactory;
use Store\Service\WarehouseGoodsManager;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'p-order' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/purchase-order[/:action[/:id]]',
                    'defaults' => [
                        'controller' => OrderController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            'order-return' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/purchase-return[/:action[/:id]]',
                    'defaults' => [
                        'controller' => OrderReturnController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            'warehouse-order' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/warehouse-order[/:action[/:id]]',
                    'defaults' => [
                        'controller' => WarehouseOrderController::class,
                        'action'    => 'index'
                    ]
                ]
            ]
        ]

    ],

    'controllers' => [
        'factories' => [
            OrderController::class          => OrderControllerFactory::class,
            OrderReturnController::class    => OrderReturnControllerFactory::class,
            WarehouseOrderController::class => WarehouseOrderControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            OrderManager::class                 => OrderManagerFactory::class,
            OrderGoodsManager::class            => OrderGoodsManagerFactory::class,
            WarehouseOrderManager::class        => WarehouseOrderManagerFactory::class,
            WarehouseOrderGoodsManager::class   => WarehouseOrderGoodsManagerFactory::class,
            OrderReturnManager::class           => OrderReturnManagerFactory::class,
            OrderGoodsReturnManager::class      => OrderGoodsReturnManagerFactory::class,
            WarehouseGoodsManager::class        => WarehouseGoodsManagerFactory::class,
            PurchaseGoodsPriceLogManager::class => PurchaseGoodsPriceLogManagerFactory::class,
            PurchaseOperLogManager::class       => PurchaseOperLogManagerFactory::class,

        ]
    ],

    'listeners' => [],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            PurchaseCommonPlugin::class => PurchaseCommonPluginFactory::class
        ],
        'aliases'   => [
            'purchaseCommon' => PurchaseCommonPlugin::class
        ]
    ],

    'view_helpers' => [
        'factories' => [
            PurchaseHelper::class => PurchaseHelperFactory::class
        ],
        'aliases' => [
            'PurchaseHelper'  => PurchaseHelper::class,
        ],
    ],

    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../data/language',
                'pattern' => '%s.mo'
            ]
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],

    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];