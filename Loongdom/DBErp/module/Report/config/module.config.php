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

namespace Report;

use Report\Controller\Factory\IndexControllerFactory;
use Report\Controller\Factory\ReportFinanceControllerFactory;
use Report\Controller\Factory\ReportPurchaseControllerFactory;
use Report\Controller\Factory\ReportStockControllerFactory;
use Report\Controller\IndexController;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Report\Controller\ReportFinanceController;
use Report\Controller\ReportPurchaseController;
use Report\Controller\ReportStockController;
use Report\Report\Factory\SalesReportFactory;
use Report\Report\SalesReport;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'report' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/report[/:action]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            'report-stock' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/report-stock[/:action]',
                    'defaults' => [
                        'controller' => ReportStockController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            'report-purchase' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/report-purchase[/:action]',
                    'defaults' => [
                        'controller' => ReportPurchaseController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
            'report-finance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/report-finance[/:action]',
                    'defaults' => [
                        'controller' => ReportFinanceController::class,
                        'action'    => 'index'
                    ]
                ]
            ],
        ]

    ],

    'controllers' => [
        'factories' => [
            IndexController::class => IndexControllerFactory::class,
            ReportFinanceController::class  => ReportFinanceControllerFactory::class,
            ReportPurchaseController::class => ReportPurchaseControllerFactory::class,
            ReportStockController::class    => ReportStockControllerFactory::class
        ]
    ],

    'service_manager' => [
        'factories' => [
            SalesReport::class => SalesReportFactory::class
        ]
    ],

    'listeners' => [],

    'controller_plugins' => [
        'factories' => [

        ],
        'aliases'   => [

        ]
    ],

    'view_helpers' => [
        'factories' => [

        ],
        'aliases' => [

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