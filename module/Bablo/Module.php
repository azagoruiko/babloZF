<?php
namespace Bablo;

use bablo\dao\MysqlCurrencyDAO;
use bablo\model\User;
use Bablo\Service\AuthUserService;
use PDO;
use Zend\Authentication\AuthenticationService;
use Zend\Console\Request;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Helper\Navigation\AbstractHelper;

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
        
        $eventManager->attach('route', function(MvcEvent $e){
            $sm = $e->getApplication()->getServiceManager();
            $acl = $sm->get('ACL');
            AbstractHelper::setDefaultAcl($acl);
        });
        
        $eventManager->attach('dispatch', function (MvcEvent $e) {
            $sm = $e->getApplication()->getServiceManager();
            $auth = $sm->get('AuthService');
            $role = 'guest';
            if ($auth->hasIdentity()) {
                $role = 'user';   
            }
            AbstractHelper::setDefaultRole($role);
            /**
             * @var Acl
             */
            $acl = $sm->get('ACL');
            $resCtrl = 'mvc:' . $e->getRouteMatch()->getParam('controller');
            $resAct = $resCtrl . ':' . $e->getRouteMatch()->getParam('action');
            //if (php_sapi_name() !== 'cli' && !$acl->isAllowed($role, $resCtrl)) {
            if (!($e->getRequest() instanceof Request) && !$acl->isAllowed($role, $resCtrl)) {
                if (!($acl->hasResource($resAct) && $acl->isAllowed($role, $resAct))) {
                    $url = $e->getRouter()->assemble([], ['name' => 'home']);
                    $resp = $e->getResponse();
                    $resp->getHeaders()->addHeaderLine("Location", $url);
                    $resp->setStatusCode(302);
                    $resp->sendHeaders();
                    exit;
                }
            }
        });
        /*$this->initSession(array(
            'remember_me_seconds' => 3600,
            'use_cookies' => true,
            'cookie_httponly' => true,
        ));*/
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
                'Bablo\service\AuthSession' => function($sm) {
                    $srv = new Service\AuthStorage();
                    return $srv;
                },
                'Bablo\dao\CurrencyDAO' =>  function($sm) {
                    $conn = $sm->get('MySQLConnection');
                    $dao = new MysqlCurrencyDAO($conn);
                    return $dao;
                },
                        
                'AuthService' => function($sm) {
                    return new AuthenticationService(
                           $sm->get('Bablo\service\AuthSession'), 
                           $sm->get('Bablo\dao\UserService')); 
                }, 
                'Bablo\dao\UserService' =>  function($sm) {
                    $dao = $sm->get('Bablo\dao\UserDAO');
                    //$srv = new AuthUserService($dao);
                    $srv = $dao;
                    return $srv;
                },
                'Bablo\dao\UserDAO' =>  function($sm) {
                    //$conn = $sm->get('MySQLConnection');
                    //$dao = new MysqlUserDAO($conn);
                    $gw = $sm->get('Bablo\dao\UserTable');
                    $dao = new Service\ZendMysqlUserService();
                    $dao->setGw($gw);
                    return $dao;
                },
                
                'Bablo\dao\UserTable' =>  function($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultPrototype = new ResultSet();
                    $resultPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $adapter, null, $resultPrototype);
                },
                
                'MySQLConnection' => function ($sm) {
                    return new PDO('mysql:host=localhost;dbname=' . 'bablo', 'bablo3', 'parol');
                },
                        
                
                'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
                
                'ACL' => function ($sm) {
                    $acl = new Acl();
                    $acl->addRole(new GenericRole('guest'));
                    $acl->addRole(new GenericRole('user', ['guest']));
                    $acl->addRole(new GenericRole('admin'));

                    $acl->addResource(new GenericResource('mvc:Bablo\Controller\Accounting'));
                    $acl->addResource(new GenericResource('mvc:Bablo\Controller\Index'));
                    $acl->addResource(new GenericResource('mvc:Bablo\Controller\Index:index'));
                    $acl->addResource(new GenericResource('mvc:Bablo\Controller\Index:dashboard'));
                    $acl->addResource(new GenericResource('mvc:Bablo\Controller\Index:login'));
                    $acl->addResource(new GenericResource('mvc:Rest\Controller\Report'));
                    $acl->addResource(new GenericResource('mvc:Rest\Controller\Validate'));
                    
                    $acl->allow('guest', 'mvc:Bablo\Controller\Index:index');
                    $acl->allow('guest', 'mvc:Bablo\Controller\Index:login');
                    
                    $acl->allow('user', 'mvc:Bablo\Controller\Index');
                    $acl->allow('user', 'mvc:Bablo\Controller\Accounting');
                    $acl->allow('user', 'mvc:Rest\Controller\Report');
                    $acl->allow('user', 'mvc:Rest\Controller\Validate');
                    
                    return $acl;
                },
        ));
    }
}
