<?php

namespace BabloTest\Controller;

use Bablo\Controller\IndexController;
use Bablo\Form\LoginForm;
use BabloTest\Bootstrap;
use Zend\Http\PhpEnvironment\Response;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Mvc\Router\RouteMatch;

class IndexControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        
        
        
        $this->controller = new IndexController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
        
        $serviceManager->setAllowOverride(true);
        $aservice = new \Zend\Authentication\AuthenticationService(
                           new StorageInterfaceMock(), 
                           new AdapterInterfaceMock()); 
        
        $serviceManager->setService('AuthService', $aservice);
    }
    
    public function testNonAuthIndexAction()
    {
        $this->routeMatch->setParam('action', 'index');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNull($result->user);
        $this->assertNotNull($result->login);
        $this->assertTrue($result->login instanceof LoginForm);
    }
    
    public function testAuthIndexAction()
    {
        $this->routeMatch->setParam('action', 'index');

        $this->controller->getAuthService()->authenticate(new AdapterInterfaceMock());
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
        
        $this->assertTrue($result instanceof Response);
    }
}