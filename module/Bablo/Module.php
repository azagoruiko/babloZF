<?php
namespace Bablo;

use bablo\dao\MysqlUserDAO;
use PDO;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->initSession(array(
            'remember_me_seconds' => 3600,
            'use_cookies' => true,
            'cookie_httponly' => true,
        ));
    }

    public function initSession($config)
    {
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
    }
    

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'bablo' => __DIR__ . '/../../common/bablo'
                ),
            ),
        );
    }
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Bablo\service\CurrencyService' =>  function($sm) {
                    return $sm->get('Bablo\dao\CurrencyDAO');
                },
                'Bablo\dao\CurrencyDAO' =>  function($sm) {
                    $conn = $sm->get('MySQLConnection');
                    $dao = new \bablo\dao\MysqlCurrencyDAO($conn);
                    return $dao;
                },
                'Bablo\dao\UserService' =>  function($sm) {
                    $dao = $sm->get('Bablo\dao\UserDAO');
                    $srv = new \bablo\service\UserServiceImpl($dao);
                    return $srv;
                },
                'Bablo\dao\UserDAO' =>  function($sm) {
                    $conn = $sm->get('MySQLConnection');
                    $dao = new MysqlUserDAO($conn);
                    return $dao;
                },
                'MySQLConnection' => function ($sm) {
                    return new PDO('mysql:host=localhost;dbname=' . 'bablo', 'bablo3', 'parol');
                },
        ));
    }
}
