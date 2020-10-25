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

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Sales\Controller\Factory\SalesOrderControllerFactory;
use Sales\Controller\Factory\SalesOrderReturnControllerFactory;
use Sales\Controller\Factory\SalesSendOrderControllerFactory;
use Sales\Controller\SalesOrderController;
use Sales\Controller\SalesOrderReturnController;
use Sales\Controller\SalesSendOrderController;
use Sales\Plugin\Factory\SalesCommonPluginFactory;
use Sales\Plugin\SalesCommonPlugin;
use Sales\Service\Factory\SalesGoodsPriceLogManagerFactory;
use Sales\Service\Factory\SalesOperLogManagerFactory;
use Sales\Service\Factory\SalesOrderGoodsManagerFactory;
use Sales\Service\Factory\SalesOrderGoodsReturnManagerFactory;
use Sales\Service\Factory\SalesOrderManagerFactory;
use Sales\Service\Factory\SalesOrderReturnManagerFactory;
use Sales\Service\Factory\SalesSendOrderManagerFactory;
use Sales\Service\Factory\SalesSendWarehouseGoodsManagerFactory;
use Sales\Service\SalesGoodsPriceLogManager;
use Sales\Service\SalesOperLogManager;
use Sales\Service\SalesOrderGoodsManager;
use Sales\Service\SalesOrderGoodsReturnManager;
use Sales\Service\SalesOrderManager;
use Sales\Service\SalesOrderReturnManager;
use Sales\Service\SalesSendOrderManager;
use Sales\Service\SalesSendWarehouseGoodsManager;
use Sales\View\Factory\SalesHelperFactory;
use Sales\View\SalesHelper;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'sales-order' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/sales-order[/:action[/:id]]',
                    'defaults' => [
                        'controller' => SalesOrderController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            'sales-send-order' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/sales-send-order[/:action[/:id]]',
                    'defaults' => [
                        'controller' => SalesSendOrderController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            'sales-order-return' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/sales-order-return[/:action[/:id]]',
                    'defaults' => [
                        'controller' => SalesOrderReturnController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
        ]

    ],

    'controllers' => [
        'factories' => [
            SalesOrderController::class         => SalesOrderControllerFactory::class,
            SalesSendOrderController::class     => SalesSendOrderControllerFactory::class,
            SalesOrderReturnController::class   => SalesOrderReturnControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            SalesOrderManager::class                => SalesOrderManagerFactory::class,
            SalesOrderGoodsManager::class           => SalesOrderGoodsManagerFactory::class,
            SalesSendOrderManager::class            => SalesSendOrderManagerFactory::class,
            SalesSendWarehouseGoodsManager::class   => SalesSendWarehouseGoodsManagerFactory::class,
            SalesGoodsPriceLogManager::class        => SalesGoodsPriceLogManagerFactory::class,
            SalesOperLogManager::class              => SalesOperLogManagerFactory::class,
            SalesOrderReturnManager::class          => SalesOrderReturnManagerFactory::class,
            SalesOrderGoodsReturnManager::class     => SalesOrderGoodsReturnManagerFactory::class
        ]
    ],

    'listeners' => [],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            SalesCommonPlugin::class => SalesCommonPluginFactory::class
        ],
        'aliases'   => [
            'salesCommon' => SalesCommonPlugin::class
        ]
    ],

    'view_helpers' => [
        'factories' => [
            SalesHelper::class  => SalesHelperFactory::class
        ],
        'aliases' => [
            'salesHelper'   => SalesHelper::class
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