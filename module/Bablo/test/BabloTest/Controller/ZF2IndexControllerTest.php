<?php
namespace BabloTest\Controller;

class ZF2IndexControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase {
    
    public function setUp() {
        $this->setApplicationConfig(include '../../../config/application.config.php');
        parent::setUp();
    }
    
    public function testLoginAction() {
        $this->dispatch('/bablo/index/login', 'POST', ['user' => 'a@a.com', 'pass' => '111']);
        
        $this->assertModuleName('Bablo');
        $this->assertControllerName('Bablo\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertActionName('login');
        $this->assertMatchedRouteName('bablo/default');
        print_r($this->getResponse());
        $this->assertResponseStatusCode(302);
        
        
    }
}
