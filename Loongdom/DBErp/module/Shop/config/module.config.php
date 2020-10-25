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

use Shop\Controller\Factory\IndexControllerFactory;
use Shop\Controller\Factory\OrderGoodsControllerFactory;
use Shop\Controller\IndexController;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Shop\Controller\OrderGoodsController;
use Shop\Event\Factory\ListenerFactory;
use Shop\Event\Listener;
use Shop\Service\Factory\ShopOrderDeliveryAddressManagerFactory;
use Shop\Service\Factory\ShopOrderGoodsManagerFactory;
use Shop\Service\Factory\ShopOrderManagerFactory;
use Shop\Service\ShopOrderDeliveryAddressManager;
use Shop\Service\ShopOrderGoodsManager;
use Shop\Service\ShopOrderManager;
use Shop\View\Factory\ShopHelperFactory;
use Shop\View\ShopHelper;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'app-shop' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/shop/order[/:action[/:id]]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            'app-shop-order-goods' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/shop/goods[/:action[/:id]]',
                    'defaults' => [
                        'controller' => OrderGoodsController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
        ]

    ],

    'controllers' => [
        'factories' => [
            IndexController::class      => IndexControllerFactory::class,
            OrderGoodsController::class => OrderGoodsControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            ShopOrderManager::class         => ShopOrderManagerFactory::class,
            ShopOrderGoodsManager::class    => ShopOrderGoodsManagerFactory::class,
            ShopOrderDeliveryAddressManager::class => ShopOrderDeliveryAddressManagerFactory::class,

            Listener::class                 => ListenerFactory::class
        ]
    ],

    'listeners' => [
        Listener::class
    ],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [

        ],
        'aliases'   => [

        ]
    ],

    'view_helpers' => [
        'factories' => [
            ShopHelper::class => ShopHelperFactory::class
        ],
        'aliases' => [
            'shop' => ShopHelper::class
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