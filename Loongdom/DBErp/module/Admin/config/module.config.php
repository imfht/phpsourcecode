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

namespace Admin;

use Admin\Controller\AdminController;
use Admin\Controller\AdminGroupController;
use Admin\Controller\AppController;
use Admin\Controller\Factory\AdminControllerFactory;
use Admin\Controller\Factory\AdminGroupControllerFactory;
use Admin\Controller\Factory\AppControllerFactory;
use Admin\Controller\Factory\HomeControllerFactory;
use Admin\Controller\Factory\IndexControllerFactory;
use Admin\Controller\Factory\OperLogControllerFactory;
use Admin\Controller\Factory\RegionControllerFactory;
use Admin\Controller\Factory\SystemControllerFactory;
use Admin\Controller\HomeController;
use Admin\Controller\IndexController;
use Admin\Controller\OperLogController;
use Admin\Controller\RegionController;
use Admin\Controller\SystemController;
use Admin\Event\AdminListener;
use Admin\Event\Factory\AdminListenerFactory;
use Admin\Plugin\AdminCommonPlugin;
use Admin\Plugin\AdminSessionPlugin;
use Admin\Plugin\Factory\AdminCommonPluginFactory;
use Admin\Plugin\Factory\AdminSessionPluginFactory;
use Admin\Report\Factory\HomeReportFactory;
use Admin\Report\HomeReport;
use Admin\Service\AdminUserGroupManager;
use Admin\Service\AdminUserManager;
use Admin\Service\AppManager;
use Admin\Service\AuthAdapter;
use Admin\Service\AuthManager;
use Admin\Service\Factory\AdminUserGroupManagerFactory;
use Admin\Service\Factory\AdminUserManagerFactory;
use Admin\Service\Factory\AppManagerFactory;
use Admin\Service\Factory\AuthAdapterFactory;
use Admin\Service\Factory\AuthenticationServiceFactory;
use Admin\Service\Factory\AuthManagerFactory;
use Admin\Service\Factory\OperlogManagerFactory;
use Admin\Service\Factory\RegionManagerFactory;
use Admin\Service\Factory\SystemManagerFactory;
use Admin\Service\OperlogManager;
use Admin\Service\RegionManager;
use Admin\Service\SystemManager;
use Admin\View\Helper\AdminHelper;
use Admin\View\Helper\CurrentAdmin;
use Admin\View\Helper\CurrentRoute;
use Admin\View\Helper\ErpCurrencyFormatHelper;
use Admin\View\Helper\Factory\AdminHelperFactory;
use Admin\View\Helper\Factory\CurrentAdminFactory;
use Admin\View\Helper\Factory\CurrentRouteFactory;
use Admin\View\Helper\Factory\ErpCurrencyFormatHelperFactory;
use Admin\View\Helper\Factory\HelpUrlFactory;
use Admin\View\Helper\HelpUrl;
use Zend\Authentication\AuthenticationService;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],

            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/home',
                    'defaults' => [
                        'controller' => HomeController::class,
                        'action'     => 'index',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
                            ],
                            'defaults' => [
                                'action' => 'index'
                            ]
                        ]
                    ]
                ]
            ],

            'admin' => [
                'type'      => Segment::class,
                'options'   => [
                    'route' => '/admin[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => AdminController::class,
                        'action'     => 'index',
                    ]
                ]
            ],

            'admin-group' => [
                'type'  => Segment::class,
                'options'   => [
                    'route' => '/admin-group[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => AdminGroupController::class,
                        'action'    => 'index'
                    ],
                ]
            ],

            'admin-system' => [
                'type'  => Segment::class,
                'options'   => [
                    'route' => '/admin-system[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => SystemController::class,
                        'action'    => 'index'
                    ],
                ]
            ],

            'region' => [
                'type'  => Segment::class,
                'options'   => [
                    'route' => '/admin-region[/:action[/:id[/:top-id]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => RegionController::class,
                        'action'    => 'index'
                    ],
                ]
            ],

            'app' => [
                'type'  => Segment::class,
                'options'   => [
                    'route' => '/admin-app[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => AppController::class,
                        'action'    => 'index'
                    ],
                ]
            ],

            'oper-log' => [
                'type'  => Segment::class,
                'options'   => [
                    'route' => '/admin-oper[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => OperLogController::class,
                        'action'    => 'index'
                    ],
                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            IndexController::class  => IndexControllerFactory::class,
            HomeController::class   => HomeControllerFactory::class,
            AdminController::class  => AdminControllerFactory::class,
            AdminGroupController::class => AdminGroupControllerFactory::class,
            RegionController::class => RegionControllerFactory::class,
            SystemController::class => SystemControllerFactory::class,
            AppController::class    => AppControllerFactory::class,
            OperLogController::class=> OperLogControllerFactory::class
        ],
    ],

    'service_manager' => [
        'abstract_factories' => [

        ],
        'factories' => [
            AdminUserManager::class => AdminUserManagerFactory::class,
            AuthenticationService::class => AuthenticationServiceFactory::class,
            AuthManager::class      => AuthManagerFactory::class,
            AuthAdapter::class      => AuthAdapterFactory::class,
            AdminUserGroupManager::class => AdminUserGroupManagerFactory::class,
            RegionManager::class    => RegionManagerFactory::class,
            AppManager::class       => AppManagerFactory::class,
            OperlogManager::class   => OperlogManagerFactory::class,
            SystemManager::class    => SystemManagerFactory::class,

            HomeReport::class       => HomeReportFactory::class,

            AdminListener::class    => AdminListenerFactory::class
        ],
        'aliases' => [
            
        ]
    ],

    'listeners' => [
        AdminListener::class
    ],

    'permission_filter' => include __DIR__ . '/permission.php',

    'controller_plugins' => [
        'factories' => [
            AdminSessionPlugin::class => AdminSessionPluginFactory::class,
            AdminCommonPlugin::class  => AdminCommonPluginFactory::class
        ],
        'aliases' => [
            'adminSession' => AdminSessionPlugin::class,
            'adminCommon'  => AdminCommonPlugin::class
        ]
    ],

    'session_containers' => [
        'I18nSessionContainer'
    ],

    'view_helpers' => [
        'factories' => [
            CurrentRoute::class => CurrentRouteFactory::class,
            CurrentAdmin::class => CurrentAdminFactory::class,
            HelpUrl::class      => HelpUrlFactory::class,
            ErpCurrencyFormatHelper::class => ErpCurrencyFormatHelperFactory::class,
            AdminHelper::class  => AdminHelperFactory::class
        ],
        'aliases' => [
            'currentRoute'  => CurrentRoute::class,
            'currentAdmin'  => CurrentAdmin::class,
            'HelpUrl'       => HelpUrl::class,
            'erpCurrencyFormat' => ErpCurrencyFormatHelper::class,
            'adminHelper'   => AdminHelper::class
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
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/left'             => __DIR__ . '/../view/layout/left-menu.phtml',
            'layout/header'           => __DIR__ . '/../view/layout/header.phtml',
            'layout/footer'           => __DIR__ . '/../view/layout/footer.phtml',
            'layout/messages'       => __DIR__ . '/../view/partial/messages.phtml',
            'layout/ajaxPage'           => __DIR__ . '/../view/partial/ajaxPaginator.phtml',
            'layout/page'           => __DIR__ . '/../view/partial/paginator.phtml',
            'layout/breadcrumb'     => __DIR__ . '/../view/partial/breadcrumb.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
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
    ],
];
