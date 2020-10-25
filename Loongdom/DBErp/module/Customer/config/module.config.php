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
use Customer\Controller\Factory\CustomerCategoryControllerFactory;
use Customer\Controller\Factory\CustomerControllerFactory;
use Customer\Controller\Factory\SupplierCategoryControllerFactory;
use Customer\Controller\Factory\SupplierControllerFactory;
use Customer\Controller\SupplierCategoryController;
use Customer\Controller\SupplierController;
use Customer\Plugin\CustomerCommonPlugin;
use Customer\Plugin\Factory\CustomerCommonPluginFactory;
use Customer\Service\CustomerCategoryManager;
use Customer\Service\CustomerManager;
use Customer\Service\Factory\CustomerCategoryManagerFactory;
use Customer\Service\Factory\CustomerManagerFactory;
use Customer\Service\Factory\SupplierCategoryManagerFactory;
use Customer\Service\Factory\SupplierManagerFactory;
use Customer\Service\SupplierCategoryManager;
use Customer\Service\SupplierManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            //客户
            'customer' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/customer[/:action[/:id]]',
                    'defaults' => [
                        'controller' => CustomerController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            //客户分类
            'customer-category' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/customer-category[/:action[/:id]]',
                    'defaults' => [
                        'controller' => CustomerCategoryController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            //供应商
            'supplier' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/supplier[/:action[/:id]]',
                    'defaults' => [
                        'controller' => SupplierController::class,
                        'action'    => 'index'
                    ]
                ]
            ],

            //供应商分类
            'supplier-category' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/supplier-category[/:action[/:id]]',
                    'defaults' => [
                        'controller' => SupplierCategoryController::class,
                        'action'    => 'index'
                    ]
                ]
            ]
        ]
    ],

    'controllers' => [
        'factories' => [
            CustomerController::class           => CustomerControllerFactory::class,
            CustomerCategoryController::class   => CustomerCategoryControllerFactory::class,
            SupplierController::class           => SupplierControllerFactory::class,
            SupplierCategoryController::class   => SupplierCategoryControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            CustomerManager::class          => CustomerManagerFactory::class,
            CustomerCategoryManager::class  => CustomerCategoryManagerFactory::class,
            SupplierManager::class          => SupplierManagerFactory::class,
            SupplierCategoryManager::class  => SupplierCategoryManagerFactory::class
        ],
    ],

    'listeners' => [],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            CustomerCommonPlugin::class => CustomerCommonPluginFactory::class
        ],
        'aliases' => [
            'customerCommon' => CustomerCommonPlugin::class
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