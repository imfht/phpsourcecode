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

use Stock\Controller\Factory\IndexControllerFactory;
use Stock\Controller\Factory\StockCheckControllerFactory;
use Stock\Controller\IndexController;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Stock\Controller\StockCheckController;
use Stock\Plugin\Factory\StockCommonPluginFactory;
use Stock\Plugin\StockCommonPlugin;
use Stock\Service\Factory\OtherWarehouseOrderGoodsManagerFactory;
use Stock\Service\Factory\OtherWarehouseOrderManagerFactory;
use Stock\Service\Factory\StockCheckGoodsManagerFactory;
use Stock\Service\Factory\StockCheckManagerFactory;
use Stock\Service\OtherWarehouseOrderGoodsManager;
use Stock\Service\OtherWarehouseOrderManager;
use Stock\Service\StockCheckGoodsManager;
use Stock\Service\StockCheckManager;
use Stock\View\Helper\Factory\StockHelperFactory;
use Stock\View\Helper\StockHelper;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'erp-stock' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/stock[/:action[/:id]]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            'stock-check' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/stock-check[/:action[/:id]]',
                    'defaults' => [
                        'controller' => StockCheckController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
        ]

    ],

    'controllers' => [
        'factories' => [
            IndexController::class => IndexControllerFactory::class,
            StockCheckController::class => StockCheckControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            OtherWarehouseOrderManager::class       => OtherWarehouseOrderManagerFactory::class,
            OtherWarehouseOrderGoodsManager::class  => OtherWarehouseOrderGoodsManagerFactory::class,
            StockCheckManager::class                => StockCheckManagerFactory::class,
            StockCheckGoodsManager::class           => StockCheckGoodsManagerFactory::class
        ]
    ],

    'listeners' => [],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            StockCommonPlugin::class => StockCommonPluginFactory::class
        ],
        'aliases'   => [
            'stockCommon' => StockCommonPlugin::class
        ]
    ],

    'view_helpers' => [
        'factories' => [
            StockHelper::class  => StockHelperFactory::class
        ],
        'aliases' => [
            'stockHelper' => StockHelper::class
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