<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'view_helpers' => [
        'invokables' => [
            'summary' => '\Bablo\ViewHelper\SummaryHelper',
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Главная',
                'route' => 'home',
                'resource' => 'mvc:Bablo\Controller\Index',
            ],
            [
                'label' => 'Добавить бабла',
                'route' => 'bablo/default',
                'controller' => 'accounting',
                'action' => 'edit-income',
                'resource' => 'mvc:Bablo\Controller\Accounting',
            ],
            [
                'label' => 'Бабло',
                'route' => 'bablo/default',
                'controller' => 'accounting',
                'action' => 'income',
                'resource' => 'mvc:Bablo\Controller\Accounting',
            ],
            [
                'label' => 'Выход',
                'route' => 'bablo/default',
                'controller' => 'index',
                'action' => 'logout',
                'resource' => 'mvc:Bablo\Controller\Accounting',
            ],
        ],
    ],
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Bablo\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'dashboard' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/dashboard',
                    'defaults' => array(
                        'controller' => 'Bablo\Controller\Index',
                        'action'     => 'dashboard',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'bablo' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/bablo',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Bablo\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Bablo\Controller\Index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Bablo\Controller\Index' => 'Bablo\Controller\IndexController',
            'Bablo\Controller\Accounting' => 'Bablo\Controller\AccountingController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../../Bablo/view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../../Bablo/view/bablo/index/index.phtml',
            'error/404'               => __DIR__ . '/../../Bablo/view/error/404.phtml',
            'error/index'             => __DIR__ . '/../../Bablo/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'user' => array(
                    'options' => array(
                        'route' => 'user <action> <param1> [<param2>]',
                        'defaults' => array(
                            'controller' => 'Bablo\Controller\Index',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
