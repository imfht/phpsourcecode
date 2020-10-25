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
use Store\Controller\Factory\BrandControllerFactory;
use Store\Controller\Factory\GoodsCategoryControllerFactory;
use Store\Controller\Factory\GoodsControllerFactory;
use Store\Controller\Factory\PositionControllerFactory;
use Store\Controller\Factory\UnitControllerFactory;
use Store\Controller\Factory\WarehouseControllerFactory;
use Store\Controller\GoodsCategoryController;
use Store\Controller\GoodsController;
use Store\Controller\PositionController;
use Store\Controller\UnitController;
use Store\Controller\WarehouseController;
use Store\Event\Factory\ListenerFactory;
use Store\Event\Listener;
use Store\Plugin\Factory\StoreCommonPluginFactory;
use Store\Plugin\StoreCommonPlugin;
use Store\Service\BrandManager;
use Store\Service\Factory\BrandManagerFactory;
use Store\Service\Factory\GoodsCategoryManagerFactory;
use Store\Service\Factory\GoodsManagerFactory;
use Store\Service\Factory\PositionManagerFactory;
use Store\Service\Factory\UnitManagerFactory;
use Store\Service\Factory\WarehouseManagerFactory;
use Store\Service\GoodsCategoryManager;
use Store\Service\GoodsManager;
use Store\Service\PositionManager;
use Store\Service\UnitManager;
use Store\Service\WarehouseManager;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            //商品
            'goods' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/store-goods[/:action[/:id]]',
                    'defaults' => [
                        'controller' => GoodsController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            //商品分类
            'goods-category' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/store-goods-category[/:action[/:id]]',
                    'defaults' => [
                        'controller' => GoodsCategoryController::class,
                        'action'    => 'index'
                    ]
                ]
            ],


            //仓库
            'warehouse' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/store-warehouse[/:action[/:id]]',
                    'defaults' => [
                        'controller' => WarehouseController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            //库位
            'position' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/store-warehouse-position[/:action[/:id]]',
                    'defaults' => [
                        'controller' => PositionController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            //计量单位
            'unit' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/store-unit[/:action[/:id]]',
                    'defaults' => [
                        'controller' => UnitController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            //商品品牌
            'brand' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/store-brand[/:action[/:id]]',
                    'defaults' => [
                        'controller' => BrandController::class,
                        'action'    => 'index'
                    ]
                ]
            ]
        ]
    ],

    'controllers' => [
        'factories' => [
            GoodsController::class      => GoodsControllerFactory::class,
            GoodsCategoryController::class => GoodsCategoryControllerFactory::class,
            WarehouseController::class  => WarehouseControllerFactory::class,
            PositionController::class   => PositionControllerFactory::class,
            UnitController::class       => UnitControllerFactory::class,
            BrandController::class      => BrandControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            GoodsManager::class     => GoodsManagerFactory::class,
            GoodsCategoryManager::class => GoodsCategoryManagerFactory::class,
            WarehouseManager::class => WarehouseManagerFactory::class,
            PositionManager::class  => PositionManagerFactory::class,
            UnitManager::class      => UnitManagerFactory::class,
            BrandManager::class     => BrandManagerFactory::class,

            Listener::class         => ListenerFactory::class
        ],
    ],

    'listeners' => [
        Listener::class
    ],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            StoreCommonPlugin::class => StoreCommonPluginFactory::class
        ],
        'aliases' => [
            'storeCommon' => StoreCommonPlugin::class
        ]
    ],

    'view_helpers' => [
        'factories' => [],
        'aliases' => [],
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
