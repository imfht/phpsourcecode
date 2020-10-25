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

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Finance\Controller\Factory\PayableControllerFactory;
use Finance\Controller\Factory\ReceivablesControllerFactory;
use Finance\Controller\PayableController;
use Finance\Controller\ReceivablesController;
use Finance\Event\Factory\FinanceListenerFactory;
use Finance\Event\FinanceListener;
use Finance\Plugin\Factory\FinanceCommonPluginFactory;
use Finance\Plugin\FinanceCommonPlugin;
use Finance\Service\Factory\PayableLogManagerFactory;
use Finance\Service\Factory\PayableManagerFactory;
use Finance\Service\Factory\ReceivableLogManagerFactory;
use Finance\Service\Factory\ReceivableManagerFactory;
use Finance\Service\PayableLogManager;
use Finance\Service\PayableManager;
use Finance\Service\ReceivableLogManager;
use Finance\Service\ReceivableManager;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            //付款
            'finance-payable' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/finance-payable[/:action[/:id]]',
                    'defaults' => [
                        'controller' => PayableController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            //收款
            'accounts-receivable' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/accounts-receivable[/:action[/:id]]',
                    'defaults' => [
                        'controller' => ReceivablesController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
        ]
    ],

    'controllers' => [
        'factories' => [
            PayableController::class    => PayableControllerFactory::class,
            ReceivablesController::class=> ReceivablesControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            PayableManager::class       => PayableManagerFactory::class,
            PayableLogManager::class    => PayableLogManagerFactory::class,
            ReceivableManager::class    => ReceivableManagerFactory::class,
            ReceivableLogManager::class => ReceivableLogManagerFactory::class,

            FinanceListener::class      => FinanceListenerFactory::class
        ],
    ],

    'listeners' => [
        FinanceListener::class
    ],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            FinanceCommonPlugin::class => FinanceCommonPluginFactory::class
        ],
        'aliases' => [
            'financeCommon' => FinanceCommonPlugin::class
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