<?php
namespace Rest;

use bablo\dao\MysqlExpenceDAO;
use bablo\dao\MysqlIncomeDAO;
use bablo\service\IncomeServiceImpl;
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
                    'bablo' => __DIR__ . '/../../common/bablo',
                    //'Bablo' => __DIR__ . '/../../module/Bablo',
                    //'Rest' => __DIR__ . '/../../module/Rest',
                ),
            ),
        );
    }
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Rest\service\ExpenceService' =>  function($sm) {
                    return $sm->get('Rest\dao\ExpenceDAO');
                },
                'Rest\dao\ExpenceDAO' =>  function($sm) {
                    $conn = $sm->get('MySQLConnection');
                    $dao = new MysqlExpenceDAO($conn);
                    return $dao;
                },
                'Rest\service\IncomeService' =>  function($sm) {
                    $dao = $sm->get('Rest\dao\IncomeDAO');
                    $srv = new IncomeServiceImpl($dao);
                    return $srv;
                },
                'Rest\dao\IncomeDAO' =>  function($sm) {
                    $conn = $sm->get('MySQLConnection');
                    $dao = new MysqlIncomeDAO($conn);
                    return $dao;
                },
                'MySQLConnection' => function ($sm) {
                    return new PDO('mysql:host=localhost;dbname=' . 'bablo', 'bablo3', 'parol');
                },
        ));
    }
}
